# Modifiers
Called via the pipe symbol `|`. Parameters are passed via a colon `:`. Modifiers can be chained: `{VAR|mod1|mod2:param}`.

**⚠️ Case in variable-reference parameters:** If a modifier parameter is a variable name or field key rather than static text (as in `calc`, `print`, `array_item`), the parser looks for it **in exactly the case it is written** — no automatic uppercasing occurs. All data keys in the templating engine (including nested arrays passed via `TplAssign` and the global array) are stored in UPPERCASE, so such parameters must also be written in uppercase: `{ROW|calc:PRICE*2}`, not `{ROW|calc:price*2}` — otherwise the name will not be found.

**Arrays and nesting.** The value modifiers (`html`, `slash`, `int`, `num`, `nums`, `date`, `idate`, `short`, `pad`, `plain`, `text`, `path`, `case`) take both a single value and an array: on an array they apply to every element at any depth of nesting, and the keys stay as they are. The ones built differently are those that look at the shape of the array as a whole — `join`, `split`, `array_item`, `json_item`, `calc`, `tag`, `webp`, `json`, `js`.

### Text and HTML
* `{BRAND|def:No brand}` — **Default value.**
* `{DESC|short:150}` — **Truncation.** Keeps 150 characters (100 by default) and appends `...`. Second parameter — a custom suffix instead of `...`: `{DESC|short:150,read more}`.
* `{TEXT|html}` — Safe output of HTML entities (`htmlspecialchars`).
* `{TEXT|slash}` — Safe output (`addslashes`).
* `{TEXT|js}` — **Safe insertion into JS.** Wraps the value as a JSON string (`json_encode`) and additionally escapes `<`, `>`, `&`, `'`, `"`, so the value cannot be used to break out of a string or close a `</script>` tag. Use for inserting a PHP value directly into an inline `<script>`, for example: `var name = {NAME|js};`.
* `{CONTENT|plain}` — **Powerful cleanup.** Removes `<script>` and `<style>`, strips HTML tags, decodes `&nbsp;`, `&copy;` and collapses extra whitespace. Ideal for SEO tags.
* `{NAME|case:up}` — **Case.** The kind comes first: `up` — all uppercase, `down` — all lowercase, `first` — only the first letter capitalised, the tail left as it is, `title` — every word capitalised (the tail of a word is lowered in the process: `IVAN IVANOV` → `Ivan Ivanov`). The second parameter is the target: `value` (the default) or `key`, that is the names of the keys: `{DATA|case:down,key}`. The case is changed by the UTF-8 rules, Cyrillic included. The kind is obligatory: without it, or with an unknown name, parsing the template stops with an error.
* `{BADGE|tag:span,badge,color: red}` — **Wraps a value in an HTML tag.** Parameters: `tag,class,style` (everything except the tag is optional). `{BADGE|tag}` without parameters wraps in `<span>`. If the value is empty — the tag is not output at all (empty string). If the input is an array (e.g., a list of product tags) — wraps **each** element separately and joins them with a line break: `{TAGS|tag:span,tag-badge}`. Escaping of `_cm_`/`_sp_` etc. works here too — see the table below.

**Order matters:** `tag` does not escape the content of the value — if it came from a user (untrusted HTML), apply `|html` first, and only then `|tag`: `{USER_COMMENT|html|tag:span,comment}`.

### Formatted Output
* `{TEXT|print:VAR1,VAR2}` — **Flexible string formatting.** Uses standard PHP `sprintf` to substitute values into a template string.
* Allows flexible control over output order (e.g., for multilingual support).
* Supports passing both variables (looks them up in the current context, name must be strictly UPPERCASE) and static data (strings or numbers).
* If the value of key `VAR1` is not found in the module data (including if it is not written in uppercase), it is passed to the function as plain text.

