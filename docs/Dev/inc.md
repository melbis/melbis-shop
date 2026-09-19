# Library Modules

Library modules are modular scripts belonging to the `inc` group. Unlike regular modules, the parser does not call them and they have no templates. Their purpose is to contain shared functions used by several regular modules at once. This allows repetitive logic to be placed in a single location rather than duplicated across individual modules.

In a library's name, after `inc` comes its own group — `melbis_inc_web_callback.php` belongs to the `web` group. Libraries are organized by this group in the Workbench tree, alongside the modules of their respective project section (see "Accepted Conventions").

To connect a library to a regular module, simply check the box next to it in the IDE's right panel — the parser will load it automatically before running the module, making all its functions available.


## The Namespace and the Short Name

A library, like an ordinary module, can declare a namespace of its own — then its functions lose the prefix, and callers get the right to call them by a short name (see "Modular Scripts"):

```php
<?php
namespace MELBIS_INC_LOGIC;

function OrderCreate($mOrder)
{
    ...
}
```

The second half of the matter is **the short name the library will be called by**. The library declares it itself, and that is why it is the same across the whole store: `LOGIC\OrderCreate` reads the same in any file. The name is written in the parameter field above the editor: for an ordinary module that field holds the declared input parameters, and a library has none, so the field is taken by this instead.

**There is one name.** The engine will not accept a comma-separated list on saving and answers `One alias only`, showing what stands in the field. Equal names on different libraries are not forbidden: they collide only in a file that has included both, and there the author of the file decides.

The field may be left empty — then the library takes no part in short calls, and it is called by the full name of its space. That is a lawful variant and raises no warnings.

Filling in the name without declaring a `namespace` is not possible: on saving the engine warns `Namespace missing`, because in that case there is nothing to import.

A declared name is a recommendation for the whole store rather than a ban. A module may give a library any name in its own file, but on saving it will get `Alias differs` with both variants: the one in the file and the one in the library's manifest. The warning does not stand in the way of saving — it is there so that one library is not called differently in different files without a reason.

Let us look at three characteristic examples from the demonstration store.

## melbis_inc_web_topic — Temporary Tables

This module contains a single function — `MELBIS_INC_WEB_TOPIC_Sub`. Its purpose: to build an in-memory temporary table containing all subsections of a given section, recursively traversing the category tree.

```php
function MELBIS_INC_WEB_TOPIC_Sub($mId)
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

Why is this needed? When a module displays a list of products in a section, it must account not only for products in that section itself, but also for products in all its subsections. The same table will be needed by the attribute filter module. Instead of writing a recursive CTE in each of these modules, it is sufficient to call the library once — and the temporary table is ready for use in subsequent queries:

```php
// In the melbis_store_topic module:
function MELBIS_STORE_TOPIC($mVars)
{
    $id = $mVars['id'];

    // Create a temporary table of subsections
    MELBIS_INC_WEB_TOPIC_Sub($id);

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
              ORDER BY t.absindex, ts.pos
                 LIMIT 100
                ";
    $goods = MELBIS()->SqlSelect(__LINE__, $command);
    ...
}
```

## melbis_inc_web_callback — Template Engine Callbacks

This module registers template engine modifiers. A callback is a PHP function that can be called directly from an HTML template as a variable modifier.

Registration is placed in a wrapper function, with the callback itself defined alongside it:

```php
// In the melbis_inc_web_callback library:
function MELBIS_INC_WEB_CALLBACK()
{
    MELBIS()->DefineCallback('PageLink');
}

function MELBIS_INC_WEB_CALLBACK_PageLink($mVars)
{
    $link = ( $mVars['kind_key'] == 'kLink' ) ? $mVars['link'] : '/?topic_id='.$mVars['id'];

    return $link;
}
```

The function's name is not written in the registration: the engine looks for `PageLink` in the same file the registration line stands in — here that is `MELBIS_INC_WEB_CALLBACK_PageLink`, and in a library with a namespace it would be `MELBIS_INC_WEB_CALLBACK\PageLink`. What counts is the file with the registration, not the module that called the wrapper.

Naming the function explicitly is only needed when its name differs from the modifier's or when it lies in another file:

```php
MELBIS()->DefineCallback('PageLink', MELBIS_INC_WEB_CALLBACK_PageLink(...));
```

The three dots are not a shorthand in the example but PHP syntax: that is how a function is passed as a value without being called, and the name is resolved by the compiler rather than by a string in quotes. The string form (`'MELBIS_INC_WEB_CALLBACK_PageLink'`) works too, but after the library moves into a namespace it points into the void. Either form is checked by the engine at registration, so a non-existent name will not live to reach the storefront.

The wrapper is called by the **top-level module** — in the file body, before its main function:

```php
// In the page router module, e.g. melbis_base_page:
MELBIS_INC_WEB_CALLBACK();

