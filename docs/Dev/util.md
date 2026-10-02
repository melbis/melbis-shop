# Utility Methods

A set of platform methods that belong neither to the template engine nor to the
database, but are needed in module code from time to time: finding out what is
currently executing, calling a function of your own module, building a path to an
uploaded file, aborting a page and serving a different one instead.

## Current Module Information

**`UnitName()`** — the name of the currently executing module. Can be useful in library functions that need to know who called them (for example, to load language tags for that specific module):

```php
$tags = LANGS\Tags($tpl, MELBIS()->UnitName());
```

**`UnitParam()`** — the declared input parameters of all loaded modules, as an array of "module name → list of declarations". An introspection tool: needed by service and debug modules that build a project map, not by ordinary storefront code.

## Calling a Function of Your Own Module

A function of your own whose name is known on the spot is called by the module simply by name — `Price($store, $currency)`: PHP assembles the full name `MELBIS_STORE_CARD\Price` itself from the `namespace` line.

**`UnitFunc($mName, ...$mParams)`** is needed when the function name arrives **as data**. Such a name is **not** resolved by PHP against the current namespace: a string in a variable is always taken as a full name, and `$func = 'Price'; $func();` will go looking for a global `Price`. Only something that knows the running module can assemble the right name — and that is what `UnitFunc` does:

```php
// Web module dispatcher: the function name came from POST
return MELBIS()->UnitFunc($mVars['post']['func'], $mVars);
```

The method checks both forms of the name — first `MODULE\Name`, then the former `MODULE_Name` of a module without a namespace — so one and the same dispatcher works in a module of either form. In a module of the former form, `UnitFunc('Price', ...)` also frees the call from the prefix: the method completes `MELBIS_STORE_CARD_Price` itself. If no function with that name exists, execution stops with an error.

`UnitFunc` has exactly one boundary: it calls functions of **its own** module. A function that other modules call is a library function — its place is in an inc-module, callers call it through a `use` line, and the library's functions call each other simply by name. `UnitFunc` is no good inside a library: the name context belongs to the calling module, and the method would build the name from that one.

**`UnitRun($mUnit, $mFunc, ...$mParams)`** includes a module and runs its function **under that module's name** — as if it had been called from inside that module, which is why `UnitFunc` works within the call. This is the method the agent endpoint uses to run AI tools, and that is its only purpose.

From a cached module the call is **forbidden** — the engine stops the page with an error. The reason lies in the dependencies: a query run under somebody else's name is ascribed to the wrong module, and the caller's cache stops depending on the table it actually reads. A module that needs another module's function needs a library: include it in the manifest and call the function by name directly — and the references will survive as well, which is what the paragraph below warns about.

```php
$answer = MELBIS()->UnitRun('melbis_agent_user', 'MELBIS_AGENT_USER\CmdList', $user_id, $fields);
```

The function name is passed here in full, with the namespace: the command `CmdList` of the `melbis_agent_user` tool is the function `MELBIS_AGENT_USER\CmdList`.

**References do not survive these methods.** The arguments are gathered into an array, and an array loses the link to the caller's variable, so a function with `&$` in its signature gets a copy and quietly works for nothing. Such functions are called by name directly — in a module with a namespace that is simply the ordinary short call.

## Path to Uploaded Files

**`FilePath($mUploadTime, $mFileName = '')`** builds a path to a file based on its upload date — images and attachments are organized into directories of the form `files/2026/07_22/14_30/`:

```php
$url = MELBIS()->FilePath($image['upload_time'], $image['file_name']);
```

Without a file name it returns the path to the directory, with a trailing slash — in this form the method replicates the template modifier `|path`. If `null` is passed instead of a date, the method switches to the current template group's directory: this is how static design assets are addressed.

**When a file name is provided, WebP checking is added.** For `.jpg`, `.jpeg`, and `.png` files, the method checks whether a ready WebP version already exists in the cache, and if so returns the path to it, otherwise to the original. Importantly, `FilePath` **does not convert anything**: it only uses what has already been converted. The cache is created by the `|webp` and `|text:webp` modifiers — they perform the conversion on the fly, the very first time an image is rendered (see "Modifiers"). Therefore, the path returned by `FilePath` points to WebP only for files the storefront has already displayed at least once.

In templates, use the modifiers; the method is needed when the path is involved in module logic — for example, when checking whether a file exists or when building a link for an external export.

## Abnormal Termination

A module that terminates abnormally must release the service compilation lock
for the cache — this is done by the `Stop()` and `Halt()` methods. They are
described in the "Logs, Errors, and Metrics" section.

## Serving a Different Page Instead of the Current One

Sometimes a module discovers mid-execution that there is nothing to show: a
product has been deleted, a section is closed, a link is outdated. Rather than
returning an empty fragment into an already-assembling page, the page can be
aborted and a completely different one sent to the browser.

```php
// In the melbis_inc_web_error library:
namespace MELBIS_INC_WEB_ERROR;

function NotFound()
{
    MELBIS()->Stop();

    header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
    MELBIS()->Run('melbis_base_404', []);

    header('Content-type: text/html; charset='.MELBIS_CHARSET);
    echo MELBIS()->Fetch();

    exit;
}
```

Line by line:

* **`Stop()`** — first of all. The current module holds a service lock on its
  cache compilation, and normal release will never be reached since `exit` comes
  at the end. Without `Stop()`, the next request to this module will assume that
  the cache is being compiled by another process and wait indefinitely.
* **`Run($mUnitName, $mParams)`** — the same method the root script uses to
  launch the entry point; here it launches the 404 page module. This module
  should be marked in the IDE as an entry-point module.
* **`Fetch()`** — returns the assembled HTML **as a string**. This is what
  distinguishes it from `Publish()`, which prints the same result itself and also
  sends `Content-Type`. The string form is needed when you set headers manually —
  as here, where the response status must be exactly `404`.
* **`exit`** — neither `Stop()` nor `Halt()` interrupt PHP execution on their own.

This pattern is convenient to keep in a library (`inc`) and call from any module
that needs to abort a page: `ERROR\NotFound();` after the line
`use MELBIS_INC_WEB_ERROR as ERROR;`.