### Numbers and Math
* `{ID|int}` — Cast to integer.
* `{ORDER_ID|pad:6,0}` — **Pad to length.** Parameters: `length,character` (character is optional, defaults to `0`). `{ORDER_ID|pad:6,0}` for `42` gives `000042` (left-padding). To pad **on the right**, specify the length with a minus sign: `{SKU|pad:-8, }` pads with spaces on the right to 8 characters.
* `{PRICE|num:2,., }` — **Formatting.** Parameters: `digits, decimal_separator, thousands_separator`. Defaults: `2`, `,`, `'`. Output: `1'250,50`.
* `{QTY|nums:2}` — **Smart formatting.** Drops zeros if the number is whole (`5.00` -> `5`, but `5.50` -> `5,50`).
* `{ROW|calc:PRICE-(OUT_PRICE/100)|num:0}` — **Calculator.** Safely evaluates a formula, substituting keys from the `{ROW}` array. `{QTY|calc:VALUE*2}` — math for a single variable, use the word `VALUE` inside the formula. Variable/field names in the formula must be strictly UPPERCASE (see the rule above); anything not recognized as a variable name or number will be treated as `0`.

  If `calc` is applied not to a single record but to a **list of records** (e.g., a gallery or product list), the formula is evaluated for each record separately, and the result is a list of numbers — exactly like a single value, but in plural. This is convenient when you need to calculate something for each list item without an explicit `{#...}` loop:
  ```html
  {GOODS_LIST|calc:PRICE*0.9|num:2|join:, }
  ```
  The result is a string like `89.10, 45.00, 120.50` (one number per product in `GOODS_LIST`). Since `calc` returns an array, to output it outside a loop it needs to be reduced to a string — for example via `|join`, `|json`, or passed into a `{#GOODS_LIST}...{GOODS_LIST#}` loop, where `{ROW|calc:...}` will calculate the same thing for the current element.

  Suitable for simple one-off calculations directly in markup (discount percentages, price differences, etc.); if the formula becomes long or is used in multiple places — it is better to calculate the value in PHP and pass it ready-made via `TplAssign`, for the same reasons as with compound conditions above: it is easier to see what is being calculated and to reuse the calculation.
  
### Dates
* `{TIME|date:Y-m-d}` — **Smart date.** Automatically understands both UNIX timestamps and string dates (passing them through `strtotime` ISO 8601). Default format: `Y-m-d H:i`.

### Multilingual Formats (intl)
These modifiers automatically adapt output formats to the current page language (`MELBIS()->LanguageSet('en')`), using the ICU library. Ideal for projects with `en`, `ru`, `ua`, etc.

* `{PRICE|inum:2}` — Formats a number according to language rules. (In `ru` outputs `1 250,50`, in `en` outputs `1,250.50`). No need to specify separators, only the number of decimal places (default 2).
* `{QTY|inums:2}` — Same, but drops the fractional part for whole numbers (`5.00` -> `5`).
* `{TIME|idate}` — Smart localized date. Without parameters outputs the standard format for the language (ru: *9 апр. 2026 г., 14:00*, en: *Apr 9, 2026, 2:00 PM*).
* `{TIME|idate:dd MMMM yyyy}` — Custom date format. **Important:** the ICU standard is used, not the PHP standard. Case matters (e.g., month is `M`, minutes are `m`).

### Arrays and JSON
* `{ROW|join:, }` — Joins a flat array into a string.
* `{ROW|json}` / `{ROW|js}` — Converts an array to JSON. `js` uses HEX-escaping of tags and quotes for safe insertion of both arrays and text strings into JavaScript.

  **Inside an inline `<script>` use `js`, not `json`.** The difference is purely in escaping: `json` leaves `<`, `>`, `&`, and quotes as-is, so a string from the DB containing `</script>` will break the tag before the browser even reaches the JavaScript. `js` outputs them as `<` and eliminates that possibility. For inserting JSON into an attribute or into the page body, `json` is fine.

  **Numbers are cast to numbers.** Both modifiers iterate over the array and convert string values that look like numbers into real JSON numbers: `"1250.50"` → `1250.5`. This is necessary because everything comes from the DB as strings, while external consumers (GTM, analytics, APIs) expect numeric types. The casting rule is the same as `is_numeric()`.

  **Keys that must remain strings** are listed as a parameter, comma-separated, in exactly the case they have in the data itself:

  ```html
  {ECOMM|js:ITEM_ID,SKU}
  ```

  This is needed for values that look like numbers but are not: article number `007` (otherwise becomes `7`), code `1e5` (becomes `100000`), order number with leading zeros. The key is searched at any depth of nesting; if it points to a sub-array — the entire subtree is left untouched.

  Data that entered the template through `TplAssign` is stored in upper case — for it the parameter goes in capitals too, as in the example. An array assembled otherwise (returned by a callback, arrived as a row of a loop) keeps its keys as they are, and they have to be written the same way. Bringing them to one shape beforehand is what `case` is for: `{ECOMM|case:down,key|js:item_id}`.
