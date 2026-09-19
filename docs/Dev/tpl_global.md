# Global Array

Normally, each module only parses the data it generated itself (local context). But in complex interfaces, data from one module is often needed in another (for example, the Cart module calculated a total, but it needs to be displayed in the site header).

For this purpose, there is a separate global array. It is accessed through four methods.

> **They work exactly like `Tpl*`.** `GlobalAssign` is `TplAssign`, `GlobalAppend` is `TplAppend`, and so on: the same calling forms, the same uppercase key conversion, the same merge rules, the same `:` path notation. There is exactly one difference — no context pointer is needed: there is only one global array for the entire page. If you have mastered one pair of methods, the second will require nothing new.

**`GlobalAssign($name, $value = '')`** — writes a value under the specified name, **completely overwriting** whatever was there before: both scalars and arrays. Simple assignment, no merging.

Called in two forms — a name + value pair, or a single array of pairs:

```php
// Name + value
MELBIS()->GlobalAssign('site_currency', 'UAH');
MELBIS()->GlobalAssign('cart', ['total_sum' => 1500]);

// Array of pairs: each key becomes a separate variable
MELBIS()->GlobalAssign(['cart' => $cart_data, 'user' => $user_data]);
```

When passing an array of pairs, the second argument is not used.

**Keys are converted to uppercase** — both the variable name and all string keys inside the passed array, at any depth. Write however you like in PHP; in the template the key is always UPPERCASE: `GlobalAssign('cart', ['total_sum' => 1500])` → `{CART:TOTAL_SUM}`. Numeric keys of lists remain as-is.

**`GlobalAssignRaw($name, $value = '')`** and **`GlobalAppendRaw($name, $value = '', $replace = false)`** — the same methods, but the keys of the passed array stay exactly as the module wrote them. This is for data, not for names: a global variable that several units assemble piece by piece and hand to the page whole through `{SITE|js}`, where the front end expects `userName` rather than `USERNAME`. The name of the variable itself is raised as usual, and everything else is no different: the same merging in `Append`, the same calling forms, the same path through `:`.

```php
MELBIS()->GlobalAppendRaw('site', ['lang' => $lang, 'userName' => $name]);
```
```html
var SITE = {SITE|js};   // {"lang":"ru","userName":"Ivan"}
```

A raw variable is read in the template as usual — `{SITE:USERNAME}` finds `userName`, the index raises the case while searching by itself. Only three modifiers behave differently, the ones that reach inside an array by name: `array_item`, `calc` and `webp` look for the key by an exact match, so on a raw variable the path is written as it is in the data — `{SITE|array_item:userName}`.

A variable stays raw as long as everyone who writes into it writes through `Raw`: an ordinary `GlobalAppend` on top of it will raise the case of what it appended, and the one array will end up holding keys of two kinds.

**`GlobalAppend($name, $value = '', $replace = false)`** — does not overwrite, but merges with the existing value, and does so differently depending on the type of `$value`:

* **Scalar** (string, number) — always **concatenated as text** with `.`, even if they are numbers: if the value was `5` and you appended `3`, the result will be the string `"53"`, not `8`. `GlobalAppend` never performs numeric addition: calculate it in PHP and write it with `GlobalAssign`.
* **Array** — merged with the existing value **by keys**, not by position. By default (`$replace = false`) — deep merge at any nesting level: matching associative sub-keys are preserved at every level, and matching numeric lists (galleries, tags, etc.) are not corrupted positionally but **appended to the end**. With `$replace = true` — single-level merge: a matching key is replaced by the new value entirely, along with everything that was inside it (including lists — in this mode they are replaced, not appended). String concatenation never occurs inside arrays in either mode — it is a feature of the scalar case only.

> **If the type does not match, the old value is lost.** Appending an array to a location holding a scalar — the scalar is discarded and merging starts from an empty array. Appending a scalar to a location holding an array — the array is discarded and only the new string remains. No error will be raised.

The calling forms are the same, but with one distinction: **when passing an array of pairs, the role of `$replace` is played by the second argument**, not the third.

```php
// Name + value
MELBIS()->GlobalAppend('page', ['title' => 'Product Catalog']);

// Array of pairs: second argument is $replace
MELBIS()->GlobalAppend(['cart' => $extra_cart, 'user' => $extra_user]);
MELBIS()->GlobalAppend(['cart' => $extra_cart_data], true); // single-level replacement instead of merge
```

A typical use case for `Append`: several modules independently write under the same name. The authorization library puts `page = ['auth' => 1, 'user_id' => 5]`, and the page module wants to add a title there:

```php
MELBIS()->GlobalAppend('page', ['title' => 'Product Catalog']);
// -> page = ['auth' => 1, 'user_id' => 5, 'title' => 'Product Catalog']
```

**`GlobalFetch($name, $default = null)`** — reads a value back on the PHP side (for example, to read what another module wrote to the global array). Also accepts a path: `GlobalFetch('PAGE:DIR')` will retrieve a nested element without creating anything. Behavior when a key is missing — same as `TplFetch`: without a second argument — a parse error; with any second argument (including `null`) — silently returns it instead of an error.

