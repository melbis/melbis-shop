# Configuration

All project settings are stored in a single file `config.json` located in the root directory of the site. At startup, the platform reads it and defines each parameter as a PHP constant. This means that in any project script you can access configuration parameters directly by name, for example `MELBIS_LANG` or `MELBIS_CACHE`.

The `config.json` file is protected from direct browser access by an `.htaccess` rule.

The file can be edited manually, however we recommend using the built-in dialog in the Melbis Shop application: **menu "Development → Installation"**. It provides a convenient graphical interface for all configuration parameters and allows you to immediately test the database connection.

> When creating a storefront on the Melbis platform, the first thing to do is include the `units/melbis.php` file — this is where `config.json` is loaded and the platform is initialized. This is described in more detail in the "Root Scripts" section.

Example configuration file:

```json
{
    "MELBIS_DB_HOST_NAME": "localhost",
    "MELBIS_DB_USER_NAME": "shop_user",
    "MELBIS_DB_USER_PASS": "password",
    "MELBIS_DB_NAME":      "shop_db",
    "MELBIS_DB_NICK":      "ms",
    "MELBIS_DB_CHARSET":   "utf8mb4",
    "MELBIS_DB_COMMAND":   "SET sql_mode = CONCAT(@@sql_mode, ',NO_UNSIGNED_SUBTRACTION');",
    "MELBIS_DB_ENGINE":      "MyISAM",
    "MELBIS_DB_ENGINE_TEMP": "Memory",
    "MELBIS_TIME_ZONE":    "Europe/Kyiv",
    "MELBIS_LANG":         "ru",
    "MELBIS_CHARSET":      "UTF-8",
    "MELBIS_DESKTOP_CHARSET": "WIN1251",
    "MELBIS_CACHE":        true,
    "MELBIS_TEMPLATE":     "default",
    "MELBIS_ROOT":         "/",
    "MELBIS_BUILD":        "1",
    "MELBIS_DEBUG_CODE":   "",
    "MELBIS_USER_LOG":     false,
    "MELBIS_IP_LIST":      "",
    "MELBIS_BACKUP_TIME_BEGIN": "05:00:00",
    "MELBIS_BACKUP_TIME_END":   "05:30:00"
}
```

## Database Parameters

| Parameter | Description |
|---|---|
| `MELBIS_DB_HOST_NAME` | MySQL server host. Can include a port separated by a colon, for example `localhost:3311`. |
| `MELBIS_DB_USER_NAME` | Database username. |
| `MELBIS_DB_USER_PASS` | Database user password. |
| `MELBIS_DB_NAME` | Database name. |
| `MELBIS_DB_NICK` | **Table alias (prefix).** Used in SQL queries as `{DBNICK}`. For example, with the value `ms`, the table `{DBNICK}_topic` becomes `ms_topic`. This allows multiple projects to be stored in a single database without table name conflicts. |
| `MELBIS_DB_CHARSET` | Database connection encoding. `utf8mb4` is recommended. |
| `MELBIS_DB_COMMAND` | SQL command executed immediately after connecting to the database. Typically used to configure the MySQL operating mode. |
| `MELBIS_DB_ENGINE` | Default table engine. `MyISAM` is recommended — it ensures efficient operation of the platform's table cache. |
| `MELBIS_DB_ENGINE_TEMP` | Engine for temporary tables. `Memory` is recommended for maximum speed. |

## Localization Parameters

| Parameter | Description |
|---|---|
| `MELBIS_TIME_ZONE` | Server time zone. Used in all date and time operations. The value must conform to PHP format, for example `Europe/Kyiv`, `UTC`. |
| `MELBIS_LANG` | Default interface language, for example `ru`, `en`, `uk`. |
| `MELBIS_CHARSET` | Site page encoding. `UTF-8` is recommended. |
| `MELBIS_DESKTOP_CHARSET` | Encoding for data exchange with the Melbis Shop Windows client. Typically `WIN1251`. |

## Platform Parameters

| Parameter | Description |
|---|---|
| `MELBIS_CACHE` | Enables (`true`) or disables (`false`) the caching system. During development it is convenient to temporarily disable it to see changes without clearing the cache. |
| `MELBIS_TEMPLATE` | The name of the main template group. Default is `default`. Corresponds to the name of a subdirectory inside `templates/`. |
| `MELBIS_ROOT` | Path to the site relative to the domain root. If the site is at the root — `/`. If in a subdirectory, for example `www.site.com/shop/` — specify `/shop/`. The value is used to generate correct links. |
| `MELBIS_BUILD` | Current build number. Appended to the URLs of CSS and JavaScript files as a cache-busting parameter (`?v=1`). Increment the value when deploying a new frontend version so that browsers refresh the files. |

## Debugging and Logging Parameters