* `{CSV|split:;,0}` — **Splits a string by a delimiter and retrieves an element by index.** Parameters: `delimiter,index` (index is zero-based). `{CSV|split:;,0}` for the string `"Nikon;Aculon;10x42"` returns `"Nikon"`. If no index is specified — `{CSV|split:;}` — returns the **entire array** of results, which can then be passed to `|join` or output in a loop `{#CSV}...{CSV#}`.
* `{ROW|array_item:ADDRESS,CITY}` — **Retrieves a value by key (or a path of keys separated by commas) from an already prepared array**, e.g. `{ROW}`. Traverses nested keys `['ADDRESS']['CITY']` — the path, as everywhere in modifiers, is specified in UPPERCASE, since the keys of arrays that enter the template via `TplAssign` are always stored in uppercase. If the path does not lead to an existing key — returns an empty string. Without parameters — returns the original array unchanged.
* `{JSON_FIELD|json_item:address,city}` — Same as `array_item`, but the input value is a **JSON-format string**, not a ready PHP array: first decodes the JSON, then traverses the specified key path. **Case here is an exception to the general rule:** JSON keys do not pass through the templating engine and are not converted to uppercase, so the path must be written in the case in which the keys are actually written in the JSON string itself (usually — as serialized by PHP/DB, i.e. most often in lowercase, as in the example above). If the string is not valid JSON or the path is not found — returns an empty string.

If the path in `array_item`/`json_item` points not to a final scalar value but to a nested array/object — the result will be an array; combine with `|join` if a string is needed.

### System Functions
* `{UPLOAD_TIME|path}` — Generates a path to the file storage directory based on the upload date. Returns a string with a trailing slash. Used in templates together with the file name: `<img src="{UPLOAD_TIME|path}{FILE_NAME}">`.
* `{TEXT|text}` — Adds the base site path to relative `src=` and `href=` links (uses `MELBIS_ROOT`). Applied to ready HTML markup from the database: product descriptions, article text, delivery terms — i.e., content where links to files were placed by a manager in an editor.

* `{TEXT|text:webp}` — **Same plus conversion of images inside the text to WebP.** This mode finds all `src=` and `href=` attributes in the markup pointing to `.jpg`, `.jpeg` and `.png` files from the uploads directory, and replaces them with WebP versions — converting missing ones on the fly and storing them in cache, just as the `|webp` modifier does for a single image.

  The parameters match the first two of `|webp` and are passed in parentheses: `{TEXT|text:webp(80,1500)}` — quality 80, time budget 1500 ms per page. Without parentheses the defaults apply: quality `85`, timeout `1000`.

  ```html
  <div class="description">{DESCRIPTION|text:webp}</div>
  ```

  The difference from `|webp` is in scope: `|webp` works with a single image whose path is known to the module, while `text:webp` works with arbitrary HTML that may contain any number of images the module knows nothing about. The page-wide time budget protects against a description with dozens of not-yet-converted photos slowing down the response: once the budget is exhausted, the remaining images are served in their original format and will be converted on subsequent page loads.
* `{ROW|webp:quality,timeout_ms,date_field,file_field}` — **On-the-fly image conversion to WebP, with caching.** Checks whether a ready WebP version already exists; if not — converts `.jpg`/`.jpeg`/`.png` to `.webp` and saves it to a separate cache, from which it serves the ready file on subsequent requests. Returns a URL: either to the `.webp` version (conversion succeeded) or to the original file (format not supported, time limit exhausted, or conversion failed).

  All parameters are optional and are passed positionally, comma-separated:
  1. **Compression quality**, 0–100 (default `85`).
  2. **Timeout in ms** (default `1000`) — the total time budget for conversion for the *entire page*: once the cumulative page render time exceeds the threshold, the modifier stops converting new files (including for other `|webp` tags on the same page) and simply serves the original — this protects the page from slowdowns when many not-yet-cached images need to be converted at once.
  3. **Upload date field** in the source array (default `UPLOAD_TIME`) — if it is set and found, we have a *dynamic* file (a regular image uploaded via the admin panel/by a user), whose path is built from the upload date, just like `|path`. If you pass an **empty string** here, the modifier switches to *static template file* mode — the path is built relative to `templates/{template}/`, without a date dependency.
  4. **File name field** (default `FILE_NAME`) — in dynamic mode this is the key in the passed array (`{ROW:FILE_NAME}`); in static mode — the file path itself, literally, relative to `templates/{template}/`.

  The modifier understands both a single record (an associative array with date/file name fields) and a **list of records** (a gallery) — in the latter case it returns a list of ready URLs, one per element.

  **Dynamic file** (a regular uploaded image — default fields, no parameters needed):
  ```html
  {*IMG}
      <picture>
          <source srcset="{IMG|webp}" type="image/webp">    
          <img src="{IMG:UPLOAD_TIME|path}{IMG:FILE_NAME}" class="card-img-top p-2">
      </picture>    
  {IMG*}
  ```

  **Static template file** (not tied to a date — third parameter is empty, fourth is the path to the file relative to `templates/{template}/`):
  ```html
  <picture>
      <source srcset="{NULL|webp:80,1000,,/images/melbis_shop.png}" type="image/webp">    
      <img src="{PATH}/images/melbis_shop.png" class="card-img-top p-2">
  </picture> 
  ```
  Here `{NULL}` is used as a "carrier" tag: the modifier itself does not need a real variable value — the entire file path is specified via parameters.

  In both examples, `<source type="image/webp">` offers the browser a WebP version, while the regular `<img>` provides a guaranteed fallback in case the browser does not support WebP or the image has not yet been converted.

### Calling Standard PHP Functions
The parser allows passing a variable's value through any available PHP function (unless it is blocked for security reasons).

### Escaping Special Characters in Modifier Parameters
Since comma, colon, `|`, `{`, `}` and space are used by the parser itself as service delimiters, you can pass them literally in a parameter (e.g., the delimiter in `split` or a value in `style` for `tag`) using special substitutions:

| Substitution | Character |
|---|---|
| `_nl_` | newline |
| `_sp_` | space |
| `_cl_` | `:` |
| `_cm_` | `,` |
| `_pl_` | `|` |
| `_bo_` | `{` |
| `_bc_` | `}` |

For example, `{TEXT|split:_cm_,1}` will split the string by a literal comma (not the service one), and `{VAR|tag:span,,font-family: Arial_cm_ sans-serif}` will insert a real comma into the `style` value.


**How arguments are passed:**

* The value of the variable itself (what is to the left of `|`) always automatically becomes the first argument of the called function.
* All parameters you write after the colon `:` are split by comma `,` and passed to the function as the second, third, and subsequent arguments.

**Syntax:**
`{VARIABLE|function_name:param2,param3}`

**Usage examples:**

* Without additional parameters:
  * `{PRICE|round}` — Calls `round($PRICE)`. Rounds `15.7` to `16`.
  * `{TITLE|mb_strtoupper}` — Calls `mb_strtoupper($TITLE)`. Converts the string to uppercase.
  * `{EMAIL|md5}` — Calls `md5($EMAIL)`. Useful for generating hashes (e.g., for Gravatar).
* With additional parameters:
  * `{PRICE|round:1}` — Calls `round($PRICE, 1)`. Leaves one decimal place.
  * `{ARTICLE|trim:.}` — Calls `trim($ARTICLE, '.')`. Removes dots from the beginning and end of the string.
  * `{TEXT|strip_tags:<b><a>}` — Calls `strip_tags($TEXT, '<b><a>')`. Removes all tags except the allowed `<b>` and `<a>`.

**A practically important case** that is not obvious from the general rule: `{QUERY|rawurlencode}` — encoding for a path segment, unlike `[QUERY]` (see "Template Syntax").

**Passing an array as a module call argument.** Serialize the array in PHP, and pass the ready string to the template — the receiving module will declare the parameter as type `serial`, and the parser will automatically expand it back into an array:

```php
$filters_line = serialize($filters);
MELBIS()->TplAssign($tpl, 'FILTERS', $filters_line);
```

```html
{MELBIS:goods_navi([TOTAL],[PAGE_NUM],[FILTERS])}
```

Square brackets are mandatory: a serialized string is full of quotes, colons and curly braces, and without `urlencode` the argument list parsing will break. The parser performs the reverse `urldecode` itself when parsing a `serial` parameter.

Serialize in PHP, not with a modifier in the template: `TplAssign` converts all array keys to uppercase, so `serialize` inside a tag would save keys as `ORDER`, `PRICE` rather than the original `order`, `price`.

**⚠️ Case in parameters when they are array keys:** For standard PHP functions, parameters are passed **literally, as you wrote them** — the parser does not look them up in context and does not convert them to uppercase (unlike `calc`/`print`/`array_item`, where the parameter is a variable name that the engine itself finds in the UPPERCASE context). If a function's parameter is a key that the function itself uses to look inside an array string (like the second argument of `array_column`), it must match the actual case of your data keys, and those, having passed through `TplAssign`, are always in UPPERCASE:

```
{STORE|array_column:id|join}   — won't work, returns empty string: array_column looks for key 'id',
                                  but in STORE rows the key is named 'ID'
{STORE|array_column:ID|join}   — works: the parameter case matches the actual key
```

This is not the same rule as the case of parameters in `calc`/`print`/`array_item` (there the reason is the engine searching for a variable), but in practice both cases require writing the parameter in uppercase.

### Calling Custom Functions (Module Callbacks)
For complex business logic (e.g., string translation, multi-currency support, specific formatting) you can register your own functions. They are called via a modifier starting with the `$` sign (e.g., `|$Translate`).

The callback mechanism uses strict registration and passes all data to the function as a single associative array, allowing flexible combination of template variables and system settings.

#### 1. Registering Functions in PHP
There are two levels of registration: global and local (at the level of a specific module).

* Global registration (`DefineCallback`):
Writes to the core's shared registry, which is copied into each unit upon inclusion — so the callback is available to all modules, including nested submodules. It is sufficient to register it once in the top-level module: there is no need to attach the library to each nested module where the modifier is used (see "Library Modules"). Registration must be done **at the top level** of the library or module (not inside a function body): the top level is executed on every request when the file is included, whereas the module function is not run when serving from cache.
```php
MELBIS()->DefineCallback('Translate');
```
The second argument may be absent: the engine then takes the function of the same name from the file the registration line is written in — `MELBIS_INC_LANG\Translate` in a file with a namespace, or `MELBIS_INC_LANG_Translate` in a flat one. This is the only form in which the modifier's name and the function's name cannot diverge.

Naming the function explicitly is needed when it is called differently or lies in another file. The default parameters can be kept with the short form as well, by passing `null` as the second argument:

```php
MELBIS()->DefineCallback('Translate', MELBIS_INC_Translate(...), ['from' => 'en']);
```
The three dots are PHP syntax: the function is passed as a value without being called. The name is resolved by the compiler, so the registration does not fall apart when the function is renamed or the library moves into a namespace. A string in quotes is accepted as well, but it does not survive every move.

**The name is checked at registration.** If there is no such function, the engine says so at once — when the module is included, rather than the first time the modifier fires on the storefront.
* Local registration / Override (`UnitCallbackCustom`):
Called from within a specific module (after it is included). Adds a function or overrides a setting exclusively for the current module — does not propagate to submodules.
```php
MELBIS()->UnitCallbackCustom('Translate', MELBIS_INC_Translate(...), ['from' => 'ru']);
```
* Additionally, the methods `UnitCallbackSet` and `UnitCallbackGet` are available for dynamically changing default parameters at runtime.

#### 2. Calling in an HTML Template

In the template you specify the alias (with the `$` sign) and, if needed, list **additional arguments** separated by commas. Arguments can be keys from the current data array, as well as static strings or numbers.

**Syntax:** `{MAIN_VARIABLE|$alias:ARG_1,ARG_2}`


The alias is a case-sensitive key; by convention it matches the function's Pascal tail — one name is found by a single search in the template, in the registration, and in the declaration.

**Example:** `{PRICE|$Discount:GROUP_ID,10,USD}`
*(where `PRICE` and `GROUP_ID` are variables from the database, and `10` and `USD` are static text passed directly from the markup).*

#### 3. How the Data Array (Payload) for the Function is Assembled

The parser uses a **hybrid data passing model**. Your function receives a single array containing data in two formats simultaneously: by **universal numeric indices** and by **string keys**.

**Array structure using the example `{PRICE|$Discount:GROUP_ID,10,USD}`:**

```php
[
    // === Universal numeric indices ===
    0 => 1500,         // Index 0 ALWAYS contains the main value of the {PRICE} tag
    1 => 5,            // Value of the GROUP_ID variable (found in current data)
    2 => '10',         // Static string (since there is no key '10' in the data)
    3 => 'USD',        // Static string

    // === String keys ===
    'price' => 1500,   // Duplicate of the main value by tag name
    'group_id' => 5,
    'usd' => 'USD',
    'from' => 'ru'     // Pulled from the PHP default settings (see below)
]

```

*(💡 Note: the parser smartly protects numeric indices — the static argument `10` will not create a string key `'10'`, so as not to break the numbering).*

**🔥 Priority layering for string keys (from lowest to highest):**
The associative part of the array is assembled with priority layering:

1. **(Lowest) Default system parameters from PHP:** The base settings specified during registration are taken. *Example:* `['from' => 'en', 'color' => 'black']`
2. **(Medium) Main variable from the template:** The parser takes the variable name before the `|` sign and places it in the array. *Example:* `['from' => 'en', 'title' => 'Value']`
3. **(Highest) Additional keys from the template:** If the developer explicitly passed a key (e.g., `FROM`) that matches a PHP default setting, **the value from the HTML template completely overwrites the default**.

This way, simple functions can just access `$mParam[0]`, `$mParam[1]` without being tied to what the variable was called in the template, while complex functions can control business logic by overriding string keys.