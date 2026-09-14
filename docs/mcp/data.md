# Data: the pool, locks, trees

Everything to do with data goes through one mechanism — a pool of steps. Two tools:
**`engine_db_select`** only reads, **`engine_db_execute`** can do everything. They
are different rights, and the owner may grant the first without the second.

A third, **`engine_db_locks`**, shows who has taken tables into work.

Files hanging on rows are in [elements.md](elements.md).

## Why it is one call

The pool runs its steps one after another **in a single connection to the database**.
That is not a convenience but a necessity: a temporary table and `LAST_INSERT_ID()`
live exactly as long as the connection, and separate calls cannot put them together.

```json
{"pool": [
  {"do": "generate", "table": "store", "name": "new_id"},
  {"do": "insert",   "table": "store",
                     "rows": [{"id": "@new_id", "name": "Product", "code_shop": "A-1"}]},
  {"do": "select",   "sql": "SELECT id, name FROM {DBNICK}_store WHERE id = :ID",
                     "params": {"id": "@new_id"}},
  {"do": "change",   "tables": ["store"]}
]}
```

| Step | What it does |
|---|---|
| `lock` | takes tables into work — the Melbis registry, not a lock of the DBMS |
| `unlock` | releases; with no argument, everything this pool took |
| `generate` | the next id from the table's generator |
| `select` | the rows of a query |
| `modify` | any query: UPDATE, DELETE, DDL, whatever |
| `insert` | a table and an array of rows, built as **one** multi-row INSERT |
| `tree_add` | a new node under `parent_id` (0 is the top level), the id in the answer |
| `tree_move` | moves a node under another parent, its subtree with it |
| `tree_up` / `tree_down` | shifts a node among its neighbours |
| `tree_remove` | deletes a node **together with its subtree**, the row count in the answer |
| `dependent` | what hangs on a table, and how many rows point at the ids named |
| `clear_dependent` | sweeps the rows of dependent tables left without an owner |
| `change` | marks tables as changed so the storefront refreshes its cache |

`engine_db_select` takes **`select` and `dependent` steps only** — the two that read;
any other step is refused by name, and the text of the sql is weighed too, past any
leading comment, so a write cannot travel in disguised as a read. Weighed means the
whole of it: an `EXPLAIN` is a read of a plan, but of an `UPDATE` or a `DELETE` the
server reads that plan by running them, so it is refused as well, and so is a `SELECT`
that ends `INTO OUTFILE`. A table the Store does not have is refused by name too,
whichever step named it.

**An id is a number or nothing.** The `id` and the `parent_id` of the tree steps take
a whole number or an `@name`; a word is refused by name and never quietly read as a
zero, because zero is a place — the top of the tree — and not a guess at what was
meant.

## Values and references between steps

**A value belongs in `params`, not in the text of the query.** In the query write
`:NAME` in upper case and put the value in `params` under that name in any case you
like. That is what keeps a quote, an apostrophe or an alphabet of its own from
breaking the query.

**`@name`** substitutes the result of an earlier step carrying that `name`: the id
from a generator, the `insert_id` of an insert, the id of a new node from `tree_add`.
It works in `params`, in the values of `insert` rows, and in the `id`/`parent_id` of
the tree steps. Only those values are lent — a named `select` step lends nothing.

**The name of a table** is written as `{DBNICK}_store` or straight as `ms_store` —
both are taken. The first form is better: such a query moves into the code of a
module without a single edit.

**The text of a step reaches the base as it is written** — one statement to a step:
a closing `;` does no harm, a second statement is a syntax error. Two things are
done to it on the way, and only two. `{DBNICK}` is replaced wherever it stands,
inside a quoted string too, as the installer does with its scripts. And `:NAME`
becomes a parameter only when `params` carry that name — but then everywhere, so the
same word inside a quoted string breaks the step. Without `params` nothing is
touched: `':ID'` stays `:ID`. Escapes are the database's own: `\'`, `\\`, `''`. So a
line of an SQL script, `core/init/agent_tool.sql` for one, runs as a `modify` step
just as it stands — nothing to reparse, nothing to retype.

## Locks

Melbis **does not use the locks of the DBMS**. Taking a table into work is a row in
the service table `oper_block`: which table, by which operation, by which user,
since when. The DBMS will not stop somebody else's `UPDATE` — it will simply spoil
the unfinished work of a person. So the checking is on you, and there is one
strategy, in three steps:

1. **Before working with tables — reading included — look at `engine_db_locks`.** A
   table in work means a person is changing its data right now, and a read may catch
   it half way. If it is busy, warn the User: who holds it, by which
   operation, since when. A read itself needs no lock, and there is no need to ask
   permission for one.
