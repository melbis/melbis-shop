# Modules and templates: the model and the rules

Only what is needed here to **avoid breaking** a Store with the MCP-tools. How the
platform is built and what its API can do is in the documentation of the platform,
linked at the end of this page; there is no point in repeating it here.

**If you are asked to write or change a module, open the developer documentation
first** (the table of chapters at the end of this page) and start with the
conventions (`rules.md`). The names of files, functions, variables and SQL aliases
there are not a wish but a contract: a flat unit prefixes every function with the name
of its file while a unit with a namespace keeps only the tail (`Main`, `CmdList`), and
the field hints in the Program work off the abbreviated aliases (`ut`,
`u_author`) for the people who will maintain this code.

## What a module consists of

One module is up to four things, and they live in different places:

| What | Where | What to change it with |
|---|---|---|
| the code | `./../units/<module>.php` | `engine_php_load` / `engine_php_save` |
| the manifest | `./../units/<module>.json` | the `manifest` parameter of `engine_php_save` |
| the views (markup) | `./../templates/<set>/units/<module>/*.htm` | `engine_html_load` / `engine_html_save` |
| statics and images | `templates/<set>/statics/`, `.../images/` | `engine_static_*`, `engine_image_*` |

The parser calls one main function, and it has two forms. A file that declares
`namespace MELBIS_CATALOGE;` names it `Main`, and the parser calls
`MELBIS_CATALOGE\Main($mVars)`. A flat file without that line names it after the file
in upper case — `function MELBIS_CATALOGE($mVars)`. The namespaced form is tried
first, so the call in the template is the same either way. There can be several template sets, and the view of a module
lies in each of them as a copy of its own — changing `default`, you do not touch the
rest.

## The manifest

The manifest is the `.json` beside the code. It is read together with the code —
`engine_php_load` prints its keys in the head of the answer — and written together
with the code (`manifest` in `engine_php_save`). Without the `manifest` parameter only
the code is saved and the `.json` is left as it was.

| Key of the manifest | Meaning |
|---|---|
| `unit_info` | the description of the module, for the hints in the Program |
| `param_info` | the declaration of the input parameters: `id: int, key: str` |
| `table_info` | the tables of the module — the engine keeps the list itself, your business is only to switch one off: `store=1|log=0` |
| `includes` | the library modules it uses: `melbis_inc_logic.php=1` |
| `cache_on`, `cache_time` | the base cache of the module |
| `lazy_load` | deferred loading |
| `ajax_load` | **an entry-point module** |
| `trick_*`, `smart_*` | the advanced cache levels |

Two traps:

- **The manifest and the `.json` name four fields differently**, for historical
  reasons: `unit_info` is `description`, `param_info` is `parameters`, `table_info`
  is `tables`, `ajax_load` is `entry_point`. Reading the `.json` with your eyes, look
  for the second name of each; sending `engine_php_save`, write the first.
- **The table list is not yours to keep.** The engine watches which tables the module
  actually went to and adds them to the manifest itself, with bare names (`store=1`),
  see the table of forms below. A save wipes what was learned and it is gathered again
  from the very first render; the only line that survives a save is a switched-off one
  (`log=0`) — that is how a dependency is dropped on a table that is written often and
  does not affect the output. A library has no list at all: its queries belong to
  whichever module called it.

## How a module is started

**From a template** — the main way, open to any module:

```html
{MELBIS:melbis_store_card([VAR:LANG],[ID],[PRIOR])}
```

The arguments are positional, separated by commas; a skipped argument is an empty
place (`{MELBIS:melbis_cataloge_sub(,,[ID])}`).

**Variables in the arguments go in square brackets only.** `[ID]` runs the value
through `urlencode` and the parser does the matching `urldecode` on arrival; `{ID}` is
substituted raw, and the first value with a comma, a bracket or a quote in it will
tear the argument list apart — the module gets rubbish. Literals (`kDefault`, `8`) are
written as they are.

**As an entry point** — by a call to `MELBIS()->Run()` from a root script. For that
the manifest must carry `ajax_load = 1`, or the parser will refuse to run the module
directly.

## The name of a table: two forms

| Where | Form | Example |
|---|---|---|
| Any SQL — in the code of a module and in the pool of the agent alike | `{DBNICK}_` | `SELECT * FROM {DBNICK}_store` |
| The service parameters: `table_info` of the manifest, the `lock`/`change`/`generate` steps, the list of locks | without the nick | `store` |

The real nick **must not** be written in the code of a module: the parser checks this
and answers with "Wrong prefix in table name!". And that is not pedantry — it is by
the occurrences of `{DBNICK}_name` that the platform ties the cache of a query to the
times the tables changed. In the pool of the agent both forms are equal, but
`{DBNICK}_` is better: such a query moves into a module without a single edit.

