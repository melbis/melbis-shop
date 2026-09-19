# Logs, Errors, and Metrics

Three channels through which the platform reports what happened: the error log
(what broke), emergency termination (how to properly interrupt a module), and
the Log API (how much everything cost). The visual panel with current request
statistics is described separately — in the "Debugger" section.

## Error Log

Runtime errors and fatal PHP errors are intercepted by
`units/melbis.php` and passed to the `MELBIS_halt` function — it outputs a page with
an error description and stops script execution (see "Root Scripts").

**Writing to a file is enabled by the presence of the `error.save` file** in the project root. While
it is absent, the message is not recorded anywhere. When it exists, errors are appended to
`core/log/melbis/front.log` with detailed context: URL, IP, User-Agent, POST data and session data.

Scheduler tasks that did not respond or returned a code outside the `2xx` range are also written there with the type **`Cron Error`** — regardless of whether the task has its own log defined (see "Task Scheduler").

All platform logs are collected in a single folder `core/log`: the `melbis` subfolder
contains `front.log` (storefront errors) and `back.log` (back-office errors), and `cron` contains
scheduler task logs. In a standard installation, Apache and Nginx logs are also mounted there, so the entire history of a single request can be read in one place.

> **The folder is located inside the site root.** Access to it must be blocked by an
> `.htaccess` rule — otherwise the logs, along with everything recorded in them, are accessible to
> the entire internet. The complete `.htaccess` is in the "Root Scripts" section.

**The platform does not clean logs** — neither by size nor by age. In a standard
installation, the entire `core/log` folder is managed by the system `logrotate`: daily
rotation, compression, one-week retention. In a non-standard deployment, rotation must be
configured manually.

The user activity log is configured separately from the error log —
the `MELBIS_USER_LOG` parameter in `config.json` (see "Configuration").

---

## Emergency Module Termination

**`Stop()`** releases the service compilation lock for the current module's cache. **`Halt($mFile, $mError, $mInfo = '')`** does the same and additionally sends a message to the error reporting system.

```php
if ( !$answer )
{
    MELBIS()->Halt(__FILE__, 'Payment gateway is unreachable', $order_id);

    return MELBIS()->TplFinal($tpl, 'error');
}
```

These are needed where a module terminates abnormally — when a connection to an external service is lost, or when invalid input data is received in a form handler. Without releasing the lock, the next request to this module will assume its cache is still being compiled by another process and wait in vain. Neither method interrupts PHP execution on its own — you must exit the module function yourself. A live example with `Stop()` — replacing a page with a 404 — is in the "Utility Methods" section.

---

## Programmatic Statistics Collection (Log API)

In addition to the visual panel, the platform provides programmatic access to module performance statistics. This is useful for automated monitoring: sending alerts on performance degradation, writing metrics to a database for subsequent analysis, or identifying problematic modules under real traffic conditions.

**`LogOn()` / `LogOff()`** — enable and disable statistics collection. Unlike the visual debugger, the Log API outputs nothing to the page and can be used in production:

```php
// In the root script, before Run():
MELBIS()->LogOn();

MELBIS()->Run($entry_point, $entry_param);
MELBIS()->Publish();

// After Run() the statistics are collected — ready for analysis
```

**`LogUnitsList()`** — returns an array of all modules involved in generating the page:

```php
$modules = MELBIS()->LogUnitsList();
// ['melbis_base_page' => true, 'melbis_cataloge' => true, ...]
```

**`LogUnitStat($unitName)`** — returns accumulated statistics for a module. Array keys:

| Key | Description |
|---|---|
| `run_cnt` | Number of actual module executions |
| `smart_cnt` | Number of Smart launches |
| `trick_cnt` | Number of Trick cache deliveries |
| `pause_cnt` | Number of cache deliveries from paused state |
| `valid_cnt` | Number of valid cache deliveries |
| `lazy_cnt` | Number of Lazy substitutions |
| `request` | Total number of calls |
| `total_time` | Total module execution time (sec) |
| `query_count` | Number of SQL queries per single call |
| `query_count_total` | Total number of SQL queries |
| `query_sum_time` | Total SQL query execution time |
| `query_max_time` | Execution time of the slowest SQL query |
| `query_max_query` | Text of the slowest SQL query |
| `query_max_params` | Parameters of the slowest SQL query |

**`LogUnitProp($unitName)`** — returns the module configuration: dependency tables, cache settings (Base, Trick, Smart), `entry_point` and `lazy_load` flags, list of connected libraries and registered callbacks.

**Example: alert on module slowdown**

```php
MELBIS()->LogOn();

MELBIS()->Run($entry_point, $entry_param);
MELBIS()->Publish();

// Check each module
foreach ( MELBIS()->LogUnitsList() as $mod => $tmp )
{
    $stat = MELBIS()->LogUnitStat($mod);

    // Skip modules whose result was taken from cache
    if ( ($stat['run_cnt'] ?? 0) == 0 ) continue;

    // Alert if the module ran for longer than 1 second
    if ( ($stat['total_time'] ?? 0) > 1 )
    {
        $params = json_encode($stat['query_max_params'] ?? []);
        error_log("[SLOW MODULE] {$mod} — {$stat['total_time']}s, "
                . "SQL: {$stat['query_max_query']}, params: {$params}");
    }
}
```

**Example: writing statistics to a database for long-term analysis**

```php
MELBIS()->LogOn();
MELBIS()->Run($entry_point, $entry_param);
MELBIS()->Publish();

foreach ( MELBIS()->LogUnitsList() as $mod => $tmp )
{
    $stat = MELBIS()->LogUnitStat($mod);
    if ( ($stat['run_cnt'] ?? 0) == 0 ) continue;

    MELBIS()->SqlQuery(__LINE__,
        "INSERT INTO {DBNICK}_log
            SET name = :NAME, date_time = NOW(),
                runtime = :RUNTIME, sql_time = :SQLTIME,
                sql_count = :SQLCNT, sql_max = :SQLMAX,
                sql_text = :SQLTEXT",
        [
            'name'    => $mod,
            'runtime' => $stat['total_time'],
            'sqltime' => $stat['query_sum_time'] ?? 0,
            'sqlcnt'  => $stat['query_count_total'] ?? 0,
            'sqlmax'  => $stat['query_max_time'] ?? 0,
            'sqltext' => $stat['query_max_query'] ?? '',
        ]
    );
}
```

By accumulating such data over several days, you can identify modules with consistently high resource consumption, find correlations between load and performance degradation, and make an informed decision about optimization.