2. **Before a change, ask the User whether to take the tables into work for the
   time of the edit.** Do not decide that alone — they know who else is working in
   the Store.
3. **Compose the pool so that the lock lives as briefly as possible.** Everything
   that can be read and computed goes into a separate read beforehand; the pool with
   the lock is short: `lock` → the change → `change`, nothing else inside. It does
   not outlive the pool anyway: what a pool took it releases itself, which is why no
   `unlock` is put at the end — it is only there to release something earlier.

`engine_db_locks` shows your locks and every lock on the shared tables:

| `user_name` | `oper_comm` | What it is |
|---|---|---|
| `MELBIS SYSTEM` | `MELBIS_SYSTEM` | the storefront: the parser locked the table from a script |
| a real person | `MELBIS_SYSTEM` | a lock taken outside an operation: a manual one from the Program — or one of your own, from a `lock` step or from adding a file |
| a real person | the name of a command | an ordinary operation: the whole group of tables of that operation is busy |

**Your locks look like theirs.** You work under the login of the person at the
keyboard, so in that list your row cannot be told from the row of their open form by
the name — only by the operation and the time.

Hence the rules of the engine, hard on both sides. The `lock` step turns away
**any** busy table — the one held by a form in the Program under your own login
included, and your own stuck one too. The `unlock` step releases **only what this
pool took**; a foreign table named there gets the same refusal. There is nothing to
get around this with, and no need: a busy table means a person is editing its data
right now.

If you get `Table is busy`, look at `engine_db_locks`, tell the owner who holds the
table, by which operation and since when, and wait for a decision. A person's lock is
lifted by them, in the Program, where every lock is listed.

One kind is yours to lift: an AI-tool that died between taking a table and
letting it go leaves a row under **your own login**, outside any operation — in the
list it reads as your name with `MELBIS_SYSTEM`. It never expires by itself.
`engine_db_unlocks` releases those, every one of them or only the tables named. It
reaches nothing else: not a lock of the storefront, not another person's, not an open
form's — a form names its operation, and rows with an operation are never touched.

Three things before you call it. A row of a second ago may belong to a call that is
still running, so read `engine_db_locks` first and look at the time. Your own manual
lock from the Program looks exactly the same — if you took a table in the interface on
purpose, this command lets it go too. And the tool stands on a right of its own,
separate from the one that lets you read the list: if it was not granted, it is not in
your list at all, and then even your own stuck row is lifted by the owner, in the
Program, like any other.

In the `table` and `tables` **fields** of the steps, and in the `tables` of
`engine_db_unlocks`, a table is named bare: `store`. The real `ms_store` is taken
there as well, but `{DBNICK}_` is not resolved in those fields and is refused. In the
text of a query it is the other way round — see "Values and references between steps"
above.

## Trees

There are more than a dozen trees: the catalogue `topic`, the dictionaries `info`,
the alternative catalogues `topic_alt`, the characteristics `*_key`, the reports, the
translations — any table with the fields `tindex`, `tlevel`, `absindex`.

**The shape of a tree is changed by the `tree_*` steps alone.** They recompute those
three fields across the whole table, and a plain INSERT/UPDATE/DELETE cannot repeat
it. The service trees (`oper`, `orders_client_field`, `u_*`) are closed to the steps,
and so is any table that is not a tree.

- A tree step **marks its own table** for the cache — it does not wait for a
  `change`. Your own `modify` steps that follow obey the usual rule.
- `tree_add` creates an **empty row**: only the id and the place in the tree. Fill
  the fields with the next `modify`, by the borrowed id.
- **A new `topic` or `info` node is seen by the administrator only** while the node
  has no rows in `topic_right`/`info_right`. If people are to see it, insert the
  rights in the same pool, usually as copies of the parent's rows.
- The root of the catalogue is `id = 1`. `parent_id = 0` puts a node on the top
  level; for `topic` there is no reason to do that, while the first node of an empty
  `info` is created exactly so.

```json
{"pool": [
  {"do": "tree_add", "table": "topic", "parent_id": 1, "name": "a"},
  {"do": "modify",   "sql": "UPDATE {DBNICK}_topic SET name = :NAME, kind_key = 'kGoods', seo_psu = :PSU WHERE id = :ID",
                     "params": {"name": "Laptops", "psu": "laptops", "id": "@a"}},
  {"do": "change",   "tables": ["topic"]}
]}
```

## What hangs on what

The base of a Store keeps **no foreign keys**: what points at what is written down
inside the engine, by hand. The `dependent` step reads that list out, so a deletion is
planned by knowing instead of by guessing.

```json
{"pool": [
  {"do": "dependent", "table": "topic"},
  {"do": "dependent", "table": "topic", "ids": [12, 15]}
]}
```

