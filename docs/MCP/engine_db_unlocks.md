# engine_db_unlocks

Lifts the locks hanging under one's own login outside any operation — those left by a pool that fell or by an AI tool that broke off.

## The Right

"Direct access → Database → Release table locks" (`AGENT_ENGINE_DB_UNLOCKS`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| [`tables`] | list of strings | which tables to free: the bare name `store` or the real `ms_store`. Without it — all the hanging locks of one's own login |

The tool does not touch what belongs to others:

* **the storefront's lock** — it stands under `MELBIS SYSTEM`;
* **another person's lock**;
* **the lock of an open program window** — it names its operation.

One's own manual lock from the program looks the same as a hanging one and is lifted together with them. A lock taken a second ago may belong to a call that is still going: the time is visible in `engine_db_locks`.

## The Answer

```json
{"released": {"columns": ["table", "since"],
              "rows": [["store", "2026-09-17 10:12:40"]]}}
```

| Column | What it is |
|---|---|
| `table` | the freed table without the prefix |
| `since` | since when it was taken |

If there is nothing to lift, `rows` is empty, and the second block is `Nothing of yours is stuck…`, or with `tables` — `Nothing of yours is stuck on <tables>…`.

## Refusals

| Answer | When |
|---|---|
| `Unknown table: <name>` | a table that the database does not have is named in `tables` |

The common refusals — "[Answers and Refusals](answers.md)".
