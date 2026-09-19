# engine_db_select

Reads data as a pool of steps: selects and the map of the tables' dependencies. It cannot write.

## The Right

"Direct access → Database → Read data" (`AGENT_ENGINE_DB_SELECT`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| [`pool`] | list of objects | the steps of the pool — see "[The Database](engine_db.md)" |
| [`pool_source`] | string | a JSON file on this computer with the list of steps — an absolute path or a path from the store folder — instead of `pool` |

Either `pool` or `pool_source` is needed, but not both.

## The Steps

### select

| Field | What it is |
|---|---|
| `sql` | the query: `SELECT`, `SHOW`, `DESCRIBE` or `EXPLAIN` |
| `params` | the values for the `:NAMES` of the query |
| `name` | the name of the step; the file of a heavy step is named by it |
| `big` | lift the limit of 5000 rows |
| `output` | a path on this computer: the rows are written there and not into the answer |

The whole text of the query is checked, after the leading comments: it must read. An `EXPLAIN` of a writing command and `SELECT … INTO OUTFILE` refuse — the first runs the command in order to show the plan, the second writes a file on the server.

The step answers `rows` — how many rows, and `data` — the rows themselves as objects.

### dependent

| Field | What it is |
|---|---|
| `table` | the main table |
| `ids` | the ids of the rows of the main table — to count how many rows point at them |

There are no foreign keys in the store's database: which table points at which is written down in the engine by hand, and the step reads that list.

The step answers `depend` — a row per relation: `main` — the main table, `table` — the dependent one, `key` — its field that points at a row of the main one, `nullable` — whether it may stand empty. With `ids`, `count` is added — how many rows of each dependent table point at these ids.

* **`count` is a forecast, not a report:** how many rows will hang loose if these ids are
  deleted. The rows that have been left without an owner already it does not see. The tables
  where not a single row points at these ids do not get into `count`: an empty `count` means
  that nothing hangs loose.
* **An empty `depend`** means that no relations are written down, not that there are no
  dependent tables.
* **One table can be tied twice** — `user_task` to `user` through `user_id` and `exec_id` —
  so `count` goes by tables and not by relations.

## The Answer

```json
{"ok": true,
 "steps": [{"num": 1, "do": "select", "rows": 2,
            "data": [{"id": "1", "name": "Root"}, {"id": "2", "name": "Laptops"}]}],
 "changed": [], "unlocked": [], "ms": 10}
```

| Field | What it is |
|---|---|
| `ok` | whether the pool reached the end |
| `steps` | the steps in order: the number `num`, the step `do`, `name` if there was one, and the answer of the step |
| `changed`, `unlocked` | with reading they are always empty |
| `ms` | how long the call lasted on the server |

A heavy step carries `file` and `head` instead of `data` — see "[The Database](engine_db.md)".

## Refusals

A step that did not pass stops the pool. The answer comes with a mark of error and the same JSON; that step has the field `error` with the reason, the steps before it are carried out.

| The step's `error` | When |
|---|---|
| `This command runs select and dependent steps only` | the step is neither `select` nor `dependent` |
| `This command reads only, and this step starts with …` | the query does not read |
| `An EXPLAIN of a … may run it; this command reads only` | an `EXPLAIN` of a writing command |
| `INTO OUTFILE writes a file on the server; this command reads only` | the query writes a file on the server |
| `Too many rows: …. Narrow the query, or repeat this step with "big": true` | more than 5000 rows without `big` |
| `Unknown table: …` | a table that does not exist is named in `table` |
| `The id of this step takes a whole number, and […] is not one` | `ids` holds something other than a whole number |
| `No step named … before this one` | `@name` refers to a step that is not there earlier |
| the text of the database's error | an error in the query |

Refusals before the pool starts:

| Answer | When |
|---|---|
| `pool is required: …` | there is neither `pool` nor `pool_source` |
| `pool or pool_source, not both…` | both are named |
| `No such file: …` | the file of `pool_source` is not there |
| `… is not utf-8 text - save it in utf-8.` | the file of the pool is not in UTF-8, or holds a zero byte |
| `… is not whole json: it breaks at byte …` | the file of the pool is cut off or spoiled |
| `… holds no list of steps…` | the file holds no list of steps |

The common refusals — "[Answers and Refusals](answers.md)".
