# Showcase Architecture

The Melbis showcase is a few lines in the root script and a tree of modules
that unfolds from a single entry point. Here is the overall picture: how the
request path works, what the project consists of, and how modules come together
into a page.
Details of each component are covered in subsequent sections.

## Request Path

```
index.php
  │
  ├─ require 'units/melbis.php'     environment initialization: reading config.json
  │                                 into PHP constants, error handlers,
  │                                 class autoloader, MELBIS() function
  │
  ├─ MELBIS()->DefineSession(...)   visitor session, CSRF token,
  │                                 debugger activation code check
  │
  ├─ MELBIS()->Run($entry_point)    launches the entry point module
  │     │
  │     └─ Parser ─ executes the module's PHP code
  │               ─ renders its .htm templates
  │               ─ expands nested {MELBIS:...} calls
  │               ─ checks and saves cache at each level
  │
  ├─ MELBIS()->Publish()            outputs the final HTML
  │
  └─ MELBIS()->Report()             debugger panel, if enabled
```

Everything that happens between `Run` and `Publish` is the parser's work. There
is no business logic in the root script: it only decides which module to launch
first and delivers the result.

## Project Structure

| What | Location | Section |
|---|---|---|
| Root scripts | site root: `index.php`, `cron.php`, `.htaccess` | "Root Scripts" |
| Module scripts | `units/` — one `name.php` file per module | "Module Scripts" |
| Templates | `templates/{group}/units/{module}/*.htm` | "Template Syntax" |
| Settings | `config.json` in the site root | "Configuration" |
| Engine | `core/` — platform classes and data exchange scripts | — |

There is exactly one manual file inclusion in the project — `require 'units/melbis.php'` in
the root script. All other dependencies are loaded by the platform itself:
libraries are marked by the developer in the module parameters, and nested modules are
found by the parser via tags in the templates.

## Module Nesting

Each module generates an HTML fragment based on its templates. This fragment may contain
tags that call other modules — to any depth. The parser processes the entire chain automatically.

Let's look at a real example from the demo store. The router module `melbis_base_page` is
launched as the entry point and builds the complete page. Its main template `main.htm`
calls three nested modules:

```html
<!doctype html>
<html>
    {MELBIS:melbis_base_head()}

    <body>
        {MELBIS:melbis_base_header()}

        <main>
            {CONTENT}
        </main>

        {MELBIS:melbis_base_footer()}
    </body>
</html>
```

The `melbis_base_header` module, in its own template, in turn calls the catalog module:

```html
<nav>
    {MELBIS:melbis_cataloge()}
</nav>
```

And the `melbis_cataloge` template, while iterating over menu items, calls yet another
module for the submenu, passing it the `[ID]` of the current loop element:

```html
{#MENU}
    <li class="nav-item dropdown">
        {MELBIS:melbis_cataloge_sub(,,[ID])}
    </li>
{MENU#}
```

In parallel, the product page template calls the product list module, which calls the
card for each product, and the card template in turn calls two more modules — the image
and the attributes:

```html
<!-- Template melbis_store_card/main.htm -->
<div class="card">
    <h4>{NAME|html}</h4>
    {MELBIS:melbis_store_image([ID],kDefault)}
    {MELBIS:melbis_store_features([ID])}
    ...
</div>
```

Thus, a single store page can consist of a dozen nested modules — and each of them is
developed, tested, and cached independently.

## What Is Computed on Each Request

A module's result is cached together with its local data — on the next call with the
same parameters, the module may not execute at all. Therefore, everything that depends
on a specific visitor is handled by a separate mechanism: a global array is expanded in
a second pass, which is never cached.

This is where mistakes most often occur: a username, cart contents, and the current
language cannot be output as a regular module variable — otherwise the first visitor
will bake their value into the cache, and everyone else will see it. How to do it
correctly is described in the "Global Array" section; the cache levels themselves are
covered in the "Caching" section.