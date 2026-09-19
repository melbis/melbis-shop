# Debugging Tools

When developing modules, questions inevitably arise: why is the page generating slowly? Which SQL query is causing the bottleneck? Is the cache working correctly? To answer these questions, the Melbis platform is equipped with a built-in debugger that displays detailed statistics directly on the page — without any third-party tools.

## Enabling the Debugger

The debugger is activated via URL: add the GET parameter `debug_on_KEY` to any page address, where `KEY` is the secret code defined in the `MELBIS_DEBUG_CODE` configuration parameter (or via **"Development → Installation"**, the "Debugger Password" field).

For example, if the code is `pass`:

```
https://site.com/?debug_on_pass
https://site.com/?topic_id=5&debug_on_pass
```

**The debugger state is stored in the PHP session** — once activated, it remains enabled as you navigate between pages of the site. This allows you to examine the behavior of different pages without manually adding the parameter to every URL.

To disable it — navigate to any page with the `debug_off` parameter:

```
https://site.com/?debug_off
```

**Machine-readable view — `debug_ai_KEY`.** The same secret code, but instead of the
panel the page receives only the report — the same JSON the panel provides via the
"Download Reports" button, inside the `<script type="application/json"
id="melbis-report-json">` tag. The panel with its styles, tables, and timeline is not
built at all: that is about four fifths of its volume, and a program reading the report
does not need it. The mode applies to **exactly one request** and is not stored in the
session, so there is no need to turn it on and off. File cache sizes in this view are
taken only if the session has already counted them: counting walks the cache directories,
and that is not worth it for a one-off request.

> The `MELBIS()->DebugState()` method allows you to check in module code whether the debugger is enabled, and output diagnostic data only in debug mode without affecting production:
> ```php
> if ( MELBIS()->DebugState() )
> {
>     error_log('Variable value: ' . print_r($data, true));
> }
> ```

---

## Debugger Panel

### Summary Line

At the very top is a brief summary of the current request:

| Parameter | Description |
|---|---|
| **Cache** | Whether caching is enabled (`On` / `Off`) |
| **Compile** | Total page generation time in seconds |
| **SQL cnt/time** | Number of SQL queries / total execution time |
| **Load** | Server load: 1 min / 5 min / 15 min |
| **Mem peak/lim** | Peak memory usage / PHP memory limit |
| **Files** | Number of included PHP files |
| **Cache Static** | Three numbers: size of the project's static cache entries / total APCu memory used / APCu segment size |
| **Cache Base** | Used / free space in the base cache directory. If the directory is mounted in memory (as the standard installer does), the second number is the remaining allocated space |
| **Cache Trick** | Used / free space on disk |

The three numbers for **Cache Static** are necessary because APCu memory is shared: between "how much your project has used" and "how much is free in the segment" there may be something else. If the second number is close to the third, APCu will start evicting entries, and even well-behaved requests will begin missing the cache.

### Timeline

Below the summary is a horizontal bar showing how page generation time was distributed across all modules. Each module is colored with several color bands depending on how it was processed:

| Color | Execution Type | Description |
|---|---|---|
| Pink | **Run Native** | Module executed fully (cache was not used) |
| Yellow | **Paste Lazy** | Module loaded asynchronously (Lazy Loading) |
| Light Blue | **Run Smart** | Module executed early on Smart cache command |
| Orange | **Cache Trick** | Trick cache was served instead of execution |
| Light Green | **Cache Base Pause** | Cache is valid but still within the pause period |
| Green | **Cache Base Valid** | Cache is valid, retrieved from disk |
| Gray | **Core** | Core and framework overhead |

Hovering over a segment shows the module name, time, and percentage of total page time.

---

## "Page Modules" Table

Detailed statistics for each module on the page. Columns:

| Column | Description |
|---|---|
| **Module** | Module name and its cache settings (Cache On/Off, Pause, Trick, Smart) |
| **Paste Lazy** | How many times the module was substituted as a Lazy placeholder |
| **Run Native** | How many times the module executed fully |
| **Run Smart** | How many times the module was launched early by Smart cache |
| **Cache Trick** | How many times Trick cache was served |
| **Cache Base Pause** | How many times the cache was valid but in a pause state |
| **Cache Base Valid** | How many times the cache was retrieved from disk as valid |
| **Total Call** | Total number of module calls on the page |
| **Total Time** | Total module execution time |
| **SQL in/total** | SQL queries per single call / total across all calls |
| **SQL time** | Total SQL query execution time |
| **SQL avg/max** | Average and maximum time for a single SQL query |
| **Content of query for max time** | Text of the slowest SQL query with parameters |

Cells where actual execution or cache retrieval occurred are highlighted in the corresponding color — making it immediately clear which path each module took.

The **Summary** row at the bottom shows totals across all modules on the page.

---

## "Modules Details" Table

