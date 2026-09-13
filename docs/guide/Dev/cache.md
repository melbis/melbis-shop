# Caching

The caching system is one of the key components of the Melbis platform's performance. Its philosophy is simple: the developer writes a module in a way that is convenient from the perspective of logic and code readability, while the platform handles optimization through several levels of caching.

Melbis Shop provides five types of cache. Each solves its own problem and can be used independently or in combination with others.

---

## 1. Static Query Cache (APCu)

Caching at the level of an individual SQL query. The result is stored in the server's RAM via the APCu extension. On a repeated call with the same parameters, no database query is executed — the result is taken from memory.

The default storage duration is one day; a longer period can be set if needed. However, the cache will become invalid in any case if changes appear in the involved tables: the parser **automatically finds all tables in the query text** (by the pattern `{DBNICK}_...`) and ties the cache to the time of their last modification.

Useful for frequently called reference data inside modules where it makes no sense to enable the basic cache for the entire module, but repetitive similar queries create unnecessary load.

```php
// Load the list of currencies — the same for the entire page
$currencies = MELBIS()->SqlSelectStatic(__LINE__,
    "SELECT id, name, rate
       FROM {DBNICK}_currency
      WHERE active = 1"
    // timeout not specified — default value is used (one day)
);

// Load a single record
$settings = MELBIS()->SqlSelectStaticFlat(__LINE__,
    "SELECT *
       FROM {DBNICK}_key_value
      WHERE key_name = :KEY",
    ['key' => 'delivery_free_limit']
);
```

`SqlSelectStatic` returns an array of rows, `SqlSelectStaticFlat` returns a single flat record, `SqlSelectStaticValue` returns a single value (see "Working with the Database").

Both methods obey the global switch `MELBIS_CACHE` from `config.json`: when set to `false`, they neither read nor write APCu, but simply execute the query — just like regular `SqlSelect` and `SqlSelectFlat`. By disabling caching during development, you disable this level as well, and you will not see stale reference data.

---

## 2. Dataset Cache (Enum)

Solves the N+1 query problem — when iterating over a list of products, a separate query is made for each item (for example, to fetch an image or attributes).

**How it works.** In the module that assembles the product list, a set of identifiers is registered in advance via `EnumSet`. The nested module, when calling `SqlSelectEnumFlat`, receives the prepared list of IDs via `EnumGet` for substitution into the query — and loads all the required data **in a single query**. On subsequent calls, the data is taken from memory.

```php
// In the product list module:
$goods = MELBIS()->SqlSelect(__LINE__, "SELECT id, name, price FROM {DBNICK}_store WHERE ...");

// Register the set of IDs
MELBIS()->EnumSet('store', array_column($goods, 'id'));
```

Nested module `melbis_store_image`:
```php
$image = MELBIS()->SqlSelectEnumFlat(__LINE__,
    "SELECT store_id, file_name, upload_time
       FROM {DBNICK}_store_image
      WHERE store_id IN (:IDS)
        AND kind_key = :KIND",
    'store_id',
    $id,
    ['kind' => 'kDefault']
);
```

The `EnumGet` method inside `SqlSelectEnumFlat` automatically forms a **safe list of identifiers** for substitution into `IN (...)` without using prepared statements — values are cast to integers and joined with commas.

**`EnumSet` and `EnumGet` support multiple arrays for the same key.** This is especially important on pages where the same objects appear in different blocks. For example, a catalog page may have three different product lists: the main section, bestsellers, and new arrivals. A separate `EnumSet` is made for each:

```php
MELBIS()->EnumSet('store', array_column($goods_main,     'id'));
MELBIS()->EnumSet('store', array_column($goods_hits,     'id'));
MELBIS()->EnumSet('store', array_column($goods_new,      'id'));
```

When a nested module calls `SqlSelectEnumFlat` with a specific `$id`, the `EnumGet` method iterates through all registered sets under the name `'store'` and finds the one that contains this identifier. The query with the corresponding set of IDs will already be cached — the data will be taken from memory. If the product did not end up in any set, `EnumGet` will return a set containing only the current element, and the query will execute as usual.

**Enum and the basic module cache**

The Enum + basic cache combination has a non-obvious aspect: the sets are registered by the PHP code of the list module, but when that module is served from cache, its PHP code is not executed. Without special support, this would bring back the N+1 problem: the parent is served from cache, `EnumSet` is not called — and each uncached nested module would execute its own separate query.

The platform resolves this automatically, no configuration is needed:

- When saving the module cache, all sets registered by it via `EnumSet` during execution are written to the **service header of the cache file** — the first line in the form `#MELBIS:{"enum":{...}}` (JSON metadata; it does not appear in the HTML output, but you will see this line if you open the cache file directly).
- Each time the cache is read — whether basic, Trick, or a "pause" copy — the sets from the header are restored into memory **before** processing nested modules, and `SqlSelectEnumFlat` in child modules again works with a single query, just as during a normal run.

A pleasant consequence: the restored set is always consistent with the cached HTML — nested modules will request exactly the IDs that are actually rendered in this copy of the markup, even if it is a Trick copy with slightly stale data.


