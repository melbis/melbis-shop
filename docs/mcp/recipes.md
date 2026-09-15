# Recipes: the order of steps for typical tasks

Sequences that have been tried. Each of them begins after `session_connect` and none
of them cancels the [order of work](index.md) — the plan to the owner before the
actions, a warning before editing files and a separate one before changing data.

## Looking around an unfamiliar Store

1. `memory_list` and `memory_load` — what you already know about this Store, every
   `kCritical` note first. The notes live inside the Store itself, so they are there
   even in the first session on a new machine.
2. `engine_map_tree` — the files: root scripts, modules, template sets, folders of
   statics.
3. `engine_map_units` — which modules there are and what functions they declare.
4. `engine_db_tables` — the tables of the database: fields, keys, the `gen` mark on
   those that live on a generator.
5. `engine_search(keyword)` — where a name, a tag or a scrap of text occurs in the
   files.

Each part of the map is read from the server on the first call and lives in the memory
of the session after that. If somebody was editing the Store in parallel, call that
part again with `reload`.

The first acquaintance is the longest part of the work: warn the User in advance and
do not skimp on it. It is preparation, and afterwards things go noticeably faster —
especially if what you understood settles into the memory of the Store
(`memory_save`): the next task then starts from the notes instead of from nothing.

## Changing the markup of a module

1. `engine_html_load(path)` — read the whole view.
2. `engine_html_save(path, content)` — write it. **The whole file is overwritten**, it
   is not a patch.
3. `shop_page(path, find=<a piece of the change>)` — make sure the new markup arrived.

There is no cache to clear: a view lies under `units/<module>/`, so saving it drops the
`cache` and the `trick` of that module by itself. If the page still shows the old
thing, the reason is elsewhere — a css inside a bundle, or another module's cache
around this one; then `engine_dev_cache_clear`.

## Seeing what came out

