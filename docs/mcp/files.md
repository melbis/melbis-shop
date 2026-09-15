# Files of the project: code, markup, statics, images

Only the files of the **project** are here. Files hanging on rows of the Store — a
product photo, a certificate, a supplier's price list — are another matter, see
[elements.md](elements.md).

## Kinds and operations

In the Program **Load/Save** are common actions, while **Add/Rename/Delete** belong
to each kind separately. It is the same here: pick the command by where the file
lies.

| Kind of file | where it lies | load | save | add | rename | remove |
|---|---|---|---|---|---|---|
| PHP: a root script | `index.php` | `engine_php_load` | `engine_php_save` | `engine_php_add` | `engine_php_rename` | `engine_php_remove` |
| PHP: a module or an inc | `units/<m>.php` | `engine_php_load` | `engine_php_save` (+manifest) | `engine_php_add` | `engine_php_rename` | `engine_php_remove` |
| HTML: a view of a module | `templates/<t>/units/<m>/<v>.htm` | `engine_html_load` | `engine_html_save` | `engine_html_add` | `engine_html_rename` | `engine_html_remove` |
| Statics, css/js/anything | `templates/<t>/statics/…` | `engine_static_load` | `engine_static_save` | `engine_static_add` | `engine_static_rename` | `engine_static_remove` |
| Images, fonts, icons | `templates/<t>/images/…` | `engine_image_load` | — (uploading is `add`) | `engine_image_add` | `engine_image_rename` | `engine_image_remove` |
| A folder of statics | `templates/<t>/statics/…` | — | — | `engine_static_dir_add` | `engine_static_dir_rename` | `engine_static_dir_remove` |
| A folder of images | `templates/<t>/images/…` | — | — | `engine_image_dir_add` | `engine_image_dir_rename` | `engine_image_dir_remove` |
| A template set | `templates/<t>/` | — | — | `engine_template_add` | `engine_template_rename` | `engine_template_remove` |

## Paths

**Every file command speaks one word, `path`** — the same path the map prints, with
its `./../` prefix or without it: `./../units/melbis_cataloge.php` and
`units/melbis_cataloge.php` are the same file. From it the command works out what is
meant: `units/…` is a module, a path through `templates/…/units/…` is a view, any
other `.php` is a root script. Either slash will do. A template set is the exception: it
is addressed by its name (`template`), the way a module is addressed by its file.

**Renaming and moving are one word, `new_path`**: the same folder with a new name is
a rename in place, another folder with the same name is a move as it is. Changing
both at once is refused: one act at a time, which is how the engine travels too. The
one command outside this rule is `engine_template_rename`, which addresses sets and
not files: it takes `was_template` and `new_template`.

**A miss answers in words.** Deleting or renaming something that is not there is
`Unit not found` / `File not found` / `Template not found`, not a quiet "done":
success means the job was done, always.

**Inside the tree of the Store only.** `config.json`, absolute paths and `..` in any
file parameter answer `ACCESS_DENIED` — that is not a right you lack, it is a
border. Look at the parameters of the configuration through `engine_dev_config`, the
secrets are already cut out there.

Where everything lies — [map.md](map.md).

## The rules you pay for

- **SAVE overwrites the whole file.** Before changing an existing one, read it with
  the matching `*_load`, or you will blindly wipe out somebody's work.
- **SAVE changes, it does not create.** It makes neither a file nor a folder; on a
  path that does not exist it answers "create it with `*_add`".
- **`engine_static_add` makes an empty file.** It takes no content at all: the text
  arrives on a second step, `engine_static_save`. Uploading a new css or js in one
  command, the way `engine_image_add` uploads a picture, will not work — you would
  get an empty file.
- **The body need not be retold.** In `engine_php_save`, `engine_html_save` and
  `engine_static_save`, instead of `content` you may name `file` — a path on **this**
  machine; the file travels to the Store byte for byte, past you. That is how a
  module comes back from a backup or moves between Stores: retelling that much text
  is both slower and more error-prone. Only `path` is required; naming neither
  `content` nor `file` is refused, because silence would mean "save emptiness".
- **A change to css or js reaches people only with `build: true`.** The page links
  the statics with the number of the build — `bundle.melbis.js?15` — and a browser
  keeps the old file until that number grows. The file on the server is already new:
  the save went through, the storefront serves a fresh bundle, and the visitor still
  sees the old one. The `build` flag is on all three saves and the decision is
  yours: changing a style for people, say `true`; changing a module or a view, there
  is no point, browsers do not cache those. Raising it costs one thing: every
  visitor fetches the styles once more.

