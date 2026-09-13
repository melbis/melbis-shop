# The local base of the Store

The Program keeps a Firebird base in the local folder of the Store —
`DATABASE.FDB` — and works out of it: what a manager has downloaded from the server, and what they have changed since.
Until they press Save, those changes live here and nowhere else. That is the whole
reason to read it — the server cannot tell you what has not been sent to it yet.

## Never the original, always a copy

The engine is Firebird 2.1 embedded and it takes the file exclusively: while the
Program holds `DATABASE.FDB`, nothing else attaches to it. So the file is copied first
and the copy is what you open:

```
copy "DATABASE.FDB" "<your folder>\local.fdb"
```

Copying the file of a living base is safe for the original — nothing is written back
into it. The copy can catch a moment of writing; then Firebird recovers it as you
attach, and if it cannot, take the copy again. Read the copy and never write into it:
writing there is a fight with the Program over the same rows.

## Reading it

`isql` travels with the Distribution: it lies in its root, next to the Program itself
and next to the engine it runs on. `<install>` below is that folder — the one the
instructions of this server name for you. If it is not there, the FireBird component
was left out of the install, and that is for the owner to add.

```
<install>\isql.exe -user SYSDBA -password masterkey -i q.sql <your folder>\local.fdb
```

The script is plain SQL. `SET LIST ON;` puts one column on a line, `SET BLOB ALL;` is
what makes a text field show its text instead of its number. The dialect is Firebird,
not MySQL: `FIRST 10` where you would write `LIMIT 10`, and `RDB$DATABASE` for the
one-row table an expression needs.

The answer comes back in the charset of the Program, and it is not the same
everywhere: `<install>\MShop.ini`, section `[Primary]`, key `Charset` — `WIN1251`
there means windows-1251. Decode by what it says, never by a guess.

## What lies inside

The shape of every table is in `../Install.sql`, the very file the
Program builds the base with; the comments beside the domains say what each type
becomes on the server.

Which table belongs to which window, and what the window does with it, is in the map
of the windows: `../MShop/_common.md` for the local base and the three models of
exchange, and the page of the window itself — `../MShop/FPriceManager.md` and its
kind — for the working set it loads and the command it saves with.

Two things to know before the first query:

- these are not the tables of the server. Reference data downloaded from it is
  mirrored as `CUT_*`; a bulk window keeps a working set of its own — `PRICE_*` for
  the prices, `U_*` for the descriptions — and `IMP_*`, `MERGE_*`, `REMOVE_*` belong
  to the steps that carry their names.
- a tree here has no `PARENT_ID`. It stands on `TINDEX`, `TLEVEL` and `ABSINDEX`, the
  order of a walk, exactly as the trees of the server do.

## What has not been sent

The marks are columns like `WAS_UPDATE`, `HAS_CHANGES` and `WAS_EDIT`; which table
carries which is in `Install.sql`. A row wearing one of them is changed here and not
on the server. A row added by hand shows an id far above its neighbours — the local
generator hands out its own numbers.

Weigh it against the server before you tell anyone anything: `engine_db_select` reads
the same row by its id, and the difference between the two answers is exactly what the
manager has not saved yet.
