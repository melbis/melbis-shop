# The Database

The `engine_db_*` section reads and changes the store's data right in its database, bypassing the program's windows.

## The Tools

| Tool | What it does |
|---|---|
| `engine_db_tables` | the structure of the tables: columns, keys, the generator |
| `engine_db_select` | reads data as a pool of steps |
| `engine_db_execute` | changes data as a pool of steps |
| `engine_db_locks` | who has taken tables into work |
| `engine_db_unlocks` | lifts the locks that hang under one's own login |

Reading and changing are different rights: the owner can grant `engine_db_select` without `engine_db_execute`.

## A Pool of Steps

All work with data goes as a pool — a list of steps that the engine carries out in order **in one connection to the database**. Only that way do a temporary table and `LAST_INSERT_ID()` live on from step to step: separate calls will not join them.

```json
{"pool": [
  {"do": "generate", "table": "store", "name": "new_id"},
  {"do": "insert", "table": "store", "rows": [{"id": "@new_id", "name": "Product"}]},
  {"do": "change", "tables": ["store"]}
]}
```

`engine_db_select` takes the reading steps only, `engine_db_execute` — any of them; the steps themselves are described on the pages [`engine_db_select`](engine_db_select.md) and [`engine_db_execute`](engine_db_execute.md).

**The values go into `params`.** In the text of the query one writes `:NAME` in capital letters, and puts the value into `params` under that name in any case. That way a quote, an apostrophe or a foreign alphabet does not break the query.

**`@name`** substitutes the result of an earlier step with that `name`: an id from the generator, the `insert_id` of an insert, the id of a new tree node. It works in `params`, in the values of the rows of `insert`, in the `id` and `parent_id` of the tree steps and in the `ids` of the `dependent` step. The `select` step lends no values.

**The text of the query goes to the database as it is written** — one command per step. On the way only two things are done to it. `{DBNICK}` is replaced everywhere, even inside a string in quotes. `:NAME` becomes a parameter only if `params` holds such a name — but then everywhere, and the same word inside a string in quotes breaks the step. The escaping is the database's own: `\'`, `\\`, `''`.

**There is no transaction.** An error stops the pool, but what was done before it stays. The tables the pool has taken into work it releases after an SQL error too; but if the query fell as a whole, the lock row stays, and `engine_db_unlocks` lifts it.

**The volume.** A step that has returned more than 5000 rows refuses — narrow the query, or repeat the step with `"big": true`. The rows of a heavy step — one with `big` or with an answer over 100 000 bytes — go into the file `queries\<date_time>.<step name>.jsonl` of the conversation folder, one JSON object per line; in the answer the step leaves `rows`, the path `file` and `head` — the first three lines cut to 300 characters. A step with the field `output` writes the rows into the named file whatever the size. `big` lifts the limit of rows only: a wide table read whole can exhaust the server's memory.

**A large pool** is built by a script on this computer and passed as the file `pool_source` instead of `pool`: an absolute path or a path from the store folder.

## The Names of Tables

* **In the text of the query** a table is written as `{DBNICK}_store` or by its real name
  `ms_store`. The first form is better: such a query moves into the code of a module
  without edits.
* **In the fields `table` and `tables`** — of the pool's steps and of `engine_db_unlocks` —
  a table is called by its bare name `store` or the real one `ms_store`. The form
  `{DBNICK}_store` is not accepted there.

What every field means is described in the `Engine\Tables\` folder of the program — a file per table.

## Locks

Melbis does not use the locks of the DBMS. To take a table into work means to mark it as busy: which table, by which operation, by which user, from which time; these marks are shown by `engine_db_locks`. The DBMS will not stop someone else's query at that, so they check themselves whether a table is busy.

* **Who takes tables.** The program — for the time of an operation or by hand; the storefront —
  for the time of its own write; a pool — by the `lock` step.
* **The `lock` step** waits out a lock for a moment and refuses if the table is still busy —
  even if it is a window of the program under the same login that holds it. Everything the
  pool has taken it releases itself when it finishes.
* **A hanging lock.** If a pool fell hard or an AI tool broke off between taking a table and
  freeing it, the mark stays under the login, outside any operation, and does not expire by
  itself. Such marks are lifted by `engine_db_unlocks`.

In the program all the locks are seen and lifted in the dispatcher — "[Dispatcher and Locks](../User/dispatcher.md#locks)".
