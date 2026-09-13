# Static Build

A storefront typically pulls in dozens of small `.js` and `.css` files: styling libraries,
stylesheets, scripts for individual modules. Each file is a separate request to the server.
A bundle merges them into a single file, and this happens right when you save in the
Workbench — no external bundlers, config files, or separate build commands needed.

## The Bundle Line

Open a `.js` or `.css` file in the Workbench. Above the editor you'll see two lines: a blue one —
the file description, and a grey one — the parameters. The parameters specify which bundles
this file will be included in:

```
base.js: 4
```

To the left of the colon is the **bundle name**, to the right is the **priority**. A single file can
belong to multiple bundles, listed separated by commas:

```
base.js: 4, print.css: 2
```

Priority is optional: without it, the file gets `0`. The bundle name becomes the name of
the output file, so the extension is specified directly in it — `base.js`,
`melbis.css`, `melbis.auth.js`. The name may contain Latin letters, digits, dots, and
underscores; all other characters are stripped — in particular the hyphen, so
`melbis-auth.js` will become `melbisauth.js`.

## What Can Be Bundled

Any `.js` or `.css` file inside a template group can be included in a bundle — both from the shared
`statics/` directory and **from individual module directories**:

```
templates/default/
    statics/
        base/bootstrap.js               base.js: 2
        base/bootbox.js                 base.js: 4
        melbis/main.js                  melbis.js: 1
    units/
        melbis_cataloge/scripts.js      melbis.js: 10
        melbis_base_page/scripts.js     melbis.js: 15
```

That's the whole point: a module keeps its script alongside its own templates, where it's
easy to edit and find, while the code arrives at the storefront in a shared file, adding no
extra requests.

## Build Output

The assembled file is placed in the `statics/` directory of the same template group, with the prefix
`bundle.`:

```
templates/default/statics/bundle.melbis.js
```

A rebuild is triggered on **every save** of a file that has the bundle line filled in, and it rebuilds all bundles in the group from scratch.

A composition report is prepended to the file:

```
/*       Melbis Shop auto bundle report       */
/*         Create: 2026-07-17 15:39:56        */

/*   #1    main.js       32 ln    1 kb    /templates/default/statics/melbis/main.js            */
/*   #10   scripts.js    57 ln    1 kb    /templates/default/units/melbis_cataloge/scripts.js  */
/*   #15   scripts.js    77 ln    3 kb    /templates/default/units/melbis_base_page/scripts.js */
```

It shows what was included and in what order — this is the first place to look
when it's unclear whose code ran first.

> Bundling is concatenation only. No minification, transpilation, or path rewriting
> inside CSS takes place: files are joined in priority order as-is.
> If you need minified vendor code, put the already-minified version of the file into the bundle.

## File Order

Files are sorted by priority in ascending order. The numbers themselves are arbitrary — only their
relative values matter, so it's convenient to leave gaps so you can insert files between existing ones later.

Priorities are a handy way to separate logical groups. For example, in the demo
store, files with priority below 10 are base files from `statics/`, and above 10 are module scripts: this
ensures libraries are guaranteed to load before the code that depends on them. This is not
a platform rule, just a way to keep things organized.

> **Do not assign the same priority to files whose order matters.**
> Sorting is done by number only, and files with equal values are placed in the
> order the engine traversed the metadata directory — which cannot be predicted.

## Including in a Template

```html
<link rel="stylesheet" type="text/css" href="{PATH}/statics/bundle.base.css">
<link rel="stylesheet" type="text/css" href="{PATH}/statics/bundle.melbis.css?{BUILD}">

<script defer src="{PATH}/statics/bundle.base.js"></script>
<script defer src="{PATH}/statics/bundle.melbis.js?{BUILD}"></script>
```

`{BUILD}` is the project build number, a system tag available in any template. It
acts as a version marker: as long as the number hasn't changed, the browser serves the file from its
cache, but as soon as the number increases, it downloads the file again.

It changes in two ways: manually — via the `MELBIS_BUILD` parameter in the
**"Development → Installation"** dialog (see "Configuration"), or automatically —
via a button in the Workbench that enables incrementing `MELBIS_BUILD` on every file save.

You can add `?{BUILD}` to any bundle. Vendor libraries change infrequently,
so they are often included without the marker — but that's a matter of preference, not a requirement.

## Removing a File from a Bundle

To remove a file from a bundle, clear the bundle name from the parameters line and save
the file: it will be excluded from the bundle on the next build.

> **If the last file is removed from a bundle, the bundle itself remains on disk.** There is nothing left to build it from, so the engine simply leaves it untouched — the previous
> `statics/bundle.name` file stays in place and continues to be served to the storefront. The same applies when renaming a bundle: a file with the new name will appear, but the old one will remain. Such files must be deleted manually.