## A module

`engine_php_add {path: "units/<m>.php"}` creates the module together with its
`.json` and a folder `templates/*/units/<m>/main.htm` in every set — except when the
second word of the name is `inc` or `include`, or the name holds no underscore at
all. A view folder left over from a deleted module of the same name it **wipes** in
the process — with not a word about it in the answer, so a name that has already
lived in this Store should be checked against the map first. Deleting a module takes
its `.json` and its view folders with it.

Then `engine_php_save` for the code and the manifest, `engine_html_save` for the
markup. Another view is added with `engine_html_add` by its full path
(`templates/<t>/units/<m>/extra.htm`), which will create the module's folder if it
is not there yet. Renaming a view travels inside the folder of its own module — only
the file name changes.

**The manifest** goes into `engine_php_save` as the `manifest` parameter, with the
same keys `engine_php_load` returned: `unit_info`, `param_info`, `includes`
(`foo.php=1`), `cache_on`/`cache_time`/`lazy_load`/`ajax_load` (the entry point),
`trick_*`, `smart_*`. Without `manifest` only the code is changed and the `.json` is
left alone.

**The table list is not yours to keep.** The engine watches which tables the module
actually queries and writes them into the manifest itself, with bare names
(`store=1`). A save wipes what was learned and it is gathered again on the very
first render; the only line that survives a save is a switched-off one (`log=0`) —
that is how a dependency is dropped on a table that is written often and does not
affect the output. See [units.md](units.md).

After saving a module, read the warning line from the linter: it names the includes
that were not declared and the ones that are not used.

How a module is built and what breaks in it silently — [units.md](units.md).

## Images, fonts and the rest of a template

`templates/<t>/images/` holds more than images — fonts and icons live there too. The
commands of this branch work with any file.

- **`engine_image_load`** puts the file down locally, in `mcp\melbis\<the
  same path>` — or wherever `file` names, which is how a picture lands beside a script
  you are writing; a relative `file` counts from the local folder of the Store. It returns
  the size and the mime type. png, jpeg, gif and webp are
  shown in the answer as well; svg comes as text; a font and everything else only
  lands on the disk. The local copy is never read instead of the server.
- **`engine_image_add`** is the upload: `path` names the whole target, the folder and
  the name at once; the body is `file`, a path on **this** machine, or `content`,
  text for an svg. There is no separate save here — uploading again to the same path
  is the replacement, **and it is irreversible, these files keep no versions.** So on
  an existing path the command refuses first: tell the owner what exactly will be
  replaced, and repeat with `overwrite=true`.
- **`engine_image_rename`** renames and moves by the `new_path` rule. The folder in
  `new_path` may be in **another** branch: a font travels freely from `images` to
  `statics` and back. The same works from the statics side with
  `engine_static_rename`.
- **Deleting is irreversible**, and `*_dir_remove` takes the folder down with
  everything in it. Agree it first. The `images` and `statics` folders inside a
  template are roots: they can only be filled.
- There is no need to clear the engine's cache after replacing such a file: the web
  server serves them directly. In the visitor's browser, though, a picture with the
  same name may well stay the old one — and that is not the Store's cache.

## Bundles

Every css and js included in a build has a descriptor in `templates/<t>/bundles/`,
and the name of the descriptor is a hash of the file's path. That is why renaming,
moving and deleting through the `engine_static_*` and `engine_image_*` commands drag
the descriptor along by themselves.

Changing the contents through `engine_static_save` triggers the build — the
storefront gets the new one at once.

**Which builds a file goes into is the `bundle` of that same save**, two fields in
the words the load answers by: `param_info`, the builds themselves, and `unit_info`,
what the file is. Given, the descriptor is written whole and the builds of the
template are reassembled; omitted, the file keeps what it had; both empty, it leaves
every build. So load the file first and look at what stands there — the answer says
it — then save what you meant.

```
"bundle": {"param_info": "melbis.css: 10, print.css: 2", "unit_info": "The styles of the header"}
```

The name of a build **carries its extension**: `melbis.css` is served as
`statics/bundle.melbis.css`, so a css and a js of one set are two names, not one.
After the colon is the priority — it orders the glueing and may be left out, which
means zero. Letters, digits, dot and underscore only; a hyphen is cut out silently.

