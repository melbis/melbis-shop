# melbis.php

`units/melbis.php` — is not an ordinary module script. It has no main function, no
templates, and the parser never calls it. It is a system environment initialization file
that is manually included once in the root script:

```php
require 'units/melbis.php';
```

> This is **the only manual file inclusion in the entire project.** All other
> modules are included automatically by the platform: the developer specifies dependencies in
> the module parameters via the IDE, and the parser loads the required files automatically at startup.
> Nesting depth is unlimited.

## What it does

`melbis.php` is not an ordinary module script. It has no main function, no templates, and the parser never calls it. It is a system environment initialization file that is manually included once in the root script. It performs the following:

**Loading the core and configuration.** Includes `core/core.php`, reads `config.json`, and defines all its parameters as PHP constants. Checks for the presence of a `shop.lock` file — if it exists, the site is blocked with an error message (used during backups or maintenance).

**Setting up error handlers.** Intercepts PHP runtime errors and fatal errors, passing them to the `MELBIS_halt` function. If an `error.save` file has been created on the server, errors are additionally written to the `_error_front.log` log file with detailed context: URL, IP, User-Agent, POST data, and session data.

**Registering the autoloader.** Includes Composer (if used) and registers the platform's class autoloader from the `core/class/` directory. This ensures all Melbis classes are loaded automatically on demand.

**The `MELBIS()` function** is the only global function that developers use in their scripts. It is implemented as a singleton: on the first call it creates a database connection and a parser instance; on subsequent calls it returns the already created one. All platform methods are called through it.

**The `MELBIS_halt()` function** is an emergency shutdown handler. It outputs a page describing the error and halts script execution. During the backup window (configured in `config.json`), it returns a `503 Service Unavailable` status code instead of `500`.