A section for detailed cache analysis of each module (displayed when caching is enabled). For each module, a header row is shown: name, number of tracked tables, and number of calls on the page.

For each module call, the following is displayed:

**Last update** — the table that was modified most recently among all dependent tables, with the time of modification. Shows what potentially could have invalidated the cache. To the right is the current server time (`right now`) so you can see the actual difference.

**Cache state** — cache status: whether it is enabled, the timeout, and timestamps of the latest cache directories.

**Cache load** — how the result was obtained for this specific call:

- `Run Native` — the module executed, cache is being created.
- `Run Smart` — the module executed early at the initiative of Smart cache.
- `Cache Base Valid` — cache is valid, retrieved from disk. Shows the cache creation time and the table that determined its validity.
- `Cache Base Pause` — cache is valid due to pause. Shows the cache creation time, time until the pause ends (`ETA`), and the deadline.
- `Cache Trick` — Trick cache was served. The reason is specified: high server load (`High Server load`) or long page compile time (`Long Page compile`), the actual and allowed values, and the age of the Trick cache.

**Run info** — timing for the specific call: start time (`Start`), end time (`End`), duration (`Run`), and share of total page time (`Part`). Below are the input parameters with which the module was called.

---

## "Cache Smart Monitor" Table

Cumulative cache operation statistics over the entire observation period (stored in APCu). Displayed if at least one module has Smart cache enabled. This is the data Smart cache uses to make decisions about early updates.

For each module — a row with its cache settings and seven statistics columns in `Cnt/Sum/Avg` format (count / total / average):

| Column | Description |
|---|---|
| **Paste Lazy** | Lazy call statistics |
| **Run Native** | Actual execution statistics |
| **Run Smart** | Early Smart launch statistics |
| **Cache Base Valid** | Valid cache delivery statistics |
| **Cache Base Pause** | Cache delivery in pause state statistics |
| **Cache Trick** | Trick cache usage statistics |
| **Total Run + Cache** | Total statistics for all calls (Cnt/Sum/Avg/Min/Max) |

Below the numbers for each column, a **mini histogram** is displayed (a bar proportional to the column maximum), allowing you to quickly spot anomalies and imbalances without scrutinizing the numbers.

The **"Reset Module Smart Data"** button in the IDE clears the accumulated statistics for a specific module.

---

## "Static Cache" Table

A breakdown of the cache for individual SQL queries (`SqlSelectStatic`, `SqlSelectStaticFlat`). Entries are grouped by query text: one row per SQL query, regardless of which parameters it was called with. Sorted by memory usage, heaviest entries at the top.

| Column | Description |
|---|---|
| **Query** | Group code — a short hash of the query text. The verdict is shown below the code (see below) |
| **Size** | How much memory the group occupies |
| **Share, %** | Share of the project's total static cache |
| **Variants** | How many distinct parameter sets were encountered |
| **Stale, %** | Share of dead entries: the source table has already changed, these entries can no longer be hit, but they occupy memory until their TTL expires |
| **Hit/Var** | Hits per variant: average and median |
| **Saved** | Estimated CPU time saved: query execution time multiplied by the number of hits |
| **Unused, %** | Share of memory occupied by entries with one hit or none at all |
| **Refresh reason** | Tables that have already invalidated entries in this group: name, number of killed entries, and how much time has passed since the change. Empty if nothing has gone stale yet |
| **Unit:line** | Module and line where the entry was created. `SYSTEM` means a call from the root script, before modules started |
| **Cached query** | Query text |

### How to Read Average and Median

This is the key pair of numbers in the table, and what matters is the divergence between them.

`24 : 18` — the numbers are close together, hits are distributed evenly across variants, the cache is working as intended.

`5 : 1` — the average is inflated by a handful of "hot" variants, while the typical entry was used exactly once. This is exactly what it looks like when a query is cached with overly diverse parameters: total hits accumulate due to volume, but most entries simply occupy memory. The average alone won't reveal this — it will look fine.

How much memory is being wasted is answered by the **Unused, %** column.

### Verdicts

| Verdict | When | What to do |
|---|---|---|
| **Waste** | zero hits | entries are created and never read — caching is not needed here |
| **Bloat** | memory share exceeds 25% and **Unused** exceeds 50% | the query is cached with overly diverse parameters; narrow the parameter set or abandon caching |
| **Good** | more than one second saved | pays off even with few hits |
| **Churn** | more than half of entries have gone stale | the source changes more frequently than the cache lives; reduce the TTL |
| **Good** | more than five hits per variant | pays off through frequency |
| **Poor** | fewer than two hits per variant | barely reused |
| **OK** | in all other cases | |

Checks are performed in this order, and **Good by saved time** is intentionally placed above frequency-based checks: a heavy query called ten times offloads the server more than a lightweight one called a thousand times, and it should not receive a `Poor` rating for that.