function MELBIS_BASE_PAGE($mVars)
{
    // ...
}
```

A single such call is sufficient for the entire page. The callback registry is shared across the entire request, and each subsequent module receives a copy of it at the moment it is connected — so in the templates of nested modules (`melbis_cataloge`, the product card, and others) the modifier works on its own, and **there is no need to attach the library to them**.

Why in the file body rather than inside the module function: the body executes when the module is connected on every request, whereas the module function is not executed at all when served from cache — and the registration would never take place.

After this, the `$PageLink` modifier becomes available in any template, computing the correct URL for a section based on its type:

```html
{#MENU}
    <a href="{ID|$PageLink:KIND_KEY,LINK}">{NAME|html}</a>
{MENU#}
```

The modifier name is a case-sensitive key. By convention it matches the function's Pascal tail: one name is found by a single search in all three places — in the template, in the registration, and in the declaration. The tag then reads three classes of names at once: uppercase `ID` and `KIND_KEY` are data, lowercase `path` and `webp` are built-in modifiers, and `$PageLink` is a module function.

This is exactly how it is done in the demonstration store: the library is declared only by `melbis_base_page`, while the modifier is used in the templates of `melbis_cataloge` and `melbis_cataloge_sub`, where nothing is checked in the library list.

> **Registration must complete before the module is connected.** A module receives the callbacks that were registered by the time it itself was connected. The parent module always makes it in time: its PHP executes before the template is parsed, and nested modules are connected during parsing. A sibling that registers a callback later, however, will not affect an already-connected module — register higher up the tree, not from the side.

> **The library's tables are available only to the module that declared it.** If a callback reads data from the database, those tables will be added to the cache dependency list only for the top-level module; nested modules will not be aware of them and will not be rebuilt when they change. For callbacks that merely format passed values this does not matter, but a library that reads from the database should also be attached to the modules where it is used.

For more details on modifiers and callback syntax, see the "Modifiers" section.

## melbis_inc_logic — Unified Business Logic

This is the most significant type of library module. `melbis_inc_logic` contains all functions for working with orders: creation, loading, editing, calculation, adding and removing products, discount calculation, and notifications:

```
MELBIS_INC_LOGIC_OrderCreate        — create a new order version
MELBIS_INC_LOGIC_OrderLoad          — load the current version
MELBIS_INC_LOGIC_OrderEdit          — open an order for editing
MELBIS_INC_LOGIC_OrderCalc          — calculate totals
MELBIS_INC_LOGIC_OrderGoodsAdd     — add a product to the order
MELBIS_INC_LOGIC_OrderGoodsRemove  — remove a product from the order
MELBIS_INC_LOGIC_OrderGoodsDiscount — calculate discounts
MELBIS_INC_LOGIC_NotifyEvents       — check system events
```

These functions are called from the shopping cart module on the storefront:

```php
// In the melbis_basket module:
$version = MELBIS()->SessionGetValue('order') ?? MELBIS_INC_LOGIC_OrderCreate();
$version = MELBIS_INC_LOGIC_OrderGoodsAdd($version, $store_id);
$version = MELBIS_INC_LOGIC_OrderCalc(null, $version);
```

The same cart, if the library has declared a namespace and the module has included it with a tick:

```php
// In the melbis_basket module:
namespace MELBIS_BASKET;

use MELBIS_INC_LOGIC as LOGIC;

...
$version = MELBIS()->SessionGetValue('order') ?? LOGIC\OrderCreate();
$version = LOGIC\OrderGoodsAdd($version, $store_id);
$version = LOGIC\OrderCalc(null, $version);
```

One clarification about the setting below: **the function the application calls is stored as a string in the settings registry**. A library moving into a namespace does not carry over to it — the name in the setting has to be corrected by hand to `MELBIS_INC_LOGIC\OrderCalc`.

The key advantage of this approach is **unified business logic for both the website and the desktop application**. The very same functions from `melbis_inc_logic` are called by the Melbis Shop application when a manager works with orders through the Windows client. The configuration of called functions is done in the application via **the "Development → Settings Registry" menu, the "Basic Settings" tab, the "Called Modules" section**. For example, in the "Orders → Calculation" section, the library name and the function name that the application should call to calculate an order are specified.

This way, a customer placing an order through the website and a manager editing it in the application both work through the same PHP code — with no risk of logic divergence or synchronization errors.

## Other Library Examples

In addition to those listed, other libraries are also common in projects on the Melbis platform:

- **`inc_auth`** — client authorization checks, login, logout, session management.
- **`inc_email`** — sending email notifications.
- **`inc_telegram`** — sending messages via the Telegram Bot API.
- **`inc_curl`** — HTTP requests to external APIs.
- **`inc_lang`** — working with language settings in multilingual projects.

Any logic needed in more than one module is worth moving into a library.