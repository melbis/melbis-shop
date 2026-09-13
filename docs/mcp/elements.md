# Files of elements: product photos and attachments

A file here belongs to **a row of a table**: the bytes lie in
`files/<year>/<month_day>/<hour_min>/`, the description is a row in
`files_<entity>`. The folder is worked out from `upload_time` down to the minute
and the file name is invented by the engine — never put them together yourself.

Files of the **project** — code, markup, statics, the images of a template — are
another matter, see [files.md](files.md).

## The entity

An entity is the name of the owning table, and everything else follows from it:

| From `entity` | Comes out | Example for `store` |
|---|---|---|
| the owning table | `<entity>` | `store` |
| the table of files | `files_<entity>` | `files_store` |
| the registry of kinds | `key_value` with `key_code = 'FILES_<ENTITY>'` | `FILES_STORE` |

There are thirteen of them: `store`, `info`, `info_value`, `brand`, `topic`,
`key_value`, `lang`, `field`, `advert_text`, `order_option`, `order_option_value`,
`web_key`, `web_key_value`. A product photo is a `store`.

The engine keeps no list of its own: an entity is valid when the Store has a
`files_<entity>` table with a generator. So the thirteen above are what this version
carries, and the refusal `Unknown element entity` means exactly that there is no
such companion table.

## Three tools

**`engine_files_add(files[])`** — per file, `entity`, `elem_id` and `file` (a path on
**this** machine), with `kind_key` and `real_name` optional. The engine does the
rest: takes the table into work, gets an id, builds the name and the folder, lays the
file down and writes the row. The answer gives, per file, the id, the kind, the name
and the size.

The kind defaults to `kBase` and is met against the registry of that table: if the
registry holds kinds, a wrong one is refused with the allowed ones listed; if the
registry is empty, any kind goes through. The owning row has to exist, too — an
`elem_id` that is not there is refused by name and number, before anything is
written.

**`engine_files_load(files[])`** — `entity` plus `id` for one row, or `elem_id` for
every file of an element. The files land in the `files\` folder of the local folder
of the Store, in the same structure as on the server — which is where the Program
puts them as well. The
answer is a manifest with a `state`: `ok`, `missing`, or `skipped` when the pack hit
the size limit (then ask again by `id`).

**`engine_files_remove(files[])`** — `entity` and `id`, and it removes **the row
only**.

Add and remove take `files_<entity>` into work while they run, so a table somebody
else holds — a form of the Program open on it, most often — refuses those two: the
answer names the table and says that nothing was written. Load only reads, and a held
table does not stop it. Who holds it is in `engine_db_locks`, and
it is the owner who closes the form; see "Locks" in [data.md](data.md).

A long list travels in several packs, cut by the `MaxFileSize` of the connection.
That is by size, not by count, so a pack of many small files can still hit the other
ceiling — how many uploads the php of the Store takes in one request. The refusal
says the number; send them in smaller lists.

## What is not here

**Editing a file.** Replacing means adding the new one and taking the old row off;
names are unique, nothing is ever laid over anything.

**Derived copies.** The scaled-down versions are made by the pipeline of the Program;
the agent puts down the base file only.

**Deleting a file from the disk.** There is no such thing anywhere in the product:
the row can be taken off, the bytes stay. The owner reclaims the space by running
their audit module for idle files. Tell them so if you are deleting many.

## In the markup

The path is assembled from the fields of the row, never from a hardcoded folder:

```html
<img src="{IMG:UPLOAD_TIME|path}{IMG:FILE_NAME}">
```
