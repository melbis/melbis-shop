# Naming Conventions

To improve code readability and ensure compatibility between modules from different developers, we strongly recommend following these naming conventions.

## File Names

**Root scripts and HTML templates** — lowercase, words separated by underscores:

```
index.php
page_catalog.php
main.htm
page_404.htm
item_product.htm
```

**Module scripts** follow a three-part naming scheme, separated by underscores:

```
{company}_{group}_{purpose}.php
```

- **Company** — your company name or abbreviation. Modules shipped with the platform use `melbis`. For your own projects, choose a unique identifier: it will distinguish your modules from the built-in ones and from third-party modules if you integrate external code.
- **Group** — the logical group the module belongs to. Groups related modules together and helps navigate large projects.
- **Purpose** — one or more words describing the module's task.

Examples:

```
melbis_base_page.php       — base page router
melbis_store_card.php      — product card
melbis_cataloge.php        — catalog
melbis_inc_logic_order.php — order handling library
melbis_block_slider.php    — slider
melbis_client_auth.php     — client authentication
```

Library names have four parts: the word `inc` takes the place of the group, and the library's own group moves to the next part of the name — more on this below, in the "Module Groups" section.

In addition to the code itself, each module has parameters — cache, input data, table list, connected libraries. These are configured in the "Workbench" window, in the right panel next to the code editor, and are stored together with the module. This is described in more detail in the "Module Scripts" section.

## Module Groups

The group in a module's name carries semantic meaning and determines how the module is displayed in the "Workbench" tree. Only one group name is reserved — **`inc`** (also allowed: `include` or an empty value). Modules with this group are libraries: they cannot have HTML templates, and the parser does not call them directly — their functions are used by other modules.

All other group names (`base`, `store`, `cataloge`, `basket`, `client`, `block`, `cron`, and any others) are defined by you based on your project's architecture. A group is simply a way to organize modules in the file tree.

```
melbis_base_page.php       — regular module (group base)
melbis_store_card.php      — regular module (group store)
melbis_block_slider.php    — regular module (group block)
```

### Library Groups

For libraries, the word `inc` takes the place of the group, so the library's own group moves to the next part of the name:

```
{company}_inc_{group}_{purpose}.php
```

In the "Workbench" tree, libraries are organized by this group — alongside regular modules at the same level, but with their own icon. There is no longer a shared `inc` folder where all project libraries would be dumped:

```
melbis
    auth          melbis_inc_auth.php
    logic         melbis_inc_logic_common.php
                  melbis_inc_logic_notify.php
                  melbis_inc_logic_order.php
                  melbis_inc_logic_order_calc.php
                  melbis_inc_logic_order_edit.php
    web           melbis_inc_web_callback.php
                  melbis_inc_web_topic.php
    base          melbis_base_footer.php
                  melbis_base_head.php
                  melbis_base_header.php
                  melbis_base_page.php
    basket        melbis_basket.php
    cataloge      melbis_cataloge.php
                  melbis_cataloge_sub.php
```

The purpose can be omitted if there is only one library in the group: `melbis_inc_auth.php` represents group `auth` with no purpose, while `melbis_inc_web_callback.php` represents group `web` with purpose `callback`.

The idea is to keep a library close to the modules that use it: `melbis_inc_web_callback` and `melbis_inc_web_topic` belong to the web part of the project and are grouped under `web`, rather than being scattered among all other libraries.

## Functions in Module Scripts

A module declares its own **namespace** — the name of its file in uppercase — and calls its **main function** `Main`. This is the function the parser calls:

```php
// File: melbis_store_card.php
namespace MELBIS_STORE_CARD;

function Main($mVars)
{
    ...
}
```

The exception is library modules in the `inc` group. The parser does not call them, so they have no `Main` function. Instead, they contain a set of functions that are explicitly called from other modules.

Helper functions of a module have short names without prefixes, and the name is written in **PascalCase** — every word capitalized, no underscores:

```php
function Price($mTpl, $mId)
{
    ...
}

function Features($mTpl, $mId)
{
    ...
}
```

The module name stands in the `namespace` line, and the full name of the function is `MELBIS_STORE_CARD\Price`. The boundary between the module and the function here is a backslash, a language construct: it is visible both to the eye and to tools — the IDE and the linter split the name without consulting the module tree, and a search for `Price` finds the declaration as well as the calls. The entry point is told apart from the helper functions by the name `Main` itself, reserved for it.

Abbreviations are written as words: `SmsUrl`, `XmlLoad` — not `SMSUrl`.

Functions of **library modules** (`inc`) are named the same way — in the namespace of their library:

```php
// File: melbis_inc_logic_order.php
namespace MELBIS_INC_LOGIC_ORDER;

function Create(...)    { ... }
function Load(...)      { ... }
function GoodsAdd(...)  { ... }
```

A calling module calls them through the library's short name — `LOGIC_ORDER\GoodsAdd(...)` (see "Library Modules").

## Names in a Module without a Namespace

Modules of past years declare no namespace, and all their functions lie in PHP's common space. That is why the names in them carry a prefix: the main function is named after the file in uppercase, the helpers — the same name with a Pascal tail:

```php
// File: melbis_store_card.php
function MELBIS_STORE_CARD($mVars)                { ... }
function MELBIS_STORE_CARD_Price($mTpl, $mId)     { ... }
function MELBIS_STORE_CARD_Features($mTpl, $mId)  { ... }
```

Here the change of case marks the name boundary: to the left of it is the module, to the right the function. Lowercase tails (`MELBIS_STORE_CARD_price`) are allowed too: PHP does not distinguish case in function names.

The parser looks for both forms of the main function, so such modules keep working. When a module is converted to a namespace, the underscore before the tail becomes a backslash — `MELBIS_STORE_CARD_Price` turns into `MELBIS_STORE_CARD\Price`. What else to check during the conversion is described in the "Modular Scripts" section.

## Variables in PHP Scripts

| Variable type | Rule | Example |
|---|---|---|
| Function input parameters | Start with `$m`, followed by camelCase | `$mVars`, `$mTopicId`, `$mTpl` |
| Local variables | Lowercase, words separated by `_` | `$id`, `$item_count`, `$command` |
| PHP constants | Uppercase, words separated by `_` | `MELBIS_CACHE`, `MELBIS_LANG` |

> In a module's main function, the input parameter array is conventionally named `$mVars`.

## Table Aliases in SQL

A table alias is an **abbreviation made of the first letters of the words in its name** (the `{DBNICK}_` prefix does not count): `{DBNICK}_user_task` → `ut`, `{DBNICK}_user_group` → `ug`, `{DBNICK}_user` → `u`. If a table appears in the query more than once, or a role marker is needed, a suffix is added to the abbreviation through an underscore: `u_author`, `u_exec`, `ug_main`.

The rule is not cosmetic: the field hints in the Workbench are built on it. The IDE reduces a typed `ut.` or `u_author.` to the tables that fit the abbreviation and shows their fields.

```php
$command = "SELECT ut.id, ut.name, ut.state_key,
                   u_author.login AS author, u_exec.login AS executor
              FROM {DBNICK}_user_task ut
         LEFT JOIN {DBNICK}_user u_author
                ON u_author.id = ut.user_id
         LEFT JOIN {DBNICK}_user u_exec
                ON u_exec.id = ut.exec_id
             WHERE ut.state_key <> 'kClose'
           ";
```

Values are not glued into the query text: the SQL holds a `:NAME` placeholder in uppercase, and the value goes into the parameter array — see the "Working with the Database" section for details.

## Keys in HTML Templates

All template engine keys are written in **uppercase**, with words separated by underscores:

```html
{TITLE}
{PAGE_NAME}
{PRODUCT:PRICE_OLD}
{#ITEMS}
    <a href="?id={ID}">{NAME|html}</a>
{ITEMS#}
```