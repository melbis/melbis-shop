# Modular Scripts

A modular script is the primary building block of the Melbis platform. Each module solves one isolated task: builds a catalog menu, renders a product card, processes a cart, or executes a background task. The entire site is assembled from these blocks.

## Modules Named `melbis_*` Are Not Edited

The modules that come with the platform — the ones carrying the `melbis_` prefix — are **left as they are** in your store. They must always be fresh and at hand: they are what people check against, what they take as a sample, and they are updated together with the engine.

If logic of your own is needed — even one changed line in a calculation — **your own** module is created under your own prefix:

1. add `<company>_inc_logic.php` (or an ordinary module of your own — the rule is the same);
2. copy the functions you need into it from `melbis_*`;
3. rename them with your prefix and edit them as much as you like;
4. in the template or in the calling module, replace the call with your own.

That is how every module of the delivery is dealt with: both the demonstration store and the ready-made AI tools.

The reason is simple: **an engine update brings new versions of `melbis_*`**. An edit made inside them will either disappear on the update or stop it, and a file that has diverged from the delivery cannot be compared with the reference — so you lose the only way to work out what exactly in your store is done differently from everyone else. A module of your own does not depend on the update at all.

## Working with Modules in the IDE

All modules are displayed in the file tree under **"Scripts, Templates"** in the Workbench. Modular scripts are grouped by company and group — this makes navigation convenient even in large projects with dozens of modules.

When a module is opened in the editor, its interface consists of three parts:

- **Above the editor** — two fields: the module description and the input parameter declaration. This data is used by the IDE for autocompletion when editing templates.
- **Code editor** — the module's PHP code.
- **Right panel** — module parameters: connected libraries, database table list, cache settings, entry point and lazy loading flags.

## PHP File Structure

A typical module looks like this:

```php
<?php
/**
 * Function MELBIS_CATALOGE
 **/
function MELBIS_CATALOGE($mVars)
{
    // Create a template engine pointer
    $tpl = MELBIS()->TplCreate();

    // Retrieve data from the database
    $command = "SELECT id, name
                  FROM {DBNICK}_topic
                 WHERE no_visible = 0
              ORDER BY absindex";
    $menu = MELBIS()->SqlSelect(__LINE__, $command);

    // Pass data to the template engine
    MELBIS()->TplAssign($tpl, 'MENU', $menu);

    // Return the result
    return MELBIS()->TplFinal($tpl, 'main');
}
```

The main function is the only required part of a module. Its name matches the filename in uppercase. The parser calls exactly this function, passing the input parameters as the `$mVars` array.

The module may contain any number of helper functions — they are named with the main function's name as a prefix (see the "Naming Conventions" section).

## Namespaces

The prefix in function names exists for one reason: in PHP all functions live in a common space, and `Price` from the product card would collide with `Price` from the cart. A module can declare a space of its own — then the file name leaves the function names for a single line at the top, and the calls get shorter.

```php
<?php
namespace MELBIS_STORE_CARD;

/**
 * Function Main
 **/
function Main($mVars)
{
    $price = Price($mVars['id']);
    ...
}

/**
 * Function Price
 **/
function Price($mId)
{
    ...
}
```

The main function here is called `Main` rather than the file name: the module name is already written in the `namespace` line, and there is no point repeating it. The parser looks for both forms — first `MELBIS_STORE_CARD\Main`, then the former `MELBIS_STORE_CARD` — so nothing changes in the template, and the module call tag stays the same.

The file's own helper functions are called by their short name with no prefixes at all — `Price($id)`. Calls into the engine work as before as well: PHP looks for unqualified function names first in its own space and then in the global one, so `MELBIS()`, `count()`, and any function of a flat library are visible unchanged.

**Declaring it is voluntary and per-file.** A module without a `namespace` line works exactly as it did before; flat and namespaced modules live in one store quite happily and call each other.

## Calling Library Functions

The functions of an included library that has also declared a space are called through a `use` line under the declaration of your own space:

```php
namespace MELBIS_STORE_CARD;

use MELBIS_INC_LOGIC as LOGIC;

...
$version = LOGIC\OrderCreate($order);
```

The word `as` is optional. Without it the library stays under its full name — which suits the case where it needs no short one, or has not declared one:

```php
use MELBIS_INC_AGENT_TABLE;

...
$rows = MELBIS_INC_AGENT_TABLE\Read($id);
```

**The `use` line is obligatory in a module with a space.** Without it PHP resolves the name `MELBIS_INC_LOGIC\OrderCreate()` relative to the current space — it looks for `MELBIS_STORE_CARD\MELBIS_INC_LOGIC\OrderCreate` — and falls over at the moment of the call. The only way to do without `use` is a leading slash: `\MELBIS_INC_LOGIC\OrderCreate($order)`. In a flat module, on the contrary, `use` is not needed: there a name with a slash is global anyway.

You will not have to write the line out by hand. Type `use` with a space and the Workbench shows the list of every library of the store: the full name of the space, the short name the library declared in brackets, and its description. The chosen entry is inserted as a finished line, together with `as` and the semicolon, and the library **ticks itself in the inclusions panel** at the same time — the code comes first, the manifest follows it. A tick that was already there is left alone, and the Workbench will never untick anything by itself.

The name in `use` is an alias inside one file, not a global rule: a library that has included another library declares its own `use` lines in itself, and the calling module does not need to know about them. Inheriting imports is not required — files are loaded down the tree of inclusions. If the name in a file has diverged from the one the library declared, an `Alias differs` warning appears on saving (see "Library Modules").

**An import includes nothing.** `use` is only a name; a library gets into memory by the tick in the manifest.

## What to Check When Converting a Module

The move affects more than the function declarations. Three things in the file need to be looked over by eye:

- **Class names do not fall back to the global space** — unlike functions. Inside a
  `namespace`, the call `new DateTime(...)` looks for `MELBIS_STORE_CARD\DateTime` and
  does not find it; it has to be written `new \DateTime(...)`. `catch` is especially
  treacherous: `catch (\Exception $e)` without the slash turns into catching a
  non-existent class and simply never fires.
- **Function names written as strings.** Registering a template modifier as
  `MELBIS()->DefineCallback('page_link', 'MELBIS_STORE_CARD_page_link')` points into
  the void after the move. The reliable form is the PHP 8.1 syntax, where the compiler
  resolves the name: `DefineCallback('page_link', page_link(...))`. It survives both a
  rename and a move of the function.
- **Names that live in the settings.** The order handler function in the settings
  registry and the AI tool function in the tool registry are stored as strings in the
  database. The owner corrects those by hand: the code knows nothing about them.

Old calls to your module from other files also need fixing after the move, but the edit is mechanical — the underscore before the function name becomes a backslash:

```php
MELBIS_STORE_CARD_Price($id);      // before
MELBIS_STORE_CARD\Price($id);      // after
```

## Module Workflow

Most modules follow the same pattern:

**1. Create a template engine pointer:**
```php
$tpl = MELBIS()->TplCreate();
```

**2. Retrieve data from the database** using `MELBIS()->Sql*` methods:
```php
// Return a flat array of a single record
$topic = MELBIS()->SqlSelectFlat(__LINE__, $command, $params);

// Return an array of records
$menu = MELBIS()->SqlSelect(__LINE__, $command, $params);

// Return an array indexed by key
$image = MELBIS()->SqlSelectEnumFlat(__LINE__, $command, 'id', $id, $params);

// Return a page of records and the total row count
$goods = MELBIS()->SqlSelectLimit(__LINE__, $command, $offset, $limit, $params);
```

Pagination, data writing, transactions, and table locking are described in the "Working with the Database" section.

**3. Pass data to the template engine** using `MELBIS()->Tpl*` methods:
```php
// Pass a single value
MELBIS()->TplAssign($tpl, 'TITLE', $topic['name']);

// Pass an entire array
MELBIS()->TplAssign($tpl, $topic);

// Parse a template and place the result into a variable
MELBIS()->TplParse($tpl, 'CONTENT', 'page_index');
```

