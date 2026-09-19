# Template Syntax

Everything available in an `.htm` file: including partials, outputting variables, loops, and
conditions. The methods by which a module passes data here are described in the
"Templating Engine Methods" section, and value transformations are described in the "Modifiers" section.

---

## 1. Assembling a Template from Parts

The final HTML is rarely assembled from a single file — it usually consists of a header, footer, various content variants, and placeholders. Melbis provides two ways to do this, and they are not mutually exclusive — they can be combined within a single module.

### 1.1. Assembly from the PHP Side — `TplParse`

The classic approach is already described above in the "Template Methods" section: the module explicitly decides which file to parse and which variable to store the result in:

```php
MELBIS()->TplParse($tpl, 'ORDER', 'order_absent');
```

This approach is justified when the choice of template depends on business logic that lives entirely in PHP — for example, the module itself decides whether to parse `order_absent` or `order_exist` based on data that the markup layer has no access to.

### 1.2. Assembly Inside the Template — the `{^NAME}` Tag

If the choice of parts is purely a markup concern (when to show a header/footer, when to show a placeholder instead of a block), assembling the structure from PHP is unnecessary and even undesirable: it mixes View logic with Controller code. For such cases, another `.htm` file from the same module can be included directly in the template using the `{^FILE_NAME}` tag:

```html
<div class="order-page">
    {*ORDER}
        {^ORDER_EXIST}
    {ORDER*}

    {*!ORDER}
        {^ORDER_ABSENT}
    {ORDER*}
</div>
```

In this case, the module limits itself to only passing data:

```php
MELBIS()->TplAssign($tpl, 'ORDER', $order_data);
```

and which "branch" of the markup to display is decided by the `main.htm` template itself.

**How it works:**

* The name in the tag matches the filename (without the `.htm` extension) in the current module's directory — `{^ORDER_ABSENT}` will include `order_absent.htm` from the same `units/{module}/` folder where the calling template resides.
* Inclusion is a **pure text substitution** that happens once, at the very beginning of template parsing, before loops, conditions, and variables are processed. Therefore, the included file receives **the same data context** as the location where the tag appears: the same local module variables, the same global array, and if the tag is inside a loop — the data of the current row (`{ROW}`, `{NUM1}`, etc.) for that specific iteration.
* Inclusions can be nested (a partial inside a partial), but not infinitely — if an accidental circular reference occurs, the parser will stop and write an error to the log.

**Important limitation:** you cannot split a single loop or condition block across two different inclusions — that is, you cannot put the opening `{#STORE}` in one file and the closing `{STORE#}` in another. Each included file must be a self-contained markup fragment.

**Recommendations:**

* Although technically the engine first fully expands all `{^...}` tags into a single text and only then looks for `{#KEY}...{KEY#}` pairs (so splitting a loop between two included files will formally work), this practice should be avoided — such a coupling is unreadable in either file individually and breaks easily during refactoring. Each included file is best kept as a self-contained markup fragment: it either contains the entire block, or contains no opening/closing loop or condition tags at all.

* The template-side inclusion method `{^NAME}` is closer to the MVC architecture discussed at the beginning of this document and is recommended as the default approach for splitting markup into reusable parts.

---

The templating engine works on the principle of a safe pipeline: it silently ignores missing keys (turning `null` into an empty string), blocks direct output of arrays to avoid PHP errors, and allows building modifier chains.

## 2. Outputting Variables and Working with Data

The Melbis Shop templating engine can work not only with flat variables but also with deeply nested arrays. Data exchange between different system modules is described in the "Global Array" section.

### 2.1. Basic Syntax

**Outputting a value:**

* `{TITLE}` — **Standard output.** If the key `TITLE` is present in the context but its value is `null` or an empty string, the parser will output an empty string. **If the key is not in the context at all** (the variable was not passed), the tag will remain in the output as text (`{TITLE}`) — this is intentional, so that typos in variable names are visible in the final HTML rather than silently disappearing.
* `{{TITLE}}` — **Mandatory output.** Behaves like `{TITLE}`, but if the key is absent from the context, the tag is **completely removed** (replaced with an empty string) rather than remaining as text. Useful for optional fields (`{{GIFT}}`, `{{DISCOUNT_BADGE}}`) that don't always come with the data and don't need to be hidden every time via `{*GIFT}...{GIFT*}`.

**URL-encoded output:**

* `[QUERY]` — Applies `urlencode` to the value (space → `+`, Cyrillic → `%D0%...`). For building GET parameters: `<a href="?search=[QUERY]">`, and for arguments in a nested module call: `{MELBIS:unit_name([ID])}`. Behavior when the key is missing is the same as `{TITLE}` (the tag remains visible as text).
* `[[QUERY]]` — Same as above, but with `{{TITLE}}` behavior: if the key is absent, the tag is removed entirely rather than remaining as text.

**Space is encoded as `+`** — following `application/x-www-form-urlencoded` rules. In a query string this is correct, PHP will decode the value back automatically; in module call arguments — also fine, as the parser applies a paired `urldecode`. However, in a **path segment** the plus sign will remain a plus sign: `/search/[QUERY]/` for the value `two stars` will produce `/search/two+stars/`, which is not the correct address. For paths you need `rawurlencode` — it is available as a regular modifier (see "Modifiers"):

