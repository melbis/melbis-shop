# engine_db_execute

Changes data as a pool of steps: any queries, inserts, tree nodes, the sweep and the marks for the storefront's cache. The reading steps `select` and `dependent` work here too.

## The Right

"Direct access → Database → Modify data" (`AGENT_ENGINE_DB_EXECUTE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| [`pool`] | list of objects | the steps of the pool — see "[The Database](engine_db.md)" |
| [`pool_source`] | string | a JSON file on this computer with the list of steps — an absolute path or a path from the store folder — instead of `pool` |

Either `pool` or `pool_source` is needed, but not both.

## The Steps

| Step | Fields | What it does | The step's answer |
|---|---|---|---|
| `lock` | `tables` | takes the tables into work — all at once or none | `tables` |
| `unlock` | `tables`; without them — everything the pool has taken | releases what this pool took earlier, before the end | `tables` |
| `generate` | `table` | the next id from the table's generator | `value` |
| `select` | as at [`engine_db_select`](engine_db_select.md), without the check for reading | the rows of the query | `rows`, `data` |
| `modify` | `sql`, `params` | any query: `UPDATE`, `DELETE`, DDL | `affected`, `insert_id` |
| `insert` | `table`, `rows` | inserts the rows with one multi-row `INSERT` | `affected`, `insert_id` |
| `tree_add` | `table`, `parent_id` | adds a node of a tree | `value` — the id of the node |
| `tree_move` | `table`, `id`, `parent_id` | moves a node with its subtree under another parent | — |
| `tree_up`, `tree_down` | `table`, `id` | shifts a node among its neighbours | — |
| `tree_remove` | `table`, `id` | deletes a node **together with its subtree** | `deleted` — how many rows |
| `dependent` | `table`, `ids` | as at [`engine_db_select`](engine_db_select.md) | `depend`, `count` |
| `clear_dependent` | `table` | sweeps away the rows of the dependent tables left without an owner | `cleared` |
| `change` | `tables` | marks the tables as changed | `tables` |

Any step can have a `name`: then its `value` or `insert_id` is lent to the following steps as `@name`.

**`generate`** takes an id from the generator. A generator belongs to the tables for which `engine_db_tables` shows `gen`; for the rest the id is issued by the DBMS itself through `AUTO_INCREMENT`, and the step refuses for them. There is a generator without a table too: `store_clann` issues the number of a product's clan for the field `clann` of the table `store`.

**`insert`** takes `rows` — a list of rows, each an object with the same columns. One `INSERT` holds no more than 65 535 values — the rows multiplied by the columns; more than that goes in several `insert` steps.

**The `id` and the `parent_id`** of the tree steps are a whole number or `@name`. A word refuses and is not read as a zero: a zero is the top of the tree.

## Trees

A tree is any table with the fields `tindex`, `tlevel` and `absindex`: the catalog `topic`, the reference directories `info`, the alternative catalogs `topic_alt`, the attributes `*_key` and others.

* **The shape of a tree is changed by the `tree_*` steps only.** They recount these three fields
  over the whole table, and an ordinary `INSERT`, `UPDATE` or `DELETE` will not repeat that.
* **The service trees are closed:** `oper`, `orders_client_field` and the users' personal tables
  `u_*`.
* **`tree_add` creates an empty row** — only the id and the place in the tree. The fields are
  filled by the `modify` that follows, by the lent id.
* **A new node of `topic` or `info` is seen by the administrator only,** while the node has no
  rows in `topic_right` or `info_right`.
* **The root of the catalog** is `id = 1`. `parent_id = 0` puts a node on the top level; that is
  how the first node of an empty reference directory `info` is created.
* **A tree step marks its own table** for the cache.

## The Sweep

`clear_dependent` sweeps away the rows of the dependent tables that have been left without an owner, and goes on along the chain: after `info` the tails of `info_value` are cleaned. The answer `cleared` names, per table, how many rows were taken off; a busy table the step passes by and writes `busy` at it.

* **A row with an empty key**, if the field may stand empty, points at nobody by design, and the
  sweep does not touch it.
* **The tables of people** `user` and `user_group` are closed to the sweep.
* **While the pool holds tables, the sweep refuses:** the tables it has taken are busy too. It
  is put after `unlock` or run as a separate pool.
* **The sweep itself marks** the tables it has cleaned.

## The Marks for the Cache

The storefront learns of a change to a table by a mark. The `change` step marks the named tables; the tree steps and the sweep mark their own themselves. The marks are put once at the end of the pool — a thousand inserts costs one mark. A table changed by a query without `change` is not marked, and the storefront goes on giving out the old.

## Locks in a Pool

* **`lock`** takes all the named tables or none. A lock it waits out for a moment, and a table
  busy longer it refuses — even if it is a window of the program under the same login that
  holds it.
* **`unlock`** releases only what this pool has taken.
* **At the end the pool releases everything it has taken** — an `unlock` as the last step is not
  needed. What has been released is listed in `unlocked`.

## The Answer

```json
{"ok": false,
 "steps": [
   {"num": 1, "do": "lock", "tables": ["store"]},
   {"num": 2, "do": "generate", "value": 4187, "name": "new_id"},
   {"num": 3, "do": "insert", "error": "Duplicate entry '4187' for key 'PRIMARY'"}
 ],
 "changed": [], "unlocked": ["store"], "ms": 14}
```

| Field | What it is |
|---|---|
| `ok` | whether the pool reached the end |
| `steps` | the steps in order: the number `num`, the step `do`, `name` if there was one, the answer of the step or `error` at the one that stopped |
| `changed` | which tables are marked for the cache |
| `unlocked` | which tables the pool has released |
| `ms` | how long the call lasted on the server |

## Refusals

A step that did not pass stops the pool. The answer comes with a mark of error and the same JSON; the steps before the one that stopped are carried out, their changes stay.

| The step's `error` | When |
|---|---|
| `Table is busy: … - the lock is listed in the program and lifted there` | `lock`: the table is busy |
| `Not locked by this pool: …` | `unlock`: the table was not taken by this pool |
| `Unknown table: …` | a table that does not exist is named |
| `No generator for: … - a table without one takes its id from AUTO_INCREMENT` | `generate`: there is no generator with that name |
| `Nothing to insert: rows is empty`, `rows takes a list of rows…`, `Row … has other columns than the first one` | `insert`: the rows are not of the right shape |
| `This tree is not for the agent: …` | a service tree |
| `Not a tree table: …` | the table has no tree fields |
| `No parent … in …`, `No node … in …` | there is no such node |
| `No such node, or the parent sits inside the moved branch` | `tree_move`: there is no node, or the new parent is inside the branch being moved |
| `The id of this step takes a whole number, and […] is not one`, `The parent_id of this step…` | the `id` or the `parent_id` is not a whole number |
| `This sweep is not for the agent: …` | a sweep of `user` or `user_group` |
| `Unlock before this sweep: …` | a sweep while the pool holds tables |
| `No dependent list for …` | the table has no dependents written down |
| `No step named … before this one` | `@name` refers to a step that is not there earlier |
| `Unknown step: …` | there is no such step |
| the text of the database's error | an error in the query |

The refusals before the pool starts are the same as at [`engine_db_select`](engine_db_select.md).

The common refusals — "[Answers and Refusals](answers.md)".