A step answers `depend` — a row per tie: the `main` table, the `table` hanging on it,
the `key` that names the row there, and whether that key may stand empty. Where `ids`
are named there is a `count` as well: how many rows of each dependent table point at
them.

- **`count` is a forecast, not a report.** It counts the rows pointing at those ids
  while they are all still there — what will be left hanging if you delete. What is
  orphaned in the table already it does not see; that is the business of the sweep.
- **`nullable` says one thing:** a row whose key stands empty hangs on nobody by choice,
  and the sweep spares it. A row whose key names a row that is gone is swept whatever
  the flag says.
- **An empty `depend` means no tie is declared**, not that nothing can be orphaned. The
  list is written by hand, and a table of the Store's own is on it only if the Store
  put it there.
- One table may be tied twice — `user_task` hangs on `user` by `user_id` and by
  `exec_id` — and that is why `count` goes by table and not by tie.

The step only reads and takes no lock, so it belongs in `engine_db_select` as much as in
`engine_db_execute`.

## Sweeping up after deletions

After `tree_remove`, and after mass deletions in general, call `clear_dependent`
with the name of the main table: it sweeps the rows of the dependent tables that
were left without an owner, following the chain — `info` drags the tails of
`info_value` with it. The answer says how many rows were taken off which table. The
tables of people, `user` and `user_group`, are closed to the sweep. What that sweep
will find can be asked before the deletion, by the `dependent` step above.

**The sweep does not work on a busy table**, and a table taken by **this same pool**
is busy too. So `clear_dependent` refuses to run while the pool holds anything, and
says so: put it after the `unlock` step, or run it in a pool of its own. A dependent
table held by somebody else it skips and names — you repeat that later.

## What the pool does not do by itself

**It does not guess which tables to refresh.** A table you touched with raw sql is
refreshed by the `change` step alone — the tree steps mark their own table
themselves, and the sweep marks every table it cleared. Forget the step and the storefront
goes on serving the old page, and the mistake will be a silent one. The marks are
put once, at the end, so a thousand inserts cost one mark.

**It does not roll back.** There is no transaction: DDL such as `CREATE TEMPORARY
TABLE` commits implicitly anyway, so an "atomic pool" would be a promise that cannot
be kept. An error stops the pool, but what was done before it stays done.

## How to read the answer

```json
{"ok": false,
 "steps": [
   {"num": 1, "do": "lock", "tables": ["store"]},
   {"num": 2, "do": "generate", "value": 4187, "name": "new_id"},
   {"num": 3, "do": "insert", "error": "Duplicate entry '4187' for key 'PRIMARY'"}
 ],
 "changed": [], "unlocked": ["store"]}
```

- `ok` — whether the pool reached the end;
- per step: `rows` and `data` for a read, `affected` and `insert_id` for a write,
  `value` for the generator, `depend` and `count` for the map, `cleared` for the sweep,
  `error` on the one that stopped;
- `changed` — what was marked, `unlocked` — what was released.

An SQL error arrives **as the text of its step**, not as the death of the request:
you see which step, what the database said, and what managed to run before it. The
locks this pool took are released even then — but only on a soft error. After a hard
fall the lock stays: the next `lock` bounces off it exactly as off somebody else's. It
is one of yours, though — the same row a dead tool leaves, under your own login and
outside any operation. Read `engine_db_locks` first and look at the time, in case the
call is still running, then lift it with `engine_db_unlocks` — and if that tool is not
in your list, the row goes to the owner like any other, see "Locks" above.

## Volume

Values come whole, with as many columns as there are. But a step that returned more
than **5000 rows** is turned away with a request to narrow the query: a million rows
is not a slow answer but exhausted memory and an incoherent error. If you really do
need them all, repeat the step with `"big": true`.

**A heavy step does not come here, it goes to a file.** Heavy is what you called
`big`, and what grew past 100 000 bytes without saying so. Its rows are written to
`mcp\melbis\queries\<date_time>.<name of the step>.jsonl` — one json object to a
line — and in the answer that step keeps `rows`, the path in `file`, and `head`,
three first lines clipped at 300 characters to show the shape. Give a step a `name`
and the file is named by it; without one it is named `step<number>`. Everything else of the answer stands as it was: a light
step still carries its `data`, and so the shape of a pool never changes with its size.

That file is the same jsonl the tools write, so it feeds them back: any parameter of
`tool_run` takes `{"file": "..."}`, and the rows go from a query into a change without
passing through this conversation.

**A pool too big to write out comes as a file too.** Hundreds of rows to insert are
not typed into a call: build the pool with a script on this machine — the very list
of steps `pool` takes — and give its path as `pool_file` instead, absolute or counted
from the local folder of the Store. It travels whole, and nothing of it passes
through this conversation. One `insert` holds at most **65 535 values**, rows times
columns — a ceiling of the database, not of the engine — so a bigger load is several
`insert` steps of the same pool.