```html
<a href="/search/{QUERY|rawurlencode}/">
```

**System tags.** Thirteen values — `{PATH}`, `{LANG}`, `{CSRF_TOKEN}`, `{BUILD}`, and others — are available in any template of any module without any `TplAssign`. The service tag `{NULL}` stands apart. All of them, along with an important note about modifiers, are collected in the "System Constants" section.

```html
<link rel="stylesheet" href="{PATH}/statics/bundle.base.css">
<html lang="{LANG}">
```

### 2.2. Accessing Nested Arrays (Multidimensional Data)
If a module passes not just a string to the template but a complex multidimensional array (for example, an array with profile settings or prices), you can access any level of nesting using a **colon `:`**.

The templating engine automatically "flattens" arrays, creating convenient keys, which ensures instant rendering speed.
*Syntax:* `{ARRAY:KEY:SUBKEY}` or `{ARRAY:INDEX:KEY}`

**Usage examples:**

* `{USER:PROFILE:PHONE}` — outputs the phone number from `['USER']['PROFILE']['PHONE']`.
* `{IMAGES:0:FILE_NAME}` — **Access by index.** Outputs the filename of the first image from the numbered array `['IMAGES'][0]['FILE_NAME']`.
* `{PRODUCT:PRICES:WHOLESALE|num:2}` — **Modifiers work everywhere.** See the "Modifiers" section for details. You can apply XSS protection, truncation, or number formatting directly to elements of deep arrays.
* `{USER:SETTINGS:AVATAR|def:/img/no-avatar.png}` — Sets a default value if the nested key does not exist.

### 2.3. Working with Simple (Flat) Lists
Modules often pass not complex associative arrays but simple lists of values (for example, an array of tags, a list of IDs, a set of colors: `['red', 'blue', 'green']`).

For outputting such lists in a loop, the parser automatically creates a virtual key **`ITEM`** that holds the current value.

**How it works:**
Inside the loop `{#LIST} ... {LIST#}` you simply use the `{ITEM}` tag. Since it is now a full-fledged engine element, **all modifiers and system variables** can be applied to it.

**Example (Outputting tags separated by commas, without a trailing comma):**

```html
Passed array: $mData['TAGS'] = ['php', 'mysql', 'js'];

In the template:
{#TAGS}
    <a href="/search/?q=[ITEM]">{ITEM|html}</a>{*!IS_LAST}, {IS_LAST*}
{TAGS#}

```

---

## 3. Logic Blocks (Loops)

Used for iterating over arrays (for example, lists of products, properties, files).

**Call syntax and limits:**

* `{#STORE}` ... `{STORE#}` — Outputs the entire array.
* `{#STORE=4}` ... `{STORE#}` — **Limit.** Outputs only the first 4 elements.
* `{#STORE=2:4}` ... `{STORE#}` — **Offset and Limit.** Skips the first 2 elements and outputs the next 4.

**Example:**

```html
{#STORE=4}
    <div class="item {*IS_FIRST}first-item{IS_FIRST*}">
        <b>{NUM1}. {NAME}</b> 
    </div>
{STORE#}

```

**Service variables inside a loop:**

* `{NUM0}` / `{NUM1}` — Element index (from 0) / Sequential number (from 1).
* `{COUNT}` — The number of elements being output in the *current slice* (taking the limit into account).
* `{IS_FIRST}` / `{IS_LAST}` — Returns `1` if this is the first/last element (useful for adding classes).
* `{IS_EVEN}` / `{IS_ODD}` — Returns `1` for even/odd rows.
* `{ROW}` — A special variable containing the **clean original array** of the current element. Used to pass all row data to modifier functions (for example, `{ROW|json}` or `{ROW|calc:...}`).

**💡 Virtual variable `:COUNT` (Checks outside the loop)**
When any indexed array is passed to a template, the parser automatically creates a "flat" variable with the `:COUNT` suffix containing the size of that array. This allows you to check the number of elements and build logic *before* calling the loop itself:

```html
{*STORE:COUNT>4}
    <div class="alert">We have more than 4 products! Total: {STORE:COUNT} items.</div>
{STORE:COUNT>4*}

```

---

## 4. Conditions (Display Logic)
Condition tags start with `*` and close the same way.

**Basic checks:**

* `{*NAME} Text {NAME*}` — Outputs if the variable is not empty (not `null`, not `''`, not `0`).
* `{*!NAME} Text {NAME*}` — Negation (outputs if empty).

**Comparison operations:**

* `{*PRICE>1000} Expensive {PRICE*}` — Supports `==`, `!=`, `<`, `>`, `<=`, `>=`.
* **Variable comparison:** If the right-hand side is the name of an existing variable, the parser will compare their values: `{*PRICE<OLD_PRICE} Discount! {PRICE*}`.

**Smart checks (IN and BETWEEN):**

* `{*STATUS==new,active} In progress {STATUS*}` — **List check.** Compares case-insensitively. Will trigger if STATUS equals `NEW`, `New`, or `active`. Works with `==` and `!=`.
* `{*ID==1-10} Top 10 {ID*}` — **Range check.** Will trigger if ID is from 1 to 10 inclusive. Works with `==` and `!=`.

**Conditions based on system tags:**

* `{*TEMPLATE==mobi} … {TEMPLATE*}` — block only for the `mobi` template group; `{*LANG==ru,uk} … {LANG*}` — for the required languages. System tags (`{TEMPLATE}`, `{LANG}`, and others — see "System Constants") participate in conditions like regular variables, with all comparison operations. This is the primary way to serve multiple groups or languages from a single file — see "Template Groups → Shared Code Between Groups" for details. Inside such a block, system and global values are available, but not local module variables.

**Why there are no compound conditions (&&, ||)**

Sometimes you want to write something like `{*NAME&&STATUS} ... {NAME*}` — directly in the template. This is intentional: compound conditions are not supported, and here is why.

* **Performance.** Each condition in a template is already checked by a separate `preg_match` pass over the entire text. Logical connectors `&&`/`||` would require either writing a mini expression parser inside the tag, or a cascade of nested checks — both options noticeably complicate and slow down template parsing, and this happens on every page and every render, not just once.
  
* **The markup stops being understandable to the developer.** `{*NAME&&STATUS==1||PRICE>0}` is no longer markup — it is code that needs to be read as code: with operator precedence, parentheses, and negations. A developer opening an `.htm` file should understand *what* is being shown, not *why* — and a complex logical expression directly in HTML forces them to reconstruct the business meaning of the condition every time.
  
* **The condition becomes an undocumented source of truth.** If the logic "a product is considered unavailable if there is no price OR no stock OR the status is removed from sale" is spread across multiple places in the markup as `{*PRICE==0||STOCK==0||STATUS==disabled}`, then when a business rule changes (for example, another status is added), you will have to find and simultaneously update all the places in the templates where this logic is duplicated. And a typo in one of them will go unnoticed — the condition will simply behave slightly differently in one place than in another.
  
* **Testability.** Logic in PHP can be covered by a unit test. Logic spread across `.htm` files mixed with markup — practically cannot.

**The correct approach** is to evaluate the compound condition once in the module and pass an already complete, semantically self-contained flag to the template:

```php
$goods_absent = empty($goods['PRICE']) || empty($goods['STOCK']) || $goods['STATUS'] === 'disabled';

MELBIS()->TplAssign($tpl, 'GOODS_ABSENT', $goods_absent);
```

```html
{*GOODS_ABSENT}
    <div class="out-of-stock">Item is out of stock</div>
{GOODS_ABSENT*}
```

Now the markup shows **what** is being checked (`GOODS_ABSENT` — a descriptive name), not a set of operators that needs to be decoded. All the logic lives in one place — in PHP, alongside the rest of the module's business logic — and it can be reused, tested, and changed without touching a single `.htm` file.


---

## 5. Complex Examples

### Example 1: SEO Block for a Product Page
Using modifier chains for safe generation of microdata and meta tags.
```html
<title>{PRODUCT_NAME|html} buy for {PRICE|nums} UAH</title>
<meta name="description" content="{DESCRIPTION|plain|short:160}">

<script type="application/ld+json">
{ROW|json}
</script>
```

### Example 2: Product Card with Complex Logic
Checking availability, calculating the discount percentage on the fly, and outputting placeholders.
```html
<div class="product-card {*IS_FIRST}first-item{IS_FIRST*}">
    <img src="{IMAGE_URL|def:/images/no-photo.png}" alt="{PRODUCT_NAME|html}">
    
    <h3>{PRODUCT_NAME}</h3>
    
    {*PRICE<OLD_PRICE}
        <div class="badge-discount">
            Discount {ROW|calc:100-(price/old_price*100)|nums:0} %
        </div>
        <span class="old-price">{OLD_PRICE|num} UAH</span>
    {PRICE*}
    
    <span class="current-price">{PRICE|num} UAH</span>

    {*STOCK_STATUS==instock,preorder}
        <button onclick="addToCart({ID|int})">Buy</button>
    {STOCK_STATUS*}
    {*STOCK_STATUS!=instock,preorder}
        <span class="out-of-stock">Out of stock</span>
    {STOCK_STATUS*}
</div>
```

### Example 3: Order Table in a Personal Account
Using ranges, dates, and loop counters.
```html
<table>
    <tr>
        <th>#</th>
        <th>Date</th>
        <th>Amount</th>
        <th>Status</th>
    </tr>
    {#ORDERS}
    <tr class="{*IS_EVEN}bg-gray{IS_EVEN*}">
        <td>{NUM1}</td>
        <td>{CREATED_AT|date:d M Y}</td>
        <td>{TOTAL|num:2,., } $</td>
        <td>
            {*STATUS_ID==1-3} <span class="badge-blue">{STATUS_NAME}</span> {STATUS_ID*}
            {*STATUS_ID==4-5} <span class="badge-green">{STATUS_NAME}</span> {STATUS_ID*}
        </td>
    </tr>
    {ORDERS#}
</table>
```