---

## 3. Basic Module Cache

The primary and most universal type of caching. Enabled for each module individually in the IDE on the **"Parameters"** tab.

After a module executes, its HTML result is saved to disk. On the next call with the same input parameters, the parser serves the ready-made HTML without re-running the module.

**Basic cache parameters:**

- **Enable basic cache** — activates module caching.
- **Basic cache pause, min** — the minimum interval between cache updates. `0` means "no pause": the cache is updated immediately when data changes in the monitored tables. A non-zero value reduces server load — the cache is not rebuilt more often than once every N minutes, even if the data has changed.
- **Clear basic module cache** — force-resets the cache directly from the IDE.

**How the platform determines when the cache is stale:**

The engine knows by itself which tables a module depends on: it looks at the queries the module actually ran and writes the tables it found into the module's manifest. There is no need to mark them by hand — the list is gathered from the work as it happens and fills itself out as soon as the module turns to a new table.

From there everything is as it was: at startup the parser loads the last modification time of all tables, and when any of the monitored ones is updated, the cache of the modules that depend on it becomes stale.

Only what the module ran with its own hands counts — including the queries inside the libraries it called. A library may hold a dozen functions with a dozen different tables: the list will get the tables of **the functions this module called**, not the whole of its contents. Two modules on one library get different lists.

The "Tables" tab in the IDE shows the gathered list, and one action is left to the developer — to **untick** a table the cache must not depend on. Usually that is a log or a counter: written to often, and with no effect on the output. An unticked box holds forever — it survives a save, and the engine will never write that table back.

**Saving a file resets the cache.** The rule is one and the same for code and for markup: save a module's `.php` or its `.htm` view, and the basic and Trick caches of that module are gone, with nothing left to clear by hand. Saving a library removes the cache from every module that includes it.

The cache is removed across **every** template set, not only the one whose view was edited: working out whose view exactly was saved costs more than one extra redraw. Inserting a `{MELBIS:another_module(...)}` tag into someone else's view, however, resets the cache of the module whose view was saved — the called one knows nothing of the edit, and its cache is cleared separately if need be.

> **Important:** a table gets into the list after the module has turned to it, so the first render after a save runs without a cache — that is the moment the engine is building the list. From the second call on, the cache works as usual.

> **Important:** a module that depends neither on input parameters nor on tables is not cached at all — there is nothing in it to cache, and an empty list means exactly that.

> **Important:** Tables are added to the monitored list only if they actually exist in the database at the time the parser starts. Therefore, temporary tables (`TEMPORARY TABLE`) created inside a module are **automatically excluded** from monitoring — there is no need to exclude them manually.

> **Important:** libraries should be included **through the IDE** (the checkboxes in the library list) rather than with the PHP `require`/`include` functions or the `MELBIS()->UnitInc()` method. The tables will be counted either way — the engine ascribes a query to the module that ran it, wherever that query happens to lie. But the list of inclusions is needed for something else: by it the engine finds whose cache to reset when the library itself is saved. A module that included it around the list will be left with a stale cache.

> **Important:** If a module accesses data via `$_GET` or `$_POST` directly (bypassing input parameters), it will not work correctly with caching enabled — the result will be cached once and will not account for different values of that data. All input data must be passed as declared module parameters.
>
> For the same reason, you should not read the global array via `GlobalFetch` inside the code of a cached module and manually insert the read value into HTML — the value will be "baked" into the cache at the time of first generation and will not be updated for other visitors. If a cached block needs to display live data from the global array (current language, authorization status, etc.) — use a regular tag in its `.htm` template (for example, `{VAR:LANG}`) instead of `GlobalFetch` in PHP: such tags are expanded in a separate pass that is never cached — see the "Global Array" → "Breaking through the module cache" section for details.

> You should not use the PHP `header()` function inside a module whose result is cached: `header()` outputs data directly to the stream and is not subject to caching. Headers should be set in the root script.

> Enum sets registered by a module via `EnumSet` are automatically saved together with the cache and restored when it is read — nested modules do not need to do anything special. See the "Dataset Cache (Enum)" section above for details.

To force-reset the cache from within a module (for example, if something went wrong and a re-run is needed):

```php
MELBIS()->UnitCacheReset();
```

---

## 4. Trick Cache (Smart Cache)

A protective mechanism against peak loads. Configured on the **"Trick"** tab in the IDE.

The idea: under high server load or slow page compilation, instead of regenerating the page from scratch, the platform serves an older version of the cache — one that was saved earlier. This keeps the site operational even during unexpected traffic spikes or attack attempts — the server does not get overwhelmed by a queue of slowly generated pages.

**Trick cache parameters:**

- **Enable Trick (trick cache)** — activates the mechanism.
- **Server load limit** — the observation interval and the maximum allowable load average. When exceeded, the Trick cache is used.
- **Page compilation limit, sec** — if a page takes longer than this time to build, the Trick cache is used.
- **Max cache age, min** — how "old" the Trick cache may be. `0` — no limit.
- **Clear module Trick cache** — force reset.

