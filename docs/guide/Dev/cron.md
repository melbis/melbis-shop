# Task Scheduler

Regular background jobs — loading supplier price lists, exporting products to
marketplaces, recalculating statistics, sending reminders, clearing the cache — in
Melbis are performed not by console scripts, but by **ordinary platform modules
called via HTTP**.

## Why via HTTP and not from the command line

Running a PHP script from the console lives in a different environment than the storefront: its own
interpreter settings, its own user, no `$_SERVER`, no session, no
familiar request environment. This creates a classic trap:
the script works in the browser but fails on schedule — or vice versa.

The Melbis scheduler is designed so that a background task is **the very same
modular script** that a developer writes and debugs in an IDE by opening it
in a browser. It runs with the same code, the same cache, and the same
connected libraries. Open the module in the browser, achieve the desired
result — and exactly the same thing will happen at night on schedule.

## How it works

```
System crontab (host)                 melbis_shop container
   |                                     |
   |── once per minute ───────────────> |
   |   docker exec ... php cron.php      |── cron.php
   |                                     |    ├─ CronAdd(...)  — task declaration
   |                                     |    └─ CronRun()     — what to run now
   |                                     |         |
   |                                     |         └── curl → http://localhost/?mod=...
   |                                     |                      |
   |                                     |                      └─ Apache of the same container
   |                                     |                           └─ index.php → task module
```

Key detail: `curl` hits `localhost` **inside the same container**, bypassing
the external proxy. This results in two important properties:

- the task executes via exactly the same path as a regular storefront page;
- such a request has `REMOTE_ADDR` equal to `127.0.0.1`, whereas a real
  visitor will have the proxy container's address there. The protection of
  cron modules is built on this distinction (see below).

> **Deployment requirement.** The address distinction works as long as the project's web server
> and the proxy live in different network namespaces. If the proxy ever
> ends up in the same container or is launched with `network_mode: host`, the
> `CronLocalOnly` protection will silently stop distinguishing a visitor from the scheduler.

## Installation

When installed with the standard installer, everything is already configured: the system crontab
has the following line added:

```
* * * * * /usr/bin/docker exec melbis_shop php /var/www/html/cron.php > /dev/null 2>&1
```

and `cron.php` is located in the project root. Direct browser access to it is blocked
by an `.htaccess` rule (`RewriteRule ^cron.php$ - [F]`).

The system crontab calls the script **every minute** and never changes
again: the entire project schedule lives inside `cron.php`, and the actual work lives
in modules. There is no need to touch the crontab on the server.

## Declaring tasks

```php
<?php
// cron.php

require 'units/melbis.php';

// Tasks
MELBIS()->CronAdd('*/5 * * * *', 'http://localhost/?mod=melbis_cron_price', 240, 'price');
MELBIS()->CronAdd('0 3 * * *',   'http://localhost/?mod=melbis_cron_clearing', 600);

// Run
MELBIS()->CronRun();

?>
```

**`CronAdd($mTiming, $mUrl, $mTimeout = 300, $mLogName = false)`**

- `$mTiming` — schedule in cron format (see the next section).
- `$mUrl` — the address to call. Typically `http://localhost/?mod=module_name`.
- `$mTimeout` — how many seconds the task lives, in both directions at once: that
  is how long `cron.php` waits for an answer, and that is how long PHP allows the
  task to run.
- `$mLogName` — log name without path and extension, or `false` if no log is needed.

**The timeout reaches the executor by itself.** A task is an ordinary request to
the storefront, and without this it would live under PHP's common limit (usually
30 seconds): `cron.php` would patiently wait out its 600, while the executor died
on the 30th with a fatal, and the log would read "HTTP code 500". The launch
therefore passes the timeout in the `X-Melbis-Cron` header, and the storefront
sets `set_time_limit` by it — but trusts the header **only from a local address**:
cron goes to `localhost`, and from outside that header means nothing. There is
nothing to declare and nothing to check in the task's module.

**`CronRun()`** compares schedules against the current minute and launches the matching
tasks. If there are multiple tasks, they are sent **in parallel** and `CronRun` waits for
all of them to finish — meaning a slow task does not delay the start of the others, but
does delay the completion of `cron.php` itself.

Time is compared in the project's timezone (`MELBIS_TIME_ZONE`), not the server's timezone.

## Schedule format

Five fields separated by spaces:

```
 ┌───────────── minute       0-59
 │ ┌─────────── hour         0-23
 │ │ ┌───────── day of month 1-31
 │ │ │ ┌─────── month        1-12
 │ │ │ │ ┌───── day of week  0-7   (0 and 7 — Sunday)
 │ │ │ │ │
 * * * * *
```

Each field accepts:

| Expression | Meaning |
|---|---|
| `*` | any value |
| `5` | exactly 5 |
| `1,15,30` | list of values |
| `9-18` | range |
| `*/10` | step over the entire field range |
| `1-20/3` | step within a range |

**The step is counted from the beginning of the field's range, not from zero.** This is the same
rule as in the system cron, but it often surprises people: `*/2` in the day-of-month field
means the 1st, 3rd, 5th … 31st, not even-numbered days. The reason is that `*` is simply
shorthand for the full field range, and the day range starts at one. For minutes and hours,
which start at zero, there is no visible difference.

**Day of month and day of week are combined with OR if both are restricted.**
The schedule `0 0 13 * 5` will trigger on the 13th **or** on a Friday — not only on
Friday the 13th. If one of the two fields is `*`, everything works with the usual AND. This rule
comes from the system cron and exists because real-world schedules usually
sound like "on the 1st and on Mondays."