## A `melbis_*` module is not edited

A module with the **`melbis_`** prefix is a file shipped with the engine, and in the
owner's Store it stays as it is. If you are asked to change its behaviour, do not edit
it: **make your own** and carry over what you need.

1. `engine_php_add` for `<company>_inc_logic` (or for an ordinary module, if a view is
   needed);
2. `engine_php_load` of the `melbis_*` you are taking the functions from, and
   `engine_php_save` of yours — with the names changed to your own prefix;
3. make the changes in yours;
4. in the template of the module that calls it, replace the call with yours, and save
   that template.

The reason is not tidiness: an update of the engine brings new `melbis_*` files, and a
change made in them either disappears or stops the update. And a file that has drifted
from the shipped original stops being a reference — there is then nothing to compare
the Store against.

Say this to the User plainly, in one sentence: "I will make the change in your own
module and leave the shipped file untouched, so that an update does not wipe it out".
This holds for the demo store and for the ready-made AI-tools as well — their modules
are `melbis_*` too.

## What breaks in silence

- **`*_save` overwrites the whole file** and **creates** neither a file nor a folder.
  Changing an existing one, read it first with the matching `*_load`. Needing a new
  one, `*_add` first.
- **Nobody locks a file.** The registry of locks guards tables; between your `*_load`
  and `*_save` the file may have been saved by a person in the Program, and your save
  will wipe their change in silence — only the version history will save you. When you
  are changing a file, tell the User to leave it alone for now; after a long pause,
  read the file again before writing.
- **A save drops the cache of its module, a data write does not.** Save the code of a
  module and it loses its cache, its views included — save a library and everyone who
  includes it loses theirs. Editing data is the other way round: the storefront looks
  at the mark that a table changed, and a `modify` or an `insert` step never puts that
  mark — the `change` step does, while a tree step marks its own table and
  `clear_dependent` marks the dependent tables it emptied, not the table you named it
  on. If you changed data, list the tables in `change`, or the change will not show on
  the storefront.
- **A missing key is visible in the HTML.** If the template has `{TITLE}` and the
  module passed no such key, the tag stays in the output as the text `{TITLE}` — that
  is not broken markup but a sign of a typo in the name.
- **The page you asked for may come out of the cache** — with your change not in it.
  That happens where the save dropped nothing: a css, a root script, a module other
  than the one you edited. Clear it with `engine_dev_cache_clear` and look again before
  deciding the change did not work.
- **`shop_page` shows the markup, not the view.** That a block appeared, that a tag
  did not leak out as text, that the link to the bundle is right — all visible. How it
  looks on a screen, on a phone, and whether one thing now covers another — not; that
  is still for the owner.

## Where to look in the documentation of the platform

The files are in `../Guide/Russian/Dev/`, relative to this page.

| The task | The file |
|---|---|
| how a page is put together at all | `arch.md`, `root.md` |
| writing a module: structure, parameters, the call | `unit.md` |
| the syntax of `.htm`: loops, conditions, nested parts | `tpl_syntax.md` |
| the `Tpl*` methods | `tpl_api.md` |
| the value modifiers (`\|html`, `\|num`, `\|path`, `\|webp`…) | `tpl_mod.md` |
| the system tags `{PATH}`, `{LANG}`, `{CSRF_TOKEN}` | `tpl_const.md` |
| passing data between modules | `tpl_global.md` |
| the template sets and what a group is | `tpl_group.md` |
| the `Sql*` methods, transactions, locks | `sql.md` |
| the system dictionaries and their own doors (`Sys*`) | `sys.md` |
| the service methods of the platform | `util.md` |
| dates and times in the zone of the project | `datetime.md` |
| the five levels of cache and when each is for | `cache.md` |
| library modules (`inc`) | `inc.md` |
| `config.json` and the constants it becomes | `config.md` |
| building css/js into bundles | `tpl_bundle.md` |
| web modules (external applications and panels) | `web.md` |
| deferred loading of blocks | `lazy.md` |
| sessions, CSRF, cookies, the visitor | `session.md`, `cookie.md`, `visitor.md` |
| the scheduler | `cron.md` |
| the debugger and the journals | `debug.md`, `log.md` |
| the naming conventions | `rules.md` |
| the AI-tools of a Store: registry, contract, commands | `agent_tool.md` |

## Examples of modules

The reference for the canonical style is the modules of the demo store every
installation begins with: small, but finished. A fresh copy is in the public
repository [github.com/melbis/melbis-shop](https://github.com/melbis/melbis-shop)
(`units/`, `templates/`). The modules of the Store at hand are visible through
`engine_map_tree` and `engine_php_load` — but remember that they may have been written
at different times and in different styles: check a technique against the
documentation of the platform instead of copying it on trust.
