# The map of the Store

Four MCP-tools, four parts. Each is read from the server on the first call and lives
in the memory of the session after that; the `reload` argument reads it again —
needed when the files were changed from outside, from the Program.

The map is **not read** when you connect. A session that only reads memory or runs
an AI-tool has no use for it, and for a User without the right to the tables it
would not open anyway.

## `engine_map_tree` — what the Store is made of

One flat list: modules, files, folders, images, server journals. The `path` argument
narrows it down by a substring.

```
unit ./../units/melbis_cataloge.php  cache=1 entry=0 lazy=0 tables=store=1|topic=1 includes=
dir  ./../templates/default/statics/base  kind=6
file ./../templates/default/statics/base/app.css  kind=4
log  ./../core/log/melbis/back.log  2026-08-07 14:37:08  119 KB
```

The first word says what it is: `unit` a module, `file` a file, `dir` a folder,
`log` a journal. A module carries its manifest in the tail, a file and a folder
their kind, a journal the time of its last line and its size — by which you can see
where to look before loading anything.

**Start here when the Store is unfamiliar.** That one list shows the set of modules,
which of them are cached, which template sets exist, and whether journals are being
kept.

The tree re-reads itself after every command that created, renamed or removed
something — and after a save that carried a manifest, because the manifest is
printed in these very lines. After a change to the contents of a file alone the tree
does not change and there is nothing to re-read.

## `engine_map_units` — what the modules can do

Function signatures: the name of the module, the name of the function, its
arguments. The `source` argument narrows it to one unit, named without `.php`.

```
melbis_cataloge: MELBIS_CATALOGE($mVars)
melbis_inc_auth: MELBIS_INC_AUTH_command($mUserId, $mCommand)
```

From here you can see what may be called out of a library without reading the whole
of it.

## `engine_db_tables` — how the database is built

Columns with their types, the keys, the storage engine, the mark of a generator.
Needed every time before a query to an unfamiliar table: guessing field names in
Melbis is a lost cause, the names there are historical.

With no `name` it answers the list of the tables and their count; with a name, the
columns of that one. The name may be the real one (`ms_store`), the bare one
(`store`) or the manifest form (`_store`) — all three find the same table, and a
miss comes back with the names that look similar.

What a column **means** the map does not say — for that go to the descriptions of
the tables: `../Tables/<bare>.md` from the folder of these notes, one
file per table.

## `engine_dev_config` — the parameters of the Store

The constants of `config.json`: the nick of the database, the encoding, the time
zone, the storage engine, the language. Passwords and keys come back as `<hidden>` —
and the file itself does not open through the file commands at all.

The code of the debugger is one of the hidden ones, and `shop_page` with `debug=true`
takes it from here by itself — that is why the call asks for the right over the
configuration, see [storefront.md](storefront.md).

## `engine_search` — where something occurs

Not a part of the map but its neighbour in the work: the map says what the Store
has, the search says which files a word occurs in. The word goes in `keyword`: a
tag, the name of a function, a class in the markup, a scrap of text from the
storefront — faster than reading module after module.

```
./../units/melbis_cataloge.php
    {MELBIS:melbis_goods_list([ID])}
```

The path and every line that matched, and the number of files at the end. Case is
ignored by default; for an exact match, `sensitive: true`.

**It does not look everywhere**, which is worth keeping in mind when you read an
empty answer:

| Looks in | Does not look in |
|---|---|
| `units/*.php` and the view folders `templates/*/units/<module>/*` | the `.json` manifests of the modules |
| the root `.php` files and `.htaccess` | `core/` — the engine and its journals |
| `css` and `js` from `templates/*/statics/` of every set | images, fonts, built bundles |

So "nothing was found" means "not in those places". A setting being looked for is
more often in a manifest or in the database than in the code: the manifest is
printed by `engine_map_tree`, the database is read by a pool of queries.

## The shape of a path

The map prints paths with a `./../` prefix — that is how the engine names them. In
the arguments of the tools the prefix is optional: `units/foo.php` and
`./../units/foo.php` are the same thing. A path copied out of the map works exactly
as copied.
