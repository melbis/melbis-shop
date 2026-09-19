# Root Scripts

Root scripts are the entry points of your site. Typically, these are `index.php` and `cron.php`. Their purpose is minimal: initialize the platform, determine which module to run, and publish the result. There is no business logic in root scripts — it all resides in modules.

> The only manual file inclusion in the entire project is `units/melbis.php`
> on the first line. What exactly it initializes is described in the "melbis.php" section.

## index.php

Let's walk through the structure of a typical root script line by line:

```php
// Melbis start
require 'units/melbis.php';
```

Including `units/melbis.php` initializes the entire platform environment: loads `config.json`, configures error handlers, registers the class autoloader, and creates a database connection. After this line, the `MELBIS()` function is available, returning the single parser instance. More details on what `melbis.php` does — at the end of this section.

---

```php
// Define session
MELBIS()->DefineSession('MELBIS_SHOP');
```

PHP session initialization. The parameter is a unique session name for this project. In addition to the session, this call automatically activates the CSRF token and checks the URL for a debugger parameter: if `?debug_on_KEY` is passed and `KEY` matches `MELBIS_DEBUG_CODE` from the configuration — debug mode is enabled. The full signature and session handling — in the "Sessions and CSRF" section.

---

```php
// Define self constants
MELBIS()->DefineSelfConst();
```

Loads user-defined constants into the PHP constants namespace. The call is optional if the project has no user-defined constants. More details — in the "Configuration" section.

---

```php
// Entry point
if ( isset($_GET['lazy']) )
{
    $entry_point = $_POST['mod'];
    $entry_param = $_POST['params'];
}
else
{
    $entry_point = $_GET['mod'] ?? 'melbis_base_page';
    $entry_param = [serialize($_GET), serialize($_POST)];
}
```

Defining the entry point — the name of the module that will be launched first. The logic is simple:

- On a regular request, the module name is taken from the GET parameter `mod`. If the parameter is not provided, the default module is launched (typically the page router).
- On a request with the `?lazy` parameter (asynchronous loading), the module name and parameters are taken from POST data. This mechanism is used by the lazy loading system — more details in the "Lazy Loading" section.

---

```php
// Entry point check
$entry_exists = MELBIS()->UnitExists($entry_point, true);
if ( !$entry_exists )
{
    header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
    $entry_point = 'melbis_base_404';
}
```

The module name came from the URL, meaning it was chosen not by the developer but by the visitor — and it is not required to match an actual module. **`UnitExists($mName, $mCheckEntry = false)`** checks that a module with that name exists in the project and returns `true` or `false`.

The second argument adds one more condition to the check: the module must be **allowed as an entry point** — marked with the "Entry point" checkbox in the IDE or included in lazy loading. This is the same rule by which the parser decides whether to run a module from a root script, so the verdict of `UnitExists` and the behavior of `Run` always match.

Without this check, `Run` with a non-existent name halts parsing with a parser error: the visitor will see a service page, and a search crawler will receive it with a `200` status code and index it. The check costs one filesystem lookup and turns this into a proper `404`.

The name is sanitized inside the method in exactly the same way as before a module is launched — path traversal via `../` is not possible.

The same check is needed everywhere a module name comes from outside: in the cron request handler and in the lazy loading script.

---

```php
// Run
MELBIS()->Run($entry_point, $entry_param);

// Publish
MELBIS()->Publish();

// Possible report
MELBIS()->Report();
```

This final trio of calls is present in every root script:

- **`Run()`** — launches the specified module, receives the HTML result from it, and recursively processes all nested module calls from templates.
- **`Publish()`** — sends the `Content-Type` header (if it has not already been sent) and outputs the finished HTML of the page. It also adds a `Server-Timing` header with compilation time data — useful when profiling via DevTools. The paired `Fetch()` returns the same HTML as a string without printing anything — it is needed when a page is replaced from module code (see "Utility Methods").
- **`Report()`** — if debug mode is active, outputs a debugger panel after the page with statistics on modules, SQL queries, and caching. If the debugger is not enabled, the method does nothing.

## Full index.php Example