**4. Return the result** — the final parsing of the main template:
```php
return MELBIS()->TplFinal($tpl, 'main');
```

The full list of `Sql*` and `Tpl*` methods is described in the corresponding documentation sections.

## Input Parameters

Parameters are passed to the module through the `$mVars` array. How the array is formed depends on how the module was called.

**If the module is called as an entry point** (via `MELBIS()->Run()`), parameters are passed explicitly from the root script:

```php
// In index.php:
$entry_param = [serialize($_GET), serialize($_POST)];
MELBIS()->Run('melbis_base_page', $entry_param);
```

The module declares `get: serial, post: serial` — and inside the function:

```php
$id = (int) ( $mVars['get']['topic_id'] ?? 0 );
```

**If the module is called from a template**, parameters are passed directly in the call tag — positionally, separated by commas:

```html
{MELBIS:melbis_store_image([ID],kDefault)}
{MELBIS:melbis_store_random(8)}
{MELBIS:melbis_cataloge_sub(,,[ID])}
```

**Variable values are substituted in square brackets — `[ID]`, not `{ID}`.** Arguments are parsed by comma and by the closing parenthesis, so any "dirty" value — a name containing a comma, text with a quote, a serialized array — when substituted via `{ID}` will break the argument list and the module will receive garbage. Square brackets run the value through `urlencode`, and the parser on the receiving end performs a matching `urldecode`, so the content arrives intact in any form. This rule also applies to variables of the current loop row:

```html
{#GOODS}
    {MELBIS:melbis_store_card([VAR:LANG],[ID],[PRIOR])}
{GOODS#}
```

Literals (`kDefault`, `8`) are written as-is — they are not variables, there is nothing to encode.

The module must declare the corresponding parameters, for example `id: int, key: str`, and inside the function:

```php
$id  = $mVars['id'];
$key = $mVars['key'];
```

Parameter types are declared in the field above the editor in the IDE:

| Type | Description |
|---|---|
| `int` | Integer |
| `float` | Floating-point number |
| `bool` | Boolean value |
| `str` | String |
| `fix` | Fixed set of values; the first is the default. Example: `mode: fix=list\|grid` |
| `serial` | Serialized PHP array (for passing `$_GET`/`$_POST`). If the value is empty or invalid, `[]` is returned — no need to check `is_array` in the module |

## Ways to Call a Module

**From a template** — the primary and safe method. The parser encounters a `{MELBIS:module_name(...)}` tag in an HTML template, runs the module, and substitutes its HTML result in place of the tag. Available for any module without restrictions.

**As an entry point** — a call via `MELBIS()->Run()` in the root script. The URL can be anything — the key criterion is the call via `Run`. To allow such a call, the **"Entry Point Module"** option must be enabled in the IDE's right panel. Without this flag, the parser will refuse to run the module directly. Entry points are typically page router modules, form handlers, and cron tasks.

## Module Nesting

The HTML fragment returned by a module may contain call tags for other modules — to any depth: the parser processes the entire chain on its own. A breakdown using the demo store page as an example is provided in the "Storefront Architecture" section.

## Libraries

The right panel of the IDE displays all available library modules (`inc`). To include a library it is enough to tick the checkbox — the parser loads it automatically before running the current module, and all of its functions become available. A library dragged in by another library is shown greyed out: it is included already, and there is no point in ticking it.

The developer does not maintain the module's list of tables — the engine assembles it itself, from the queries the module has run. The **"Tables"** tab shows the finished list, and the only action on it is to untick a table the cache must not depend on. How that works and when the list fills out is in the "Caching" section.

## Utility Methods

Besides working with templates and the database, a module now and then needs the platform's utility calls — the name of the current module, calling a function of its own, the path to an uploaded file. They are collected in the "Utility Methods" section.