`big` lifts the guard, not the memory of the server: a whole wide table read in one
step (fifty thousand rows of `store`, say) ends as *Allowed memory size exhausted* —
the engine builds the whole answer before sending it. Take such a table in pages by
`LIMIT`, or through the AI-tool that owns it. Two things about paging it yourself:

- **`ORDER BY` is not optional.** Without it the order is nothing the base promised,
  and two pages of the same query will repeat rows and drop others.
- **Page by the key, not by `OFFSET`**: `WHERE id > :LAST ORDER BY id LIMIT 2000`,
  carrying the last id over. `OFFSET 40000` makes the base walk forty thousand rows
  again on every page.

Each page is a call and so a file of its own, named by the time it came. Glue them
when the walk is done — that is `cat` on this machine, not a matter for the engine.

## The order when changing data

0. Check whether the entity is covered by an AI-tool ([tools.md](tools.md)): if it
   is, work with the AI-tool and not with a pool, even where the rights allow both.
1. Tell the owner what you are about to change, and ask whether to take the tables
   into work for the time of the edit. This is a warning of its own, not the same
   one as for editing files.
2. If the change could not be put back by hand, take a snapshot of the rows first —
   "A snapshot before a change" below.
3. Look at `engine_db_locks` — whether the table is busy.
4. The pool: the work → `change`. If you agreed to lock, in the same pool: `lock` as
   the first step. There is no point putting `unlock` last, the pool releases
   everything it took; it is only there to release **earlier** than the end — before
   a `clear_dependent`, for instance, which does not work on a busy table.
5. If a sweep is needed, in a separate pool afterwards.

**The personal tables of the users (`u_*`) and the generator `generator_u` — do not
touch.** Product descriptions are put together there; it is a person's private area.

A special case: **`store_clann`** — there is no table by that name, this counter
issues the id of a product's "clan", which is kept in the `clann` field of `store`.
A table has a generator if `engine_db_tables` shows a `gen` line for it; if it does
not, the id is `AUTO_INCREMENT` and the DBMS issues it itself.

## A snapshot before a change

Before a change you could not put back by hand — a mass UPDATE or DELETE, a DDL, a
sweep — read the rows you are about to touch into a file of your own. A step that
names `file` writes its rows there, one json object to a line, **at any size**, and
keeps them out of the answer:

```json
{"do":"select","name":"prices","file":"mcp/<agent>/before-price-fix.jsonl",
 "sql":"SELECT * FROM {DBNICK}_store WHERE topic_id = :TOPIC"}
```

The path is absolute or counted from the local folder of the Store. Put it in **your
own** folder: everything under `mcp/melbis/` is expendable and is wiped without
warning.

**Reading it back is your own business, and so is putting it back.** No MCP-tool
reads such a file into an answer: the Host you run in does the reading — a script, a
grep. The pool that returns the rows you build yourself from what you read, and when
it is too big to write out, the script that builds it writes it to a file for
`pool_file` — see "Volume" above. Nearly every Host can do that.

If yours cannot touch local files at all, there is a fallback, and it is a heavier
thing: a copy of the rows in a **real table of the base**, made with `CREATE TABLE
... LIKE` and `INSERT ... SELECT` in one pool. Not a `TEMPORARY` one — that lives in
the connection, and the connection is one call: the next pool will not see it. A
real table stays, and the return is one `UPDATE ... JOIN` or `REPLACE INTO ... SELECT`
on the server, nothing read from disk. But it is a table in a working base: it shows
in every list, goes into every dump, and forgotten is litter. So only when the User
insists on it, with a name that says what it is and carries the date, dropped the
moment the change is accepted — and the owner told that it exists and when it goes.

Either way, know what you have: a copy to compare with and to return from **by your
own hands**, not an undo. When a change is one that might genuinely have to be
reversed, say so before you start and ask the owner for **their** backup, the one the
Program makes; nothing of yours replaces it.

And a snapshot goes stale at once — not because the file disappears, though the local
folder does go with the machine, but because people keep working in the Store. An
hour later, putting those rows back would wipe what they have done in the meantime.

**Not with `shop_download`.** Its `action: "database"` is a dump of the whole base and
it travels whole; on a working Store that is the first thing to fall over. How far
from "working" this Store is, you can see for yourself:

```sql
SELECT TABLE_NAME, TABLE_ROWS, ROUND((DATA_LENGTH+INDEX_LENGTH)/1048576) AS mb
FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()
ORDER BY DATA_LENGTH+INDEX_LENGTH DESC
```