> The report is only shown when caching is enabled (`MELBIS_CACHE`): when disabled, `SqlSelectStatic` works like a regular `SqlSelect`, no new entries are created, and there is nothing to display.

---

## Exporting the Report: "Download Reports" Button

Below the summary line there is a **Download Reports** button. It downloads a JSON file with all the **raw** panel data — not a screenshot or table markup, but the actual numbers from which the tables are built.

What's inside:

- **env** — build version, template, language, generation time, memory, number of included files, server load, SQL counters;
- **cache** — sizes of all cache levels and APCu segment fill level;
- **units** — for each module, all collected statistics: calls and times across all modes, query counters, the slowest query with parameters, cache flags, and the list of tracked tables;
- **static** — for each static cache group, the full set of metrics, query text, and **a list of hits per entry** — from which any distribution can be calculated, not just average and median;
- **smart** — accumulated Smart cache statistics.

The file writes nothing to the server: the data is embedded in the debugger page itself, and the browser assembles the file on the client side. The cached values themselves are not included in the export — only metadata — so the file size stays within tens of kilobytes.

### Analysis with AI

The primary use case for which the export was created: you can hand the file to a language model and ask it to analyze it. You can't do this with a screenshot — the model only sees what was captured in the frame and cannot sort or cross-reference sections.

Meaningful questions to ask:

- which modules contribute the most to generation time, and what is heavier in them — SQL or the compilation itself;
- which static cache groups are not paying off and why: overly diverse parameters, too-frequent table changes, or the query is simply cheap;
- where cache settings diverge from the actual load profile — for example, a daily TTL on data that changes every ten minutes;
- compare two exports made before and after a change, and show exactly what changed.

The last point is the most practical. Download the report before optimization and after, then hand both files to a language model and ask it to show the difference. The numbers make it far clearer than a subjective sense of page speed.

> The export contains SQL query texts, module names, and the table structure of your project. This is not passwords or customer data, but it is also not something to publish publicly — treat the file as internal technical information.

---

## Tool: `RunTime`

For precise measurement of execution time for an arbitrary code segment:

```php
// Mark the start
$start = MELBIS()->RunTime();

// ... code to be measured ...

// Get elapsed time
$elapsed = MELBIS()->RunTime($start);

if ( MELBIS()->DebugState() )
{
    error_log("Discount calculation: {$elapsed}s");
}
```

`RunTime()` without an argument returns the current timestamp. `RunTime($start)` returns the difference between the current time and `$start`.

---

## Tool: Debugging via File Dump

When developing AJAX modules and web modules, standard debugging via browser output does not work — the result goes to JavaScript and is lost there. In such cases, it is convenient to save intermediate data to a file:

```php
// Save the $data array to a file dump.htm next to the module's templates
MELBIS()->TplDumpVar('dump', $data);
```

The file is created in the templates directory of the current module. Remember to remove the call after debugging.

---

## Practical Scenarios

**Find a slow module.** Check the summary line (`Compile`) and the timeline — pink (`Run Native`) segments with the greatest width indicate the longest executions. In the "Page Modules" table, find the row with the maximum `Total Time` and examine `Content of query for max time`.

**Verify that the cache is working correctly.** In the "Page Modules" table, most modules should have non-zero values in the `Cache Base Valid` or `Cache Base Pause` columns. In "Modules Details", each call should show `Cache Base Valid` or `Cache Base Pause`, not `Run Native`.

**Understand why the cache is being invalidated too frequently.** In "Modules Details", look at the `Last update` column — it shows which table was modified most recently and at what time. If the cache is being invalidated by a table that should not affect it, it may be listed unnecessarily in the module's dependency list.

**Evaluate Smart cache effectiveness.** In "Cache Smart Monitor", the ratio of `Run Smart` to `Run Native` shows how well Smart cache manages to update data in the background, preventing "cold" execution while visitors are active.

**Check Trick cache triggering.** In the "Page Modules" table, the `Cache Trick` column should be non-zero only under genuinely high load. If Trick is triggering constantly under normal load — the thresholds in the module settings need to be reviewed.

**Find a query that is wasting memory.** The "Static Cache" table is sorted by size — look from the top. A warning combination: large `Share, %`, high `Unused, %`, and a divergence between average and median in `Hit/Var`. This means the query is cached with overly diverse parameters: there appear to be many hits, but they fall on a handful of variants while the rest of the entries just sit there. The engine marks such a row with the `Bloat` verdict.

**Determine whether caching a query is worthwhile at all.** Look at `Saved` — the estimated CPU time saved. A cheap query with a thousand hits may save less than a heavy one with a dozen; in the first case, the cache occupies memory with almost no benefit.

**Compare state before and after a change.** Download the report using the **Download Reports** button before and after optimization, then hand both files to a language model and ask it to show the difference. The numbers reveal it far more clearly than a subjective sense of page speed.