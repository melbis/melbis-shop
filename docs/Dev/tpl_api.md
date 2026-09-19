# Template Engine Methods

The Melbis platform uses an MVC architecture: data handling logic is concentrated in the module's PHP code, while HTML generation is handled in templates. Although PHP allows you to assemble all the HTML directly in the module using simple string substitution, we strongly recommend against doing so. The Melbis template engine is equipped with a powerful toolkit: loops, conditions, modifiers, callbacks, nesting — all of this frees the module from display logic and makes the code cleaner and more maintainable.

Where `.htm` files are located, what a template group is, and how to select one for a specific visitor — see the "Template Groups" section.

## Template Methods

All interactions between a module and the template engine occur through the `MELBIS()->Tpl*` methods:

**`TplCreate()`** — creates a new template engine context and returns its pointer. Called at the beginning of each module:

```php
$tpl = MELBIS()->TplCreate();
```

**`TplAssign($tpl, $vars, $value = '')`** — passes data into the context, **completely overwriting** whatever was stored under that name before: both scalars and arrays, regardless of what is being written now. This is a simple assignment — no merging.

Called in two forms — a name + value pair, or a single array of pairs:

```php
// Name + value
MELBIS()->TplAssign($tpl, 'TITLE', 'Product Catalog');

// Nested array — accessible in the template as {PAGE:TITLE}, {PAGE:DIR}, etc.
MELBIS()->TplAssign($tpl, 'PAGE', $page);

// Array of pairs: each key becomes a separate variable
MELBIS()->TplAssign($tpl, $product);
MELBIS()->TplAssign($tpl, ['TITLE' => 'Catalog', 'GOODS' => $goods]);
```

When passing an array of pairs, the third argument is not used.

**Keys are converted to uppercase** — both the variable name itself and all string keys within the passed array, at any depth. In PHP you can write however you like: `TplAssign($tpl, 'page', $page)` and `TplAssign($tpl, 'PAGE', $page)` are the same thing, and in the template the key is always written in UPPERCASE. Numeric list keys remain as-is.

**`TplAssignRaw($tpl, $vars, $value = '')`** and **`TplAppendRaw($tpl, $var, $value = '', $replace = false)`** — the same methods, but the keys of the passed array stay exactly as the module wrote them. This is for data, not for names: an array that will go to the page whole through `{DATA|js}` or `{DATA|json}`, where the front end expects `userName` rather than `USERNAME`. The name of the variable itself is raised as usual, and everything else is no different: the same merging in `Append`, the same calling forms, the same path through `:`.

```php
MELBIS()->TplAssignRaw($tpl, 'DATA', ['userName' => $name, 'basket' => $basket]);
```
```html
var DATA = {DATA|js};   // {"userName":"Ivan","basket":{...}}
```

A raw variable is read in the template as usual — `{DATA:USERNAME}` finds `userName`, the index raises the case while searching by itself. Only three modifiers behave differently, the ones that reach inside an array by name: `array_item`, `calc` and `webp` look for the key by an exact match, so on a raw variable the path is written as it is in the data — `{DATA|array_item:userName}`.

A variable stays raw as long as everyone who writes into it writes through `Raw`: an ordinary `TplAppend` on top of it will raise the case of what it appended, and the one array will end up holding keys of two kinds.

**`TplAppend($tpl, $var, $value = '', $replace = false)`** — appends a value to an existing variable without overwriting it entirely. The behavior strictly depends on the type of `$value`, and these are two fundamentally different modes:

* **Scalar** (string, number) — always **concatenated as text** using the `.` operator (the same as PHP's `$var .= $value`). This applies to numbers as well: if a variable holds `5` and you call `TplAppend($tpl, 'COUNT', 3)`, the result will be the string `"53"`, not the number `8` — the `.` operator doesn't add, it only concatenates text. For numeric accumulation, calculate the sum in PHP and write it using `TplAssign`.
* **Array** — merged with the existing array **by key**, not by position. By default (`$replace = false`) — deep merge at any nesting depth: matching associative sub-keys are preserved at each level, and matching numeric lists (e.g., galleries) are not corrupted positionally but **appended to the end**. With `$replace = true` — single-level merge: a matching key is replaced entirely with the new value, including everything inside it (lists in this mode are not appended but replaced). String concatenation never occurs inside arrays in either mode — it only applies to scalars.

> **If the types don't match, the old value is lost.** Appending an array to a location holding a scalar — the scalar is discarded and merging starts from an empty array. Appending a scalar to a location holding an array — the array is discarded and only the new string remains in the variable. No error will be raised, so make sure `Append` receives the same type as what is already stored in the variable.

The calling forms are the same as for `TplAssign`, with one exception: **when passing an array of pairs, the role of `$replace` is played by the second argument**, not the fourth.

```php
// Name + value
MELBIS()->TplAppend($tpl, 'TAGS', $extra_tags);

// Array of pairs: second argument is $replace
MELBIS()->TplAppend($tpl, ['TAGS' => $extra_tags, 'FILTERS' => $extra_filters]);
MELBIS()->TplAppend($tpl, ['SETTINGS' => $extra_settings], true); // single-level replacement instead of merging
```

**`TplParse($tpl, $var, $file)`** — loads a `.htm` template by filename, parses it with the current context data, and places the result in a variable. The variable name can be a path: `TplParse($tpl, 'PAGE:CONTENT', 'page_index')` will place the parsed template inside `PAGE`, creating any missing levels. This is the method modules use to assemble the various parts of their HTML:

```php
// Parse page_index.htm and write the result to the CONTENT variable
MELBIS()->TplParse($tpl, 'CONTENT', 'page_index');

// Additional templates
MELBIS()->TplParse($tpl, 'WINDOWS', 'windows');
MELBIS()->TplParse($tpl, 'SCRIPTS', 'scripts');
```

**Accumulating results.** Normally `TplParse` writes the result to a variable, overwriting the previous value. A dot in the filename changes this behavior to appending:

```php
// Normal: result replaces the contents of CONTENT
MELBIS()->TplParse($tpl, 'CONTENT', 'page_index');

// '.name' — result is appended to the END of what is already in CONTENT
MELBIS()->TplParse($tpl, 'CONTENT', '.page_extra');

// 'name.' — result is prepended to the BEGINNING
MELBIS()->TplParse($tpl, 'CONTENT', 'page_notice.');
```

Lists are not assembled this way: the whole list is passed to the template engine with a single `TplAssign`, and the iteration is done by the `{#...}` loop in the template (see "Template Syntax"). Appending is useful for something else: assembling a variable from several different templates, adding a block conditionally, prepending a notice before the main content.

**`TplFinal($tpl, $file, $postProdName = false)`** — the final step: parses the specified template (typically `main`) with the current context state (including variables already populated by `TplParse`) and returns the finished HTML string. The result is returned from the module's main function:

```php
return MELBIS()->TplFinal($tpl, 'main');
```

The third argument is the name of a **post-processing function** through which the module's finished HTML is passed before being returned. The function is registered in advance, typically in a library module:

```php
// Registration: name → function
MELBIS()->DefinePostProd('minify', MELBIS_INC_BASE_Html(...));
```
The three dots are PHP syntax: the function is passed as a value without being called. The name is resolved by the compiler, so the registration survives both a rename and the move of the library into a namespace; a string in quotes is accepted as well.

```php
// Applied in any module
return MELBIS()->TplFinal($tpl, 'main', 'minify');
```

The function receives the finished HTML string as its only argument and must return a string. This is the place for cross-cutting markup transformations: HTML minification, appending version hashes to static files, adding `loading="lazy"` to images. If no function with the specified name is registered, parsing stops with an error.

Importantly, post-processing is executed **before** the module result is cached — meaning once per compilation, not on every request. Visitor-specific personal data must not be substituted this way; for that, there is a global array (see "Global Array").

**`TplFree($tpl, $var)`** — an alternative final step: returns the value of an already-prepared context variable and **destroys the context itself**. This is `TplFetch` and memory release in a single call:

```php
// The module assembled everything into CONTENT and doesn't need a main template
MELBIS()->TplParse($tpl, 'CONTENT', 'page_index');

return MELBIS()->TplFree($tpl, 'CONTENT');
```

The difference from `TplFinal` is that `TplFinal` parses the specified `.htm` file with the current data, while `TplFree` does no parsing — it simply returns what has already been accumulated. After the call, the `$tpl` pointer is invalid: the context can no longer be accessed. The variable name must exist — like `TplFetch` without a default value, the method stops parsing with an error if the name is unknown.

**`TplParseStr($tpl, $var, $str, $vars = [])`** — the same as `TplParse`, but the template is taken from a passed string rather than a file. Needed when markup comes from the database: emails, SMS templates, agreement texts with substitutions that are edited by a manager.

```php
$template = $letter['body'];    // '<p>Hello, {NAME}! Order {ORDER_ID} has been accepted.</p>'
$vars = [
    'NAME'      => $client['name'],
    'ORDER_ID'  => $order_id
    ];
MELBIS()->TplParseStr($tpl, 'LETTER', $template, $vars);
```

The fourth argument is an optional set of data; it is simply passed into the context before parsing, as if you had called `TplAssign`. The string has access to all template engine features: loops, conditions, modifiers.

**`TplParseVar($tpl, $var, $varTemplate)`** — parses the value of **another context variable** as a template and places the result in `$var`. Used when text containing tags has already entered the context earlier and needs to be parsed in a second pass. The dot in the filename works the same way as in `TplParse`. Both the destination and the source can be paths, but they follow different rules: missing levels are created for the destination, while the source is read-only — if the path does not exist, an empty string is placed in the destination.

**`TplFetchAll($tpl)`** — returns all context variables as a single array. A debugging tool: to see what has actually accumulated in the template engine at the time of the call. In production code, use `TplFetch` to retrieve a specific value instead.

**`TplFetch($tpl, $var, $default = null)`** — reads a variable's value back on the PHP side. Also accepts a path: `TplFetch($tpl, 'PAGE:DIR')` retrieves a nested element without creating anything. If the variable or path does not exist: without the third argument — a parse error (protection against typos in the name); if the third argument is passed (even `null`) — it is returned without an error. These are different calls, not the same call with a "softer" default:

```php
MELBIS()->TplFetch($tpl, 'DEBUG_INFO');          // variable doesn't exist — will throw an error
MELBIS()->TplFetch($tpl, 'DEBUG_INFO', null);    // variable doesn't exist — returns null, no error
MELBIS()->TplFetch($tpl, 'DEBUG_INFO', []);      // variable doesn't exist — returns []
```

**`TplClear($tpl, $var)`** — removes a variable (or several) from the context, as if it had never been passed via `TplAssign`. Accepts either a single name or an array of names, and any of them can be a path: `TplClear($tpl, 'PAGE:DIR')` will remove a single nested key, leaving the rest of `PAGE` untouched. A non-existent path is simply ignored.

```php
// Remove a single variable
MELBIS()->TplClear($tpl, 'DEBUG_INFO');

// Remove several at once
MELBIS()->TplClear($tpl, ['DEBUG_INFO', 'RAW_VARS']);
```

## Addressing by Nested Path

**Wherever a method accepts a variable name, you can pass a path using `:` instead of a simple name** — using the same notation as for reading in templates (`{PAGE:DIR}`). The method then addresses not the entire variable but one specific nested element, leaving its neighbors untouched.

Methods differ only in whether they create missing path levels:

| Method | Creates missing levels |
|---|---|
| `TplAssign`, `TplAppend`, `TplAssignRaw`, `TplAppendRaw` | yes |
| `TplParse`, `TplParseStr` | yes |
| `TplParseVar` | destination — yes, source — no |
| `TplFetch`, `TplClear` | no |

The rule is simple: **write methods create the path, read methods do not.** Therefore, `TplFetch` on a non-existent path will produce a parse error (or return the default value), while `TplClear` will simply do nothing — neither will create anything or cause any damage.

```php
// Instead of "read all of PAGE, fix DIR in PHP, write PAGE back in full"
MELBIS()->TplAssign($tpl, 'PAGE:DIR', '/catalog/');
// only PAGE:DIR is affected — the other keys of PAGE remain as they were
```

Paths work in all calling forms, including **as a key inside an array of pairs** — this is convenient when you need to distribute several values across different branches in a single call:

```php
MELBIS()->TplAssign($tpl, [
    'PAGE:DIR'   => '/catalog/',
    'PAGE:TITLE' => 'Catalog',
    'USER:NAME'  => $client_name
    ]);
```

**Case does not matter.** The path is converted to uppercase in its entirety, so `'page:dir'` and `'PAGE:DIR'` are the same thing.

**Numeric segments** address a list element: `TplAssign($tpl, 'GALLERY:0:TITLE', 'Cover')` changes the title of the first gallery image without overwriting the others.

**Intermediate levels are created automatically.** If `PAGE` does not yet exist, `TplAssign($tpl, 'PAGE:DIR', '/')` will create both `PAGE` and the key inside it. This applies only to `Assign` and `Append`; `Fetch` and `Clear` create nothing — for a missing path, `Fetch` will produce a parse error (or return the default value if one is provided), while `Clear` will simply do nothing.

> **An intermediate scalar along the path will be destroyed.** If `PAGE` held a string, then `TplAssign($tpl, 'PAGE:DIR', '/')` will turn `PAGE` into an array and the previous string will be lost: the path requires all levels above the leaf to be arrays. No error will be raised.

## Assign or Append by Path

The difference is exactly the same as without a path, but it is more clearly visible when using a path — compare using the same data:

```php
MELBIS()->TplAssign($tpl, 'PAGE', ['DIR' => '/catalog/', 'TITLE' => 'Catalog']);

// Assign by path — clean replacement of the leaf
MELBIS()->TplAssign($tpl, 'PAGE:TITLE', 'New Arrivals');
// PAGE = ['DIR' => '/catalog/', 'TITLE' => 'New Arrivals']

// Append by path — text concatenation at the same leaf
MELBIS()->TplAppend($tpl, 'PAGE:TITLE', ' — page 2');
// PAGE = ['DIR' => '/catalog/', 'TITLE' => 'New Arrivals — page 2']
```

The rule is simple: **`Assign` by path — replace the leaf, `Append` by path — append to the leaf.** Neighboring keys are unaffected in either case.

This is exactly why paths were introduced. Previously, to change a single nested leaf, you had to pass an entire nested array to `Append` — risking neighboring keys if the merge turned out to be single-level — or read the entire variable, modify it in PHP, and write it back. With a path, exactly one leaf is addressed, and the choice between replacement and appending is made explicitly.

Thus, a typical module workflow with the template engine looks like this:

```php
function MELBIS_BASE_PAGE($mVars)
{
    $tpl = MELBIS()->TplCreate();

    // ... retrieve data from the database ...

    // Pass data
    MELBIS()->TplAssign($tpl, 'PAGE', $page);
    MELBIS()->TplAssign($tpl, 'GOODS', $goods);

    // Assemble the required content variant
    MELBIS()->TplParse($tpl, 'CONTENT', 'page_goods');

    // Assemble additional parts
    MELBIS()->TplParse($tpl, 'SCRIPTS', 'scripts');

    // Return the final HTML
    return MELBIS()->TplFinal($tpl, 'main');
}
```