| Parameter | Description |
|---|---|
| `MELBIS_DEBUG_CODE` | Secret key for enabling debug mode. To activate the debugger, add the parameter `?debug_on_KEY` to the URL, where `KEY` is the value of this parameter. If the value is empty, the debugger is disabled. |
| `MELBIS_USER_LOG` | Enables (`true`) user activity logging. |
| `MELBIS_IP_LIST` | Comma-separated list of IP addresses. If set, access to the site will only be allowed from these addresses. Useful for restricting access during development. |

## Backup Parameters

| Parameter | Description |
|---|---|
| `MELBIS_BACKUP_TIME_BEGIN` | Start time of the backup window in `HH:MM:SS` format. |
| `MELBIS_BACKUP_TIME_END` | End time of the backup window in `HH:MM:SS` format. |

> During the specified time window, the platform may perform scheduled data backup operations.

## Secrets

Besides the system parameters, `config.json` holds the **secrets of the installation** — the keys of payment systems, the tokens of external services, signatures. The platform treats as a secret any key of the file whose name does not begin with `MELBIS_`: that prefix is reserved for the platform itself and serves as the boundary between its parameters and yours. Secrets are defined exactly as the system parameters are — as ordinary PHP constants, available by name in any module of the project.

```json
{
    "MELBIS_DB_HOST_NAME": "localhost",
    "MELBIS_DB_USER_NAME": "shop_user",
    "MELBIS_DB_USER_PASS": "password",

    "SHOP_SECRET":         "3f7a1c92e4b8d05f",
    "APIKEY_LIQPAY_KEY":   "sandbox_a1b2c3d4e5",
    "APIKEY_NPOST":        "9d41e0c7b25a836f"
}
```

```php
$sign_source = APIKEY_LIQPAY_KEY.$data.APIKEY_LIQPAY_KEY;
$signature = base64_encode(sha1($sign_source, 1));
```

The name of a secret must be a valid identifier: Latin letters, digits and the underscore, and not a digit as the first character. The server checks the name on writing and rejects the whole save if it does not fit — otherwise the platform could neither define the constant nor return the value to the application.

Secrets are edited in the Melbis Shop application: **menu "Development → Installation", the "Secrets" tab**. The values are read from the server when the window opens, exist only while the window is open, and are not saved on the workstation.

> **The file is written whole.** The application sends the entire set — both the system parameters and the secrets — and the server rewrites `config.json` completely. A key added to the file by hand will therefore disappear on the very first save from the application; and the other way round, a row deleted on the tab deletes the key from the file.

### Why Here

`config.json` is the only part of the project that never leaves its server:

* the browser will not reach it — the file is closed off by an `.htaccess` rule;
* it does not go into the store's archive and is not deleted when the modules are restored: it is the "face" of a particular installation, not code;
* it is neither in the database dump nor in the version history of the files.

The two habitual places have no such property. A key written straight into a module's code goes into the database together with the module's body: with file version history switched on, both the current value and every past one settle there. And a custom constant from the database (see below) is defined on every request of the storefront — a payment system's key ends up in the scope of every module at once and lands in any dump.

## Custom Constants

In addition to system configuration parameters, the platform supports custom project constants — arbitrary key-value pairs stored in the store's database. This is a convenient mechanism for storing settings specific to a particular project: working hours, limits, feature toggles, and any other values a store's employee edits themselves.

Secrets have no place here. `DefineSelfConst()` defines **the whole** set of custom constants on every request of the storefront, so a payment system's key would end up in the scope of any module and any included package, and its value would pass into the database dump and into the backup. Keep API keys, tokens and signatures in `config.json` — see the "Secrets" section above.

The most convenient way to manage these constants is through the Melbis Shop application: **menu "Development → Settings Registry", tab "User Settings"**. The interface allows you to define a complex yet clear structure — for each constant you specify a key name, prefix, label, value, and description. In addition, access permissions are configured here: you can allow editing of specific constants through the application only to certain user groups.

Two methods exist for loading custom constants into PHP code:

**`DefineSelfConst()`** — loads **all** custom constants as PHP constants, available in any module:

```php
MELBIS()->DefineSelfConst();

// After this, the following can be used in code:
// SHOP_WORK_TIME_BEGIN  -> '08:00'
// SHOP_WORK_HOLIDAY     -> 'Sat/Sun'
```

**`DefineSelfVars($prefix)`** — loads only records with the specified prefix and places them into a global array, making them available in HTML templates (see the "Global Array" section for details):

```php
MELBIS()->DefineSelfVars('CONST');

// The following becomes available in the template:
// {CONST:SOME_KEY}
```

Thus, `DefineSelfConst` operates within the PHP constants namespace and covers all records, while `DefineSelfVars` provides selective loading by prefix into the template engine context.