```php
<?php

// Melbis start
require 'units/melbis.php';

// Define session
MELBIS()->DefineSession('MELBIS_SHOP');

// Define self constants
MELBIS()->DefineSelfConst();

// Entry point
if ( isset($_GET['lazy']) )
{
    $entry_point = $_POST['mod'];
    $entry_param = $_POST['params'];
}
else
{
    $entry_point = $_GET['mod'] ?? 'melbis_base_page';
    $entry_param = [serialize($_GET), serialize($_POST)];
}

// Entry point check
$entry_exists = MELBIS()->UnitExists($entry_point, true);
if ( !$entry_exists )
{
    header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
    $entry_point = 'melbis_base_404';
}

// Run
MELBIS()->Run($entry_point, $entry_param);

// Publish
MELBIS()->Publish();

// Possible report
MELBIS()->Report();
```

## Including a Library

Libraries are usually included via checkboxes in the module parameters through the IDE — this way
the parser knows not only the files themselves but also the tables they depend on.
A root script is not a module and has no such checkbox: if a library function is needed
right here, before `Run`, it is included using the `UnitInc` call:

```php
MELBIS()->UnitInc('melbis_inc_logic');
```

The name is passed without an extension, as with all other engine methods. The dependencies
of the library itself — those marked with checkboxes in its parameters — are pulled in
recursively, and repeated inclusion of the same module is ignored.

> **Root script only.** `UnitInc` includes the code but **does not register
> the library's tables** in the change tracking system. For a cached module,
> this is a silent failure: the library reads its tables, the module cache knows nothing about them and
> will not be invalidated when they change — the storefront continues to show stale data
> until the cache expires by time. In a non-cached module the call will work, but
> even there the correct approach is the checkbox in the IDE. Hence the rule: in modules — IDE only,
> in the root script — `UnitInc`.

## Root Script Step by Step

The same `UnitInc` is convenient for breaking the root script itself into parts. Language
detection, entry point selection, logging — each step lives in its own
inc-module, and `index.php` remains a table of contents:

```php
// Define lang
MELBIS()->UnitInc('melbis_inc_index_lang');

// Run
MELBIS()->UnitInc('melbis_inc_index_run');

// Log
MELBIS()->UnitInc('melbis_inc_index_log');
```

Such an inc-module is a regular library module created in the Workbench and
visible in the common tree. The only difference is that it does not declare functions but
executes code immediately upon inclusion.

> **Variables from an included file do not leak out.** The inclusion
> is performed inside a parser method, so the file's code runs in its scope, not in the scope of `index.php`. Everything that must survive the
> inclusion is declared in the file itself using `global`:
>
> ```php
> global $gLang, $gLangPath;
> ```
>
> Without this, the variable will silently remain empty — no error will occur, and the breakage
> will be discovered later and in a different place.

## cron.php

If the project uses periodic tasks — newsletters, data cleanup,
automated imports — a second root script, `cron.php`, appears alongside `index.php`. It also starts with `require 'units/melbis.php'`, but instead of `Run`
it declares a schedule using the `CronAdd` method and launches due tasks with a
`CronRun` call. Direct browser access to it is blocked by an `.htaccess` rule.

The schedule format, the structure of a cron module, protection against external calls, task logs,
and error handling — in the "Task Scheduler" section.

## .htaccess

The `.htaccess` file handles several tasks at once. The minimal set of rules that must be present in every project:

```apache
AddDefaultCharset UTF-8
DirectoryIndex index.html index.htm index.php
Options -Indexes

# Block direct access to configuration
RewriteRule ^config.json$ - [F]

# Block direct access to cron
RewriteRule ^cron.php$ - [F]

# Block direct access to internal engine directories
RewriteRule ^core/(cache|class|init|tmp|trick|log)/ - [F]

# Block direct access to log files anywhere in the project
RewriteRule \.log$ - [F]

# Route for lazy module loading (Lazy Loading)
RewriteRule ^lazy/$ index.php?lazy [L,QSA]
```

The `^core/(…)/` rule blocks everything the engine keeps for itself: classes
(`class`), compiled cache (`cache`), analytics data (`trick`), temporary
files (`tmp`), installation SQL scripts (`init`), and logs (`log`). Nothing from outside is ever needed there. The `core/*.php` entry points through which the Melbis Shop application communicates with the engine are not affected by this rule — logs are read within the application's script tree, not via a browser URL.

The `\.log$` rule catches everything else: `TplDumpVar` dumps inside templates,
logs from previous engine versions left in the root, and any log placed
next to the project by a developer.

The `lazy/` rule is mandatory if the project uses lazy module loading — it redirects all platform AJAX requests to `index.php` with the `?lazy` flag.

If needed, SEO-friendly URL routes, HTTPS redirects, and static asset caching rules can also be placed here.