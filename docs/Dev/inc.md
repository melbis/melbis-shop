# Library Modules

Library modules are modular scripts belonging to the `inc` group. Unlike regular modules, the parser does not call them and they have no templates. Their purpose is to contain shared functions used by several regular modules at once. This allows repetitive logic to be placed in a single location rather than duplicated across individual modules.

In a library's name, after `inc` comes its own group — `melbis_inc_web_callback.php` belongs to the `web` group. Libraries are organized by this group in the Workbench tree, alongside the modules of their respective project section (see "Accepted Conventions").

To connect a library to a regular module, simply check the box next to it in the IDE's right panel — the parser will load it automatically before running the module, making all its functions available.


## The Namespace and the Short Name

A library, like an ordinary module, declares a namespace of its own — the name of its file in upper case — and gives its functions short names, without prefixes (see "Modular Scripts"):

```php
<?php
namespace MELBIS_INC_LOGIC_ORDER;

function Create()
{
    ...
}
```

The second half of the matter is **the short name the library will be called by**. The library declares it itself, and that is why it is the same across the whole store: `LOGIC_ORDER\Create` reads the same in any file. The name is written in the parameter field above the editor: for an ordinary module that field holds the declared input parameters, and a library has none, so the field is taken by this instead.

**There is one name.** The engine will not accept a comma-separated list on saving and answers `One alias only`, showing what stands in the field. Equal names on different libraries are not forbidden: they collide only in a file that has included both, and there the author of the file decides.

The field may be left empty — then the library takes no part in short calls, and it is called by the full name of its space. That is a lawful variant and raises no warnings.

Filling in the name without declaring a `namespace` is not possible: on saving the engine warns `Namespace missing`, because in that case there is nothing to import.

A declared name is a recommendation for the whole store rather than a ban. A module may give a library any name in its own file, but on saving it will get `Alias differs` with both variants: the one in the file and the one in the library's manifest. The warning does not stand in the way of saving — it is there so that one library is not called differently in different files without a reason.

A library that a module includes but does not call is declared with a `use` line with the full name and without `as`. Such a library works by the inclusion itself — for example, the code in the body of its file registers callbacks for nested modules. Without this line the engine warns `Unused` on saving: the library is included, but not one of its functions is called in the module.

Let us look at three characteristic examples from the demonstration store.

## melbis_inc_web_topic — Temporary Tables

The short name of this library is `TOPIC`. Its function `Sub` builds an in-memory temporary table containing all subsections of a given section, recursively traversing the category tree. The second function, `Menu`, returns the sections of one menu — it is called by `melbis_cataloge` and `melbis_cataloge_sub`.

```php
namespace MELBIS_INC_WEB_TOPIC;

function Sub($mId)
{
    $command = "CREATE TEMPORARY TABLE {DBNICK}_topic_sub ENGINE=MEMORY
                WITH RECURSIVE topic_sub AS (
                    SELECT t.tindex, t.id
                      FROM {DBNICK}_topic t
                     WHERE t.id = :ID
                     UNION ALL
                    SELECT ts.tindex, t.id
                      FROM topic_sub ts
                      JOIN {DBNICK}_topic t ON ts.id = t.tindex
                )
                SELECT * FROM topic_sub";

    $param = [
        'id' => $mId
        ];
    MELBIS()->SqlQuery(__LINE__, $command, $param);
}
```

Why is this needed? When a module displays a list of products in a section, it must account not only for products in that section itself, but also for products in all its subsections. The same table will be needed by the attribute filter module. Instead of writing a recursive CTE in each of these modules, it is sufficient to call `TOPIC\Sub` from the library once — and the temporary table is ready for use in subsequent queries:

```php
// In the melbis_page_topic module:
namespace MELBIS_PAGE_TOPIC;

use MELBIS_INC_WEB_TOPIC as TOPIC;

function Main($mVars)
{
    $id = $mVars['id'];

    // Create a temporary table of subsections
    TOPIC\Sub($id);

    // Now we can JOIN with {DBNICK}_topic_sub
    $command = "SELECT s.id
                  FROM {DBNICK}_topic_sub t_sub
                  JOIN {DBNICK}_topic t
                    ON t_sub.id = t.id
                  JOIN {DBNICK}_topic_store ts
                    ON ts.topic_id = t.id
                  JOIN {DBNICK}_store s
                    ON ts.store_id = s.id
                 WHERE s.no_visible = 0
              GROUP BY s.id
              ORDER BY MIN(t.absindex), MIN(ts.pos)
                 LIMIT 100
                ";
    $goods = MELBIS()->SqlSelect(__LINE__, $command);
    ...
}
```

## melbis_inc_web_callback — Template Engine Callbacks

This module registers template engine modifiers. A callback is a PHP function that can be called directly from an HTML template as a variable modifier.

Registration is placed in the function `Define`, with the callbacks defined alongside it:

```php
// In the melbis_inc_web_callback library:
namespace MELBIS_INC_WEB_CALLBACK;

function Define()
{
    MELBIS()->DefineCallback('TopicLink');
    MELBIS()->DefineCallback('StoreLink');
    MELBIS()->DefineCallback('StatusName');
}

function TopicLink($mVars)
{
    $link = ( $mVars['kind_key'] == 'kLink' ) ? $mVars['link'] : '/?topic_id='.$mVars['id'];

    return $link;
}
```

The function's name is not written in the registration: the engine looks for `TopicLink` in the same file the registration line stands in — here that is `MELBIS_INC_WEB_CALLBACK\TopicLink`, and in a library of the former form, without a namespace, it would be `MELBIS_INC_WEB_CALLBACK_TopicLink`. What counts is the file with the registration, not the module that called `Define`.

