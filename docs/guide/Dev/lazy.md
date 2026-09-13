# Lazy Loading

## Concept

During a normal page render, the parser runs all modules sequentially: first the router, then the header, menu, content, recommendation blocks, widgets — and only after all of them have finished does the page get sent to the browser. If one of the modules performs a heavy database query or calls an external API, it delays the delivery of the entire page.

Lazy Loading solves this problem. The idea is simple: "heavy" modules do not run together with the page — instead of their output, the parser substitutes an AJAX placeholder. The browser receives the page instantly, displays it to the user, and then fetches the content of the deferred modules in the background and inserts their results into the appropriate places without reloading the page.

This is especially useful for blocks that:
- execute slow aggregating SQL queries (statistics, recommendations, "similar products");
- call external services (exchange rates, stock availability via a supplier's API);
- are personalized and cannot be cached at the page level;
- are not critical for the user's initial perception of the page.

## Reasonable Limit

Lazy loading is a tool that is easy to abuse. If it is enabled for most modules on a page, the browser will fire dozens of parallel AJAX requests immediately after loading — one per module. This creates a peak load on the server that is indistinguishable in nature from a DDoS attack, except it is organized by the site itself.

A practical rule: lazy loading only makes sense for modules that are noticeably slower than the rest and do not affect the primary content of the page. There are typically one or two such modules on a page, rarely more than three.

## Activation

To enable lazy loading, simply set the **"Lazy Loading"** flag in the right panel of the IDE in the settings of the desired module.

After this, the module's behavior changes: during page rendering, instead of its output, the parser automatically substitutes a block of JavaScript code that:
1. Creates a container on the page with a unique identifier (`id = md5(module_name + parameters)`).
2. Immediately after the page loads, sends an AJAX request to fetch the module's content.
3. Inserts the received HTML into the container and executes all `<script>` tags from the response.

## How It Works

```
Browser                          Server (index.php)
   |                                     |
   |── GET /?topic_id=5 ────────────────>|
   |                                     |── Run(melbis_base_page)
   |                                     |    ├─ melbis_base_header  [normal]
   |                                     |    ├─ melbis_cataloge     [normal]
   |                                     |    ├─ melbis_store_topic  [normal]
   |                                     |    └─ melbis_store_random [lazy] → AJAX placeholder
   |<── HTML page (fast) ────────────────|
   |
   |── POST /lazy/ {mod: melbis_store_random, params: ...} ──>|
   |                                                           |── Run(melbis_store_random)
   |<── HTML of recommendations block ────────────────────────|
   |
   [insert into container]
```

The AJAX request goes to the same `index.php` via the `lazy/` route, which must be defined in `.htaccess`:

```apache
RewriteRule ^lazy/$ index.php?lazy [L,QSA]
```

In the root script `index.php`, this route is handled by a separate branch:

```php
if ( isset($_GET['lazy']) )
{
    $entry_point = $_POST['mod'];
    $entry_param = $_POST['params'];
}
```

The module's parameters are passed in URL-encoded form via POST — the parser forms them automatically from the same parameters with which the module was called in the template. The developer does not need to do anything extra: enable the checkbox and the module becomes lazy; disable it and it returns to normal mode.

> A module with lazy loading enabled remains a **fully functional entry point** — on a lazy call it is processed in exactly the same way as on a normal call, and can use the cache, libraries, and any other platform features.

## Custom Placeholder

The default placeholder is a ready-made block with `XMLHttpRequest` in plain JavaScript, with no dependencies on libraries present on the page. It usually does not need to be changed, but two methods allow you to replace both the address and the placeholder itself. They are called in the root script, before `Run()`.

**`DefineLazyScript($mUrl)`** — the address to which the AJAX request is sent. Defaults to `lazy/` from the site root. Change this if the route in `.htaccess` is named differently or if lazy loading requests need to be directed to a separate domain:

```php
MELBIS()->DefineLazyScript('/ajax/lazy/');
```

**`DefineLazyLoader($mHtml)`** — the HTML code of the placeholder itself. In the provided text, the parser will substitute four keys:

| Key | Value |
|---|---|
| `{SCRIPT}` | the address from `DefineLazyScript` |
| `{MODULE}` | the name of the deferred module |
| `{PARAMS}` | call parameters, in URL-encoded form |
| `{ID}` | the unique container identifier |

```php
$loader = '<div id="{ID}" class="lazy-block"><span class="spinner"></span></div>
           <script>
               fetch("{SCRIPT}", {
                   method: "POST",
                   headers: {"Content-type": "application/x-www-form-urlencoded"},
                   body: "mod={MODULE}&params={PARAMS}"
                   })
                   .then(function(r) { return r.text(); })
                   .then(function(html) { document.getElementById("{ID}").innerHTML = html; });
           </script>';

MELBIS()->DefineLazyLoader($loader);
```

A placeholder is usually replaced for two reasons: to show a skeleton or spinner instead of an empty space while the block is loading, and to execute `<script>` tags from the response — the default loader does this, but a custom implementation using `innerHTML` **will not**. If the deferred module returns markup containing scripts, you will need to handle their execution yourself in your custom loader.