It is best to delete and rename static files using the Workbench file tree: that way, bundle metadata is moved or removed along with the file. If you delete a file outside the IDE — for example, via FTP — its metadata will remain, and the next build will report a `Can't include file to bundle` error.

---

## Style Variables: `.psv` Files

CSS cannot calculate. A button's hover colour is the primary colour darkened by
7.5%; a notice's background is the same colour diluted with white by 80%.
Ready-made frameworks work such things out during their own build and arrive here
with the values already computed: in `bootstrap.css` a single primary colour is
spread over a dozen and a half literals and occurs in 77 places. Repainting the
store means finding every one of them.

To avoid that, a **`.psv`** file — *php style vars* — is put into the bundle. It
is an ordinary PHP file that returns an array of keys; the other files of the
bundle substitute those keys by name.

`base/theme.psv`:

```php
<?php
$primary = '#007bff';

return [
    'PRIMARY'       => $primary,
    'PRIMARY_HOVER' => '#0069d9'
    ];
?>
```

`base/buttons.css`:

```css
.btn-primary { background-color: {PRIMARY}; border-color: {PRIMARY}; }
.btn-primary:hover { background-color: {PRIMARY_HOVER}; }
```

A `.psv` file is created the same way as a `.css` or a `.js` one — in the
Workbench tree, with `.psv` chosen from the list of extensions. The editor
highlights it as PHP, and its bundle line looks just the same:

```
base.css: 0
```

### The Rules

**Keys are written in capitals.** Everything matching `{NAME}` is substituted in
the template: capital Latin letters, digits and the underscore. It is the case
that makes the notation safe among the curly braces of CSS: neither
`{color:red}` nor `@media (min-width: 576px) {` falls under it.

**Keys apply to the whole bundle, not to a file.** One `.psv` with a low priority
sets the palette, and every file after it makes use of it. That is why the
vendor's `bootstrap.css` stays common to all stores, and the only thing each one
has of its own is `theme.psv`.

**Priority decides who wins.** The `.psv` files are included in order of
priority, and the same key met twice takes the value of the last one. Overriding
is built on this: a base theme with priority 0, the store's own with priority 1,
and only what differs listed in the second.

**The `.psv` itself does not get into the built file.** It is not CSS — it only
brings the keys. It is visible in the report on the bundle's contents, but not in
its body.

**A key that does not exist stays as written.** The build is not interrupted, and
after saving, the Workbench shows a warning:

```
Warning! Unknown: PRIMARY_HOWER (buttons.css);
```

Usually that is a typo in the name. If a bundle holds no `.psv` at all, the
substitution does not run at all — a bundle of pure JS will not complain about
its own `{...}`.

> **The substitution lives only in the bundle.** A file included into a template
> directly, bypassing the bundle, travels to the browser with raw `{PRIMARY}`.

### The `PhpStyleVar` Class

There is no need to work the shades out by hand: the engine has a class for it. A
`.psv` file is included by the engine, and **an included file does not inherit
the namespace of whoever included it**, so it states at the top what it takes:

```php
<?php
use Melbis\MelbisShop\PhpStyleVar as PSV;

$primary = '#007bff';

return [
    'PRIMARY'       => $primary,
    'PRIMARY_HOVER' => PSV::darken($primary, 7.5),
    'PRIMARY_BG'    => PSV::mix('#fff', $primary, 80),
    'PRIMARY_RGB'   => PSV::rgb($primary)
    ];
?>
```

A colour is passed the way it is written in CSS — `#007bff` or `#fff` — and comes
back the same way.

| Method | What it does |
|---|---|
| `darken($hex, $percent)` | darker by that many percent |
| `lighten($hex, $percent)` | lighter |
| `mix($front, $back, $percent)` | that many percent of the first colour over the second |
| `saturate($hex, $percent)` | more saturated |
| `desaturate($hex, $percent)` | closer to grey |
| `hue($hex, $degrees)` | a turn of the hue around the colour wheel |
| `yiq($hex, $dark, $light)` | which of the two texts reads on this background |
| `rgb($hex)` | `0, 123, 255` — the numbers for `rgba()` |
| `rgba($hex, $alpha)` | the whole of `rgba(0, 123, 255, 0.25)` |
| `url($hex)` | `%23007bff` — a colour inside a picture drawn in the CSS itself |
| `rem($pixels)` | pixels into `rem` |
| `stack(['Inter', 'Segoe UI'])` | a list of fonts; quotes only the names with a space |
| `svg($markup)` | the markup of a picture into a ready `url("data:image/svg+xml,...")` |

The class calculates the way SASS does — down to the rounding of a half — so a
framework built somewhere out there keeps its own shades here.