Naming the function explicitly is only needed when its name differs from the modifier's or when it lies in another file:

```php
MELBIS()->DefineCallback('TopicLink', CALLBACK\TopicLink(...));
```

The three dots are not a shorthand in the example but PHP syntax: that is how a function is passed as a value without being called, and the name is resolved by the compiler together with the `use` line rather than by a string in quotes. The string form (`'MELBIS_INC_WEB_CALLBACK\TopicLink'`) works too, but it does not know the short name from `use`: a name in quotes is written in full, and when the function moves it stays as it was. Either form is checked by the engine at registration, so a non-existent name will not live to reach the storefront.

`Define` is called by the **top-level module** — in the file body, before its main function:

```php
// In the page router module melbis_base_page:
namespace MELBIS_BASE_PAGE;

use MELBIS_INC_WEB_CALLBACK as CALLBACK;

CALLBACK\Define();

function Main($mVars)
{
    // ...
}
```

A single such call is sufficient for the entire page. The callback registry is shared across the entire request, and each subsequent module receives a copy of it at the moment it is connected — so in the templates of nested modules (`melbis_cataloge`, the product card, and others) the modifier works on its own, and **there is no need to attach the library to them**.

Why in the file body rather than inside the module function: the body executes when the module is connected on every request, whereas the module function is not executed at all when served from cache — and the registration would never take place.

After this, the `$TopicLink` modifier becomes available in any template, computing the correct URL for a section based on its type:

```html
{#MENU}
    <a href="{ID|$TopicLink:KIND_KEY,LINK}">{NAME|html}</a>
{MENU#}
```

The modifier name is a case-sensitive key. By convention it matches the function's name: one name is found by a single search in all three places — in the template, in the registration, and in the declaration. The tag then reads three classes of names at once: uppercase `ID` and `KIND_KEY` are data, lowercase `path` and `webp` are built-in modifiers, and `$TopicLink` is a module function.

This is exactly how it is done in the demonstration store: the `$TopicLink` modifier stands in the `melbis_cataloge` template, while the module itself does not include the callback library — it was declared by `melbis_base_page`. The exception is `melbis_cataloge_sub`: it is an entry point module, it is also called by a separate request, without a page around it, so it includes the library and calls `CALLBACK\Define()` itself.

> **Registration must complete before the module is connected.** A module receives the callbacks that were registered by the time it itself was connected. The parent module always makes it in time: its PHP executes before the template is parsed, and nested modules are connected during parsing. A sibling that registers a callback later, however, will not affect an already-connected module — register higher up the tree, not from the side.

> **A callback that reads the database.** The tables a callback reads through the engine's doors go into the cache dependencies of the module in whose template it fired. What reading is acceptable for a callback — "Modifiers" → "The Callback and the Database".

For more details on modifiers and callback syntax, see the "Modifiers" section.

## melbis_inc_logic_* — Unified Business Logic

This is the most significant type of library module. In the demonstration store, the work with an order is spread across the libraries of the `logic` group: creating and loading an order, adding and removing products, discounts, calculation, writing, notifications:

| Library | Short name | Functions |
|---|---|---|
| `melbis_inc_logic_order` | `LOGIC_ORDER` | order version: `Create` — a new one, `Load` — the current one, `GoodsAdd` and `GoodsRemove` — add and remove a product, `GoodsSum` — the products total, `GoodsDiscount` — a product discount, `OptionSet` — an order option, and others |
| `melbis_inc_logic_order_calc` | `LOGIC_ORDER_CALC` | `Run` — calculate an order version |
| `melbis_inc_logic_order_edit` | `LOGIC_ORDER_EDIT` | `Run` — write an order version |
| `melbis_inc_logic_notify` | `LOGIC_NOTIFY` | `Run` — the events the application's "Dispatcher" waits for |
| `melbis_inc_logic_common` | `LOGIC_COMMON` | `Rate` — the currency rate, `Price` — a sum in the store currency |

These functions are called from the shopping cart module on the storefront:

```php
// In the melbis_basket module:
namespace MELBIS_BASKET;

use MELBIS_INC_LOGIC_ORDER as LOGIC_ORDER;
use MELBIS_INC_LOGIC_ORDER_CALC as LOGIC_ORDER_CALC;

...
$version = MELBIS()->SessionGetValue('order') ?? LOGIC_ORDER\Create();
$version = LOGIC_ORDER\GoodsAdd($version, $store_id);
$version = LOGIC_ORDER_CALC\Run(null, $version);
```

The key advantage of this approach is **unified business logic for both the website and the desktop application**. The very same functions of the `logic` libraries are called by the Melbis Shop application when a manager works with orders through the Windows client. The configuration of called functions is done in the application via **the "Development → Settings Registry" menu, the "Basic Settings" tab, the "Called Modules" section**. For example, in the "Orders → Calculation" section, the library `melbis_inc_logic_order_calc.php` and the function that the application should call to calculate an order — `MELBIS_INC_LOGIC_ORDER_CALC\Run` — are specified. **The function name is stored as a string** and is written in full, with the namespace: the code knows nothing about it, and when the function moves it is corrected by hand.

This way, a customer placing an order through the website and a manager editing it in the application both work through the same PHP code — with no risk of logic divergence or synchronization errors.

## Other Library Examples

In addition to those listed, other libraries are also common in projects on the Melbis platform:

- **`inc_auth`** — client authorization checks, login, logout, session management.
- **`inc_email`** — sending email notifications.
- **`inc_telegram`** — sending messages via the Telegram Bot API.
- **`inc_curl`** — HTTP requests to external APIs.
- **`inc_lang`** — working with language settings in multilingual projects.

Any logic needed in more than one module is worth moving into a library.