`shop_page(path)` fetches a page of the Store exactly as a visitor sees it: the address
comes from the settings of the connection, and outside addresses are refused. The whole
HTML lands in `mcp\melbis\pages\`, and the answer carries the status, the size, the
`<title>` and either the head of the page or the fragments matching `find` — read the
rest straight from the file with your own tools.

- **`shop_page(path, debug=true)`** adds the report of the parser. The engine gives it
  in a machine form — without the panel drawn for a person: the whole report lands
  beside the page as `*.report.json`, and the answer carries the summary — the build
  time, the number and the time of the SQL, the state of the cache, and a line per
  module: whether the module has a cache, whether it is allowed right now, how many
  queries it ran. The tool fills the debugger code in from the configuration itself; if
  none is set, it says so. Every call stands alone — the mode does not stick to the
  pages that follow.
- A module in the report with `cache on` but `allow false` is a sure sign that the list
  of tables in its manifest does not match what it actually reads.
- You come in as **an anonymous visitor**: no manager's session, no hidden sections.
  And this is markup, not a picture — how it looks is for the owner to say.

## Changing the code of a module

1. `engine_php_load(path)` — the code, and the keys of the manifest in the head of the
   answer.
2. `engine_php_save(path, content)` — the code. If you are also changing the
   parameters or the cache, add `manifest` with the same keys that came in the head.
3. Read the warning line from the linter in the answer: it catches calls to functions
   that were never declared and includes that are not used.

There is no cache to clear here either: saving a `.php` drops the cache of that module,
and saving a library drops the cache of everyone who includes it.

A new table need not be written into `table_info`: the engine will see the query and
add it itself, from the very first render.

## A new module

1. `engine_php_add(path="units/<name>.php")` — creates `units/<name>.php`,
   `units/<name>.json` and the view folder `templates/*/units/<name>/main.htm` **in
   every template set**. The view folders are not created if the **second word** of the
   name is `inc` or `include` (`melbis_inc_auth`), or if the name holds no underscore at
   all. Careful: if a view folder of that name is already there (left over from a
   deleted module), the command **wipes it** before creating.
2. `engine_php_save(path, content, manifest)` — the code and the manifest. The main
   function is named after the file in upper case.
3. `engine_html_save(path, content)` — the markup of the `main` view.
4. Put the call `{MELBIS:<name>([ARG])}` into the template of the module that is to
   show it — and save that template. That save drops the cache of the module the
   template belongs to, so the new tag appears on the storefront at once.

A library module (`<company>_inc_<name>`) needs neither step 3 nor step 4: it has no
markup and is attached through the `includes` of the manifest of whichever module uses
it.

## A new view for an existing module

1. `engine_html_add(path="templates/<set>/units/<module>/extra.htm")` — creates the
   file (and the folder of the module, if it was not there) in the named template set.
2. `engine_html_save` — the contents.
3. Show it: either from the markup with the tag `{^VIEW_NAME}`, or from the code with
   `TplParse`. The first is preferable when the choice is a matter of markup.

Do not forget: there can be several template sets, and the view is needed in every one
where the module is shown.

## Changing a css or a js

1. `engine_static_load(path)` → `engine_static_save(path, content)`.
2. If the file belongs to a bundle, `statics/bundle.<name>` is rebuilt by itself — this
   is the only operation on which the engine rebuilds it.
3. Add `build: true` to the save if the change is meant for the visitors: a browser
   holds the old file until the build number grows.

Statics must not be moved around past the `engine_static_*` commands — the descriptor
of a bundle is tied to the path of its file, see "Bundles" in [files.md](files.md).

## Putting an image or a font into a template

1. `engine_image_dir_add(path)`, if the folder is not there.
2. `engine_image_add(path, file|content)` — from a local path, or as text for an svg.
   An existing name is overwritten irreversibly, so on a taken name the command refuses
   first: warn the owner and repeat with `overwrite=true`.
3. In the markup, refer to it from the root of the site:
   `/templates/<set>/images/<path>`.

There is no cache to clear — these files are served by the web server, not by the
parser.

## Creating a section of the catalogue

One `engine_db_execute`: the node by a `tree_add` step, the fields by the `modify` that
follows, on the borrowed id. `tindex`/`tlevel`/`absindex` are recomputed by the engine
and are not to be touched by hand.

```json
{"pool":[
  {"do":"tree_add","table":"topic","parent_id":1,"name":"a"},
  {"do":"modify","sql":"UPDATE {DBNICK}_topic SET name = :NAME, kind_key = 'kGoods', seo_psu = :PSU WHERE id = :ID",
                 "params":{"name":"Laptops","psu":"laptops","id":"@a"}},
  {"do":"change","tables":["topic"]}
]}
```

- The root of the catalogue is `id = 1`, and it is the parent of the top-level
  sections.
- **A new section is seen by the administrator only** while it has no rows in
  `topic_right` — usually they are copied from the parent in the same pool.
- The order among neighbours is the `tree_up`/`tree_down` steps, moving is `tree_move`,
  deleting with the subtree is `tree_remove` plus `clear_dependent`. What that deletion
  will leave hanging is answered beforehand by a `dependent` step.
- Changing the fields of a section is an ordinary `UPDATE` by id: exactly what you
  named changes.

The same holds for any tree — `info`, `topic_alt`, the characteristics `*_key`: the
details are in [data.md](data.md).

## Attaching a photo to an element

One or two photos onto a ready element go through the direct door; a batch onto a whole
batch of goods goes through an AI-tool — "Doing what the Store can already do" below,
and the `files` field of a Command in [tools.md](tools.md).

1. `engine_files_add` — a list of `{entity, elem_id, file}`; `entity` is the owning
   table (`store` for a product), `file` is a path on this machine. Everything else —
   the id, the name, the folder, the row in `files_<entity>` — the engine does.
2. To see what the element already has: `engine_files_load` with `elem_id`.
3. To take one off: `engine_files_remove` by the `id` from the manifest — **the row**
   is deleted, the file on the disk is swept later by the owner's audit module.

In the markup the path is assembled from the row of the file, not from a hardcoded
folder:

```html
<img src="{IMG:UPLOAD_TIME|path}{IMG:FILE_NAME}">
```

The details and the limits (no editing, no derived copies) are in
[elements.md](elements.md).

## Doing what the Store can already do

Before you assemble the work out of a pool of queries, look at `tool_list`: it may well
be that the owner has already made an AI-tool for it, and then the right answer is to
call it instead of inventing your own way to the same tables.

Study the AI-tools with care: a task often has a short path and a long one. A bulk load
of something is the plain case - one AI-tool takes the whole batch in a call or two,
while the same result assembled row by row costs a round trip per action, and every
round trip costs **you**, not the server. Where the order of steps matters, it is
written in the descriptions of the AI-tool and its Commands - read them before the
walk, and try a few rows before the thousand.

1. `tool_list` — what there is, with the commands and the grant (`may:`).
2. `tool_list(unit)` — the same list deeper: the Commands of one AI-tool and the fields
   of each.
3. `tool_run(unit, command, params)` — the unit, the Command and its parameters.
4. `into` beside them, when the answer runs to more than one page: every page is
   appended to a file of that job, and the walk is read from the file instead of the
   chat.

The details are in [tools.md](tools.md).

**The other side of the same rule.** If no AI-tool was found for this work, do it with
a pool, and then name what it cost: how many queries, how many tables, what had to be
checked by hand. There is no point waiting for the third repetition, the numbers are in
your hands right now, and an AI-tool would open the same work to staff with no rights
to the database. How to say it — "If there is no AI-tool for the job" in
[tools.md](tools.md).

## Reading data

`engine_db_select` with a list of `select` steps — the values come whole, with nothing
cut off and no ceiling on the columns. Several queries at once is normal, they travel in
one call:

```json
{"pool":[
  {"do":"select","sql":"SELECT id, name FROM {DBNICK}_topic ORDER BY absindex"},
  {"do":"select","sql":"SELECT COUNT(*) AS how FROM {DBNICK}_store WHERE no_visible = 0"}
]}
```

A step that would return more than 5000 rows is turned away — narrow the query, or add
`"big": true` to that step if you really do need them all.

## Changing data

1. Warn the owner **separately** — this is not the same as the warning about editing
   files — wait for agreement, and ask at the same time whether to take the tables into
   work for the time of the edit.
2. Check `engine_db_locks`: whether a live manager is holding the table — the User
   with an open form included, their lock standing under the same login as yours. The engine will let you neither take nor release a busy one, so it is better to
   learn that beforehand than in the middle of a pool.
3. One `engine_db_execute` for the whole job:

```json
{"pool":[
  {"do":"generate","table":"store","name":"id"},
  {"do":"insert","table":"store","rows":[{"id":"@id","name":"Product"}]},
  {"do":"change","tables":["store"]}
]}
```

If you agreed to lock, `lock` as the first step of the same pool; a separate `unlock` at
the end is not needed, the pool releases everything it took by itself. And whatever can
be read beforehand, read before the pool, so that the lock lives as briefly as possible.

Before a deletion one step is worth spending: `dependent` — the table, and the `ids` you
are about to take away. It answers what hangs on that table and how many rows point at
those very ids, reads only and takes no lock, so it costs a deletion nothing to be
planned rather than guessed.

Three things that are easy to forget:

- **`change` is compulsory** — without it the storefront goes on serving the old page,
  and nothing will tell you.
- **`@name`** carries an id between steps; there is nothing to substitute by hand.
- **A thousand rows is one `insert` with an array of `rows`**, not a thousand steps: the
  engine builds one multi-row INSERT.

The personal tables of a user (`u_*`) and the generator `generator_u` are not to be
touched.

## Working out why the Store is slow or silent

The order is exactly this: first what you can look at yourself, and only then the
requests to the owner.

1. `shop_page(path)` — whether the Store answers, and with what. An unusual code (403,
   503, a browser-check page) is more likely from Cloudflare or nginx than from the
   engine.
2. `shop_page(path, debug=true)` — the build time, the number and time of the SQL, the
   state of the cache, line by line per module. If the build is quick and the page is
   slow to arrive, the cause is not in a module but below it.
3. A module with `cache on` but `allow false` — the list of tables in its manifest does
   not match what it reads; its cache never comes on.
4. **The journals are "yourself" too**, there is no need to go to the owner for them:
   they are the `log` lines of the map and are read with `engine_static_load`. An error
   of the storefront is `core/log/melbis/front.log`, a refusal before PHP (502, a
   timeout, TLS) is `core/log/nginx/error.log` and `core/log/apache/error.log`, a
   silent task is `core/log/cron/<task>.log`. The order and the
   reservations are in "The server journals" in [files.md](files.md).
5. Beyond that the server begins, and every Store's server is its own. **Do not assume
   how it is built and do not ask for SSH** — lead the owner through the server form of
   the Program (`FServerRemote`). The first action is always the same: the status report
   on the maintenance tab (`TSService`) — one button, it changes nothing, and the answer
   holds the free disk space, the memory, the sizes of the database, the files, the cache
   and the journals. A full disk and a swollen `log` are the commonest reasons for
   "everything suddenly stopped". The captions to name it by are in
   `Lang/<locale>/FServerRemote.xml`.
6. If details are needed, read the configuration files with them ("load from the
   server") and ask for reading commands in the console one at a time. The full order of
   an investigation is "Finding out how this particular server is built" in
   [server.md](server.md).
7. Advice about settings comes last and by the rules of [server.md](server.md): the
   file, the section, the whole line, what will happen after a restart and how to roll it
   back.