Examples:

```
* * * * *        every minute
*/15 * * * *     every 15 minutes
0 * * * *        at the start of every hour
30 3 * * *       daily at 03:30
0 9-18 * * 1-5   every hour from 9 to 18 on weekdays
0 4 1 * *        on the 1st of every month at 04:00
0 2 */7 * *      on the 1st, 8th, 15th, 22nd, and 29th at 02:00
```

### Differences from system cron

- **Named aliases are not supported.** `MON`, `JAN`, and similar must be written as
  numbers.
- **Reverse ranges work.** `22-2` in the hour field means "from 22:00 to 02:00
  across midnight." System cron rejects such an expression — this is a Melbis extension.
- **A single value with a step works.** `5/10` in minutes means "starting from
  minute 5, every 10" — that is, 5, 15, 25, and so on. System cron also rejects this
  expression.
- **A zero step is ignored.** An expression with `*/0` will simply never match, rather than
  stopping the scheduler.
- An expression that does not have five fields, a value outside the field's range, or non-numeric
  garbage makes the schedule non-matching — the task silently does not execute. The schedule
  must be verified manually; the engine will not report an error.

## Task module

A cron module is an ordinary modular script with one difference: the **"Entry point module"**
flag must be enabled in the module's parameters in the IDE, otherwise the parser will refuse
to run it on direct access.

```php
function MELBIS_CRON_PRICE($mVars)
{
    // Output to log, not to browser
    header('Content-Type: text/plain; charset=utf-8');

    // Local calls only
    MELBIS()->CronLocalOnly();

    // ... work ...
    echo "Items processed: $count\r\n";

    return '';
}
```

**`CronLocalOnly()`** allows only requests from `127.0.0.1` or `::1`, and aborts
any other with a 403 code and the text `Only local allowed`. Before exiting, the method
releases the cache compilation lock — if execution is aborted with a plain `die`,
the lock will remain, and the next request to this module will uselessly wait for
a non-existent compilation.

Place the call as **the first line after the header**, before any database queries:
a task accessible from outside is an open invitation to run heavy work on your server
as many times as desired.

During debugging, it is convenient to comment out the line to open the module from
the browser, and restore it before deployment.

Everything the module prints via `echo` goes into the task log — so a meaningful
text report is more useful than silence.

## Overlapping runs

The scheduler does **not** prevent a task from starting a second time while the first
has not yet finished. If a task with a `*/5 * * * *` schedule runs for seven minutes, after
five minutes it will be launched again, in parallel with the one that has not yet finished.

This is intentional: forced locking would mean that a crashed task leaves a lock that
must somehow be released on timeout — a source of surprises worse than the problem itself.
Responsibility for the task's behavior lies with its author.

A practical approach is **self-limiting by time**: the task processes data in batches and
exits without waiting for all work to finish if it has exhausted its allotted time.
The remaining work will be picked up by the next run.

```php
$start = MELBIS()->RunTime();
foreach ( $rows as $row )
{
    // ... processing one record ...

    $time = MELBIS()->RunTime($start);
    echo 'ID: '.$row['id'].' Time: '.$time."\r\n";
    if ( $time > 100 )
    {
        echo "Abort, time limit\r\n";
        break;
    }
}
```

The threshold is chosen to be safely less than the schedule interval — then the next
run is guaranteed to find the previous one finished. This also protects against
`$mTimeout`: the task decides for itself where to stop, rather than being aborted
midway — by PHP itself now as well, whose limit is set to that same `$mTimeout`.

## Logs

The log name is passed as the fourth argument to `CronAdd` — **without path and without
extension**:

```php
MELBIS()->CronAdd('*/5 * * * *', 'http://localhost/?mod=melbis_cron_price', 240, 'price');
```

Entries will go to `core/log/cron/price.log`. Everything extra — directories, the extension —
is discarded: the argument sets only the filename inside `core/log/cron/`. Tasks
can write to a shared log or each to its own — it is enough to pass the same name or different ones.

The log records a line with the start time, address, response code, and execution time,
followed by all output from the module.

**The platform does not clean logs.** Size is unlimited, old entries are not
deleted: whoever enabled the log is responsible for maintaining it. In a standard installation,
this is handled by the system `logrotate`, which is given the entire `core/log` folder —
it rotates files daily, compresses them, and retains them for a week. On a non-standard
deployment, this must be taken care of manually.

## Errors

If a task does not respond or returns a code outside the `2xx` range, the scheduler
reports this via the project's standard error mechanism with the type **`Cron Error`** —
meaning the entry goes to `core/log/melbis/front.log` alongside other platform errors.
This happens **regardless** of whether a task log is configured or not,
so a silently failing task will not go unnoticed.

Note that writing to this log is enabled by the presence of the `error.save` file in
the project root (see "Logs, errors, and metrics"). Without it, the message will not be recorded anywhere.

## What the scheduler does not do

- **Does not catch up on missed runs.** If the container was restarting at the scheduled minute,
  the task simply will not execute, and there will be no retry. For critical daily jobs,
  it makes sense to check inside the module whether the work was done today, rather than
  relying solely on the schedule.
- **Does not maintain a queue and does not retry on failure.** A task that fails with an error
  will simply be recorded in the error log and will be launched again at the next scheduled time.
- **Does not validate the schedule for correctness.** A typo in an expression will not cause
  an error — the task will simply never match.