But **on a move the built `statics/bundle.<name>` files are not rebuilt.** If the
command touched a descriptor, the answer carries a warning: so many files of the
bundle were affected, the build is stale, and the storefront goes on serving the old
one. Rebuilding it is a save away, though: saving any file of that template that has
a descriptor — its own content, unchanged, no `bundle` said — reassembles the whole
group.

The other side of the same rule: statics must not be moved around **past** these
commands. A descriptor left without its file brings the whole template's build down
with `Can't include file to bundle`.

Do not load a built `statics/bundle.*` without a reason: it is every file of the
build glued together, and it is large. What you edit is always the source file;
reading the build is a last resort, only to check what went into it.

## The history of versions

**`engine_history_list(path)`** — who saved the file and when,
**`engine_history_content(id)`** — the content of that version. A version is written
on every save through `*_save`, as the options of the development environment of the
Program on this machine say: switched off there, no version is written, and a file
keeps no more versions than the number set there. The list is paged: `before` is a
datetime cursor (versions no later than it) and `limit` the size of a page, so you
page down with the value of the last row.

Four different answers mean four different things, do not mix them up:

- "no versions yet" — the file is there, but has never been saved from here;
- "no such file in the project" — check the path against the map;
- "journals keep no history" — files of `core/log/` never have one;
- "files of the images tree keep no history" — replacement there is irreversible
  too, see above.

## The server journals

**This is what people go in over SSH for elsewhere.** The engine gathers the
journals of the whole stack into `core/log/` and hands them over through the same
map as the project files: they are read with an ordinary `engine_static_load` by the
path from the map.

| Channel | What is written |
|---|---|
| `core/log/melbis/front.log` | errors of the storefront: URL, IP, User-Agent, POST and the session |
| `core/log/melbis/back.log` | errors of the back office — the exchange with the Program and the web modules |
| `core/log/cron/<task>.log` | a file per cron task: time, URL, code, duration, the body of the answer |
| `core/log/apache/error.log`, `core/log/nginx/error.log` | the web server and the proxy, with `access.log` beside them — everything that happened before PHP |

So the one story of one request reads in one place: nginx took it → apache handed it
to PHP → the engine fell over → cron could not get through.

**The `melbis/` channels are written only while the file `error.save` lies in the
root** — both `front.log` and `back.log`. No file, no errors in the journal at all,
and an empty channel means "recording is off", not "all is well". Check that before
you draw a conclusion from a journal.

A daily `access.log` runs to tens of megabytes, so the engine hands over **the last
1 MB** and warns with the line `=== TRUNCATED: showing the last N MB … ===`.
Rotation is daily, seven days are kept; the compressed `.gz` files do not get into
the map.

**The whole of a file** — `engine_whole_load(path, file)`: packed on the server, sent
past the answer and written to `file` on this machine, so its size has to fit nowhere —
grep it, count it, take the piece you came for. Made for the journals, whose plain load
answers with the tail alone; but it takes any file of the Store, and byte for byte
besides — the answer road normalises line ends, this one does not.

What is not here: `docker logs` (the output of the containers is switched off), the
MySQL journal and the system journald. If it is not in these channels, that is for
the owner, [server.md](server.md).

How the platform is built on this side — `../Guide/Russian/Dev/log.md`.

## The cache

**`engine_dev_cache_clear(type, unit?)`** clears a level: `cache`, `trick`, `smart`,
`static` across the whole site, or `unit_cache`, `unit_trick`, `unit_smart` with a
`unit` named — for one module. The module is named as the map names it,
`melbis_cataloge`, and the `units/melbis_cataloge.php` form is taken as well; a name
that is no module of this Store is refused by name, so a clear that answers "done"
did clear. What these levels are — `../Guide/Russian/Dev/cache.md`.

**A save drops the cache of the module the file belongs to.** That is any path
carrying a `units` segment — `units/x.php`, with a manifest or without one, and a
view `templates/<t>/units/x/…`. What goes is the `cache` and the `trick` of module
`x`, and its run statistics in the smart monitor — they timed a body that no longer
exists. Saving a library does the same for everyone who includes it, however deep
the chain. A root script, a css or an image has no `units` in its path and drops
nothing.

Beyond that, invalidation is bound to changes of the tables and not of the files, so
`engine_dev_cache_clear` is for the wholesale and the manual clears. If the pinpoint
`unit_*` did not help, clear the whole level.