The Trick cache works in parallel with the basic cache: under normal load the module operates normally, under peak load it serves the saved Trick version.

---

## 5. Smart Cache

Works in conjunction with the basic cache when the update pause is non-zero. Configured on the **"Smart"** tab in the IDE.

**The cache pause problem:** when an update interval is set, all cache copies expire simultaneously, and at the moment they are invalidated the server experiences a sharp load spike — many parallel requests rebuild the cache all at once. In addition, the data may be stale for most of the pause duration.

**The solution — Smart cache:** the platform analyzes the current load and compilation time and proactively updates the cache ahead of schedule during a quiet moment. As a result, the data is more up-to-date and the load is smoothed out.

**Smart cache parameters:**

- **Enable Smart (smart cache)** — activates the mechanism.
- **Server load up to** — when the load is below this threshold, the platform allows early cache updates.
- **Page compilation time up to, sec** — early updates are allowed only if the page generates faster than this threshold.
- **Reset module Smart data** — resets accumulated statistics.

> **Important:** for the Smart cache to work, the basic cache pause must be greater than zero.

---

## Cache Clearing

Over time, stale cache files accumulate on disk and in memory. The platform provides tools for clearing them.

**Automatic clearing on update** is enabled by calling:

```php
MELBIS()->CacheClearAuto();
```

Each time the basic cache of a module is updated, old files will be deleted automatically. However, **this is not recommended** under high load — additional file system operations increase page generation time.

**The recommended approach** is a separate cron module that runs once a day and performs scheduled cleanup:

```php
// In the cache cleanup cron module:
function MELBIS_CRON_CACHE_CLEAR($mVars)
{
    MELBIS()->CacheBaseClear();     // clear basic cache
    MELBIS()->CacheTrickClear();    // clear Trick cache (deletes files older than "Max cache age")
    MELBIS()->CacheStaticClear();   // clear stale APCu records of the static cache

    return '';
}
```

> For `CacheTrickClear` to work, modules must have the **"Max cache age"** parameter set — this is what determines which Trick files are considered stale.

### Volume Control

Two methods return the size of the disk caches in bytes — basic and Trick:

```php
$base = MELBIS()->CacheBaseSize();
$trick = MELBIS()->CacheTrickSize();
```

Traversing the directory is not a free operation, so calling these methods from storefront pages is not recommended. Their place is in a service panel or the same cron cleanup module, to log the size before and after cleanup and verify that it is actually working.

### Resetting Smart Cache Statistics

`CacheSmartReset($mUnitName = false)` clears the load and compilation time data accumulated by the Smart cache: with an argument — for a single module, without an argument — entirely.

```php
// Forget statistics for one module
MELBIS()->CacheSmartReset('melbis_store_topic');

// Reset all accumulated statistics
MELBIS()->CacheSmartReset();
```

This is needed after a significant rework of a module: the Smart cache decides when to update early based on past measurements, and if the module has become twice as fast or slow, the old statistics will skew decisions in the wrong direction. The same reset can be performed from the IDE using the **"Reset module Smart data"** button on the "Smart" tab. Returns `false` if there were no statistics for the specified module.

---

## Batch Data Mode and Work Organization

An important architectural advantage of Melbis Shop is the **batch data modification mode**. Unlike systems with a web interface where each click of "Save" immediately modifies a table, managers in Melbis Shop make changes in batches: they enter several products, review them, and save everything at once. As a result, tables change infrequently and in a single pass — this is the ideal mode for a caching system, as it rebuilds the cache far less often.

Nevertheless, work organization matters. If a manager saves each product one by one every five minutes, the cache of the corresponding modules will be rebuilt just as frequently.

To manage this, the Melbis Shop program provides restrictions on operation execution: in the **"Users"** section, for each operation you can set both time-based restrictions (for example, prohibiting saves during peak hours) and restrictions **based on current server load** — an operation is allowed only if the server is sufficiently free.

---

## Summary Table

| Cache type | Where stored | Managed | Purpose |
|---|---|---|---|
| SqlSelectStatic | APCu (memory) | In module code | Cache for individual queries without caching the entire module |
| Enum | PHP memory (static) | In module code | Eliminating N+1 queries in loops |
| Basic | Disk | IDE → Parameters | Cache of the entire module result |
| Trick | Disk | IDE → Trick | Protection against peak loads |
| Smart | APCu + disk | IDE → Smart | Load smoothing during cache pause |

---

## Recommendations

**Do not cache everything indiscriminately.** There is no point in caching modules with unique input parameters on every call and short execution times.

**Set a large pause and enable Smart cache.** If Smart is used, you can safely set a pause of 100 minutes or more — the Smart cache will keep data current and smooth out the load, preventing avalanche updates.

**Separate libraries by purpose.** One large `inc` library connected to all modules will cause any change to any of its tables to reset the cache for the entire site.

**Use Trick for critical blocks.** The site header, catalog, home page — everything that must remain accessible under any load should have a Trick cache.

**Plan cache cleanup.** Set up a cron module with a daily call to `CacheBaseClear`, `CacheTrickClear`, and `CacheStaticClear`.