**`GlobalClear($name)`** — removes a value from the global array. Accepts either a single name or an array of names: `GlobalClear(['CART', 'USER'])` will delete both variables at once. A path can be specified in any of them: `GlobalClear('PAGE:DIR')` will delete one nested key, leaving the rest of `PAGE` untouched. A non-existent path is simply ignored.

## Accessing by Nested Path

All four methods understand **path notation via `:`** — the same notation used for reading in templates (`{PAGE:DIR}`). The method addresses not the entire variable, but a single nested element, without touching its neighbors.

They differ only in whether they create missing path levels: **write methods create them, read methods do not.** `GlobalAssign` and `GlobalAppend` (as well as their `Raw` forms) will build the structure down to the required leaf, while `GlobalFetch` and `GlobalClear` will not create anything for a non-existent path: the former will return a parse error or the default value, the latter will simply do nothing.

```php
// Instead of "read the entire PAGE, fix DIR in PHP, write PAGE back in full"
MELBIS()->GlobalAssign('PAGE:DIR', $url);
// only PAGE:DIR is affected — the other keys of PAGE remain as they were
```

The path also works **as a key inside an array of pairs**, when you need to distribute values across different branches in a single call:

```php
MELBIS()->GlobalAssign([
    'PAGE:DIR'  => $url,
    'PAGE:LANG' => $lang,
    'USER:NAME' => $client_name
    ]);
```

**Case does not matter:** the path is converted to uppercase entirely; `'page:dir'` and `'PAGE:DIR'` are the same thing.

**Numeric segments** address a list element: `GlobalAssign('MENU:ITEMS:0:NAME', 'Home')`.

**Intermediate levels are created automatically** — by `Assign` and `Append`. `Fetch` and `Clear` create nothing: for a missing path, `Fetch` will result in a parse error (or return the default value if one was passed), and `Clear` will do nothing.

> **An intermediate scalar along the path will be destroyed.** If `PAGE` held a string, then `GlobalAssign('PAGE:DIR', $url)` will turn `PAGE` into an array and the previous string will be lost: the path requires all levels above the leaf to be arrays. No error will be raised.

### Assign or Append by Path

The difference is exactly the same as without a path, but it is more visible when using a path:

```php
MELBIS()->GlobalAssign('page', ['dir' => '/catalog/', 'title' => 'Catalog']);

// Assign by path — clean leaf replacement
MELBIS()->GlobalAssign('PAGE:TITLE', 'New Arrivals');
// PAGE = ['DIR' => '/catalog/', 'TITLE' => 'New Arrivals']

// Append by path — text concatenation at the same leaf
MELBIS()->GlobalAppend('PAGE:TITLE', ' — page 2');
// PAGE = ['DIR' => '/catalog/', 'TITLE' => 'New Arrivals — page 2']
```

**`Assign` by path — replace the leaf, `Append` by path — append to the leaf.** Neighboring keys are unaffected in either case.

This is exactly why the path notation exists: to address a single leaf deep inside a nested structure without reading or rewriting the parent in full, and to explicitly choose between replacement and appending.

## Reading from a Template

What has been written can be read from **any** template on the page, regardless of which module rendered it.

**How it works:**

1. In the first module's code, the developer passes data:
`MELBIS()->GlobalAssign('cart', ['total_sum' => 1500]);`
2. In the second module's code — its own data, in the same way:
`MELBIS()->GlobalAssign('user', ['is_logged' => 1]);`
3. In **any** HTML template of other nested modules on the page, this data becomes accessible by the top-level key name via a colon, strictly in UPPERCASE:
* Displaying the total: `{CART:TOTAL_SUM|num} UAH`
* Checking authorization: `{*USER:IS_LOGGED==1} Hello, user! {USER:IS_LOGGED*}`
* Single value: `GlobalAssign('site_currency', 'UAH')` → `{SITE_CURRENCY}`

**🔥 Important priority rule:** Local module variables always "win" over global ones. If the global array has a key `TITLE` and the current module also passed a key `TITLE`, the parser will output the local value from the module. This protects the markup from accidental conflicts.

**🔥 Full feature set:** Global variables are processed by the same powerful parser core as regular data. Modifier chains (`{CART:TOTAL_SUM|calc:VALUE*0.9|num:0}`), logical conditions, and loops are all available:

```html
{#MENU:ITEMS}
    <a href="{URL}">{NAME}</a>
{MENU:ITEMS#}

```

The parser uses a two-level HTML structure protection system. If there is no local data for a block, the parser checks whether the block is global in order to decide its fate.

The main development rule: For the parser to understand that a block (e.g., `{#PAGE:LANGS}` or `{*USER:IS_LOGGED}`) belongs to the global context and must not be removed on the first pass, its root key (array) **must** be initialized in PHP before the module's parsing begins, even as an empty element. The easiest way to do this is via `MELBIS()->GlobalAssign('page', []);` at the entry point — this way the `PAGE` key will appear in the global array before the first module starts.

---

## Two-Pass Parsing and Caching

The template engine architecture is built on the principle of **Deferred Evaluation**. This means that page processing happens in two stages (passes). This solves the main problem: how to cache heavy modules while still keeping dynamic data in them (such as the current cart or the user's name).

**How the two passes work:**

1. **First pass (Module Generation and Cache):**
* When a module (e.g., a product list) executes, the parser processes only its *local* variables.
* If the parser encounters a tag, loop, or condition belonging to the global array (e.g., `{#PAGE:LANGS}`), it understands this is someone else's responsibility. It **"freezes"** this block and leaves it in the HTML as raw text.
* The resulting HTML (along with the frozen global tags) is saved to cache.

2. **Second pass (Global Finalizer):**
* Immediately after the module delivers its result — whether freshly generated or taken from cache, it does not matter — the parser runs it through once more, this time with the current data from the global array.
* It finds all frozen tag placeholders and instantly fills them with "fresh" data for the specific visitor.
* **Important:** this pass is executed anew on every request and **is never cached** — unlike the first pass, it simply has no mechanism for that. Even if the entire remaining HTML of the module came from a hard file cache, the global array tags inside it will still be recalculated for each visitor.

**What must not be put inside a frozen block.** A block on a global variable is frozen **whole**, together with everything inside it. Plain module tags in it will still be substituted — `{TITLE|html}` is replaced textually on the first pass — but a loop or a condition on a module variable will not: the first pass steps over `{#LIST}` inside `{*PAGE:LANG==ru} … {PAGE:LANG*}` along with the wrapper, and on the second there is no module context left. The parser catches this at the end of the first pass and says `Found unclosed or invalid tag: {#LIST}` — the tag is in fact closed, the trouble is where it stands.

The rule: **only global and system values live inside a block on a global variable.** If a module's block has to be shown by a global condition, the condition moves to the module: either an `if` in PHP before `TplParse`, or a variable of its own in the module's context — `TplAssign($tpl, 'LANG', $lang)` and `{*LANG==ru} … {LANG*}`. If it is not the whole block that differs by the condition but pieces of it, the global condition goes inside the loop, around what differs.

**🚀 Bypassing the module cache**
Precisely because the second pass is not cached, the global array is the standard way to "punch through" the cache of a heavy module with targeted dynamic inserts, without disabling caching of the block entirely. You can aggressively cache any heavy markup, while delivering visitor-specific data — the number of items in the cart `{CART:COUNT}`, the current language, the authorization check `{*USER:IS_LOGGED}` — through the global array, and they will **always execute in real time**, even inside a cached block.

**Example:** the site header is heavy, rarely-changing markup that is worth caching in full. But the logo link must account for the visitor's current language, and next to it is a catalog phone widget also tied to the language:

```html
<div class="container-fluid">
    <div class="row"> 
        <div class="col-xs-12 col-sm-5 col-md-4 col-lg-4">
            <a href="/{VAR:LANG_PATH}"><img src="{PATH}/images/design/logo.png" class="img-responsive"></a>      
        </div>
        <div class="hidden-xs col-sm-7 col-md-8 col-lg-8" style="position: relative;">        
            {MELBIS:studio_cataloge_phone([VAR:LANG])}
            <img src="{PATH}/images/design/memo.png" class="img-responsive pull-right">
        </div>
    </div> 
</div>
```

* `{VAR:LANG_PATH}` — a regular global array tag: the current language is written somewhere in advance by another, non-cached module (for example, one that determines the language from the URL/session) via `GlobalAssign('var', [...])`. It is read here on the second pass — even if the entire header is served from the file cache, the logo link still points to the language that is current for the visitor.
* `[VAR:LANG]` — the same global key, but in square brackets (see "URL-encoded output" in the "Template Syntax" section), used as a parameter for the nested module call `{MELBIS:studio_cataloge_phone(...)}`. The second pass expands global array tags throughout the module's text before the engine gets to expanding `{MELBIS:...}` calls — so the nested module receives the already resolved, live language value as its parameter, not the value that was current when the header was cached.


---

## Visitor Data: Session and Cache

A direct consequence of the second pass not being cached: session data
**cannot** be output as a regular module variable — the very first visitor
would bake their name into the cache, and everyone else would see it. Such values are passed
only through the global array:

```php
// In a non-cached module that identifies the visitor
$client_id = MELBIS()->SessionGetValue('client_id');
MELBIS()->GlobalAssign('user', [
    'is_logged' => !is_null($client_id),
    'name'      => $client_name
    ]);
```

```html
{*USER:IS_LOGGED}
    Hello, {USER:NAME|html}
{USER:IS_LOGGED*}
```

This way the site header can be cached in full, while the greeting will still be
personal. For more details on working with sessions, see the "Sessions and CSRF" section.