Three methods are needed where a colour gets into CSS not on its own:

```css
.shadow { box-shadow: 0 0 0 0.2rem rgba({PRIMARY_RGB}, 0.25); }
.select { background-image: url("data:image/svg+xml,%3csvg%3e%3cpath fill='{DARK_URL}'/%3e%3c/svg%3e"); }
```

### Keys in a Loop

The array is assembled by ordinary PHP, so shades of one kind are more
conveniently computed than listed:

```php
$base = [
    'PRIMARY' => '#007bff',
    'SUCCESS' => '#28a745',
    'DANGER'  => '#dc3545'
    ];

$vars = $base;
foreach ( $base as $name => $color )
{
    $vars[$name.'_HOVER'] = PSV::darken($color, 7.5);
    $vars[$name.'_BG'] = PSV::mix('#fff', $color, 80);
    $vars[$name.'_RGB'] = PSV::rgb($color);
}

return $vars;
```

That way the list of colours at the top of the file stays the only place anyone
looks into afterwards, and the number of keys plays no part: the spare ones get
in nobody's way, and the one needed always turns out to be there.

### When Something Is Wrong

The file is obliged to return an array. If it returns anything else, the build
stops:

```
second.psv gives no array of keys back
```

An error in the PHP itself — a typo, a method that does not exist — also stops
the build and names **the real file and the real line**:

```
File: .../templates/default/statics/base/theme.psv : 105
Uncaught Error: Call to undefined method Melbis\MelbisShop\PhpStyleVar::url()
```

In both cases the previously built bundle is left untouched: the storefront goes
on serving the last successful build until the file is corrected.

---

## File Transformer

A bundle only concatenates. If code also needs to be minified, obfuscated, or
otherwise transformed, there is a separate mechanism for that — the **file transformer**,
`Ctrl+B` or the button on the editing panel.

Here's how it works: the environment sends the filename and the **current contents of the editor** to the server — including unsaved edits — the server calls your transformer script via HTTP, and whatever it returns **completely replaces the text in the editor**. The result is not saved anywhere automatically: review what you got and save the file the usual way.

### Configuration

The script address is set in **"Editor Settings → Optimization → File Transformer"**:

* **Transformer URL** — `http://localhost/minify.php` by default.
* **Ask for confirmation before running** — a prompt before each invocation. The point is that transformation replaces all text in the editor, and it's easy to press `Ctrl+B` accidentally.

This is a Workbench setting, not a project setting: it is not stored in `config.json`, and
each developer configures it on their own machine.

> The script is called by the **server**, not the application — just like scheduler tasks.
> That's why the address uses `localhost`: the server calls itself, and
> the transformer works immediately, without any domain configuration. If you move the script to a
> separate host, the address must be reachable from the store's web server, not from the
> developer's workstation.

### What the Script Receives

A standard POST request with two fields:

| Field | Contents |
|---|---|
| `name` | the filename — the extension makes it easy to decide how to handle it |
| `content` | the current contents of the editor |

It should return the processed text in the response body — no wrappers, headers, or JSON. A response
with a status code of `400` or higher will be shown as an error by the environment, and the editor contents will not be changed.

### Example: minify.php

The demo store includes a ready-made transformer — a JS and CSS minifier.
It's a plain PHP file in the project root; the engine plays no part in it.

It starts with a check that the request came from the server itself:

```php
// The transformer is called by the engine, so only local requests are allowed
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
if ( $ip !== '127.0.0.1' && $ip !== '::1' )
{
    header('HTTP/1.1 403 Forbidden');
    die('Only local allowed');
}
```

Then comes the actual transformation:

```php
switch ( pathinfo($_POST['name'], PATHINFO_EXTENSION) )
{
    case 'js':
        $data = array('input' => $_POST['content']);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.toptal.com/developers/javascript-minifier/api/raw');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        echo curl_exec($ch);
        curl_close($ch);
        break;

    case 'css':
        // Same thing, but calling a CSS minifier
        break;

    default:
        echo $_POST['content'];
}
```

Branching by extension is not a platform requirement, but a convenient convention: a single address
handles all file types. The `default` branch returns the content as-is,
so pressing `Ctrl+B` on an unknown file type won't cause any harm; it's worth keeping this rule in your own transformer.

The internals can be anything: minification, obfuscation, auto-formatting, calling a
third-party service or a local utility. The platform doesn't know or check what exactly happens — it only cares about the text in the response.

> **Don't remove the local-call check.** The transformer lives in the site root,
> accepts arbitrary text, and runs on your server — without it, anyone on the internet could invoke it. The same technique is used by cron modules, which have a ready-made `CronLocalOnly()` method for this purpose (see "Task Scheduler").