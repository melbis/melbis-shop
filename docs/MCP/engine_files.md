# Element Files

The `engine_files_*` section works with the files attached to the store's rows: a product photo, a certificate, a supplier's price list. The template styling files — images, fonts, icons — are the "[Images](engine_image.md)" section.

## Where the File Lies

An element file is a row of the `files_<entity>` table and the file itself on the server's disk:

```
files/<year>/<month_day>/<hour_minute>/<table>_<user>_<id>.<extension>
```

The engine builds the folder out of the upload time down to the minute, and makes up the name itself. The name people see the file under is kept in `real_name`.

The extension stays in the name only if it is one of the safe ones: images, documents, archives, sound and video, fonts, design files. A file of another type — `php`, `html` or `svg`, for example — goes onto the disk without an extension: that way the web server will not execute it and will not show it as a page. The former name is kept in `real_name`.

## The Entity

`entity` is the name of the owner table, and everything else follows from it:

| From `entity` | What comes out | Example for `store` |
|---|---|---|
| the owner table | `<entity>` | `store` |
| the files table | `files_<entity>` | `files_store` |
| the registry of kinds | `key_value` rows with `key_code = 'FILES_<ENTITY>'` | `FILES_STORE` |

An entity will do if the store has a `files_<entity>` table with a generator. In this version there are thirteen of them: `store`, `info`, `info_value`, `brand`, `topic`, `key_value`, `lang`, `field`, `advert_text`, `order_option`, `order_option_value`, `web_key`, `web_key_value`. A product photo is `store`.

## Kind and Order

`kind_key` divides an element's files into groups, `kBase` by default. If the table's registry of kinds has rows, another kind is refused with a list of the allowed ones; if the registry is empty, any one will do. The order `pos` of an element's files is shared by all the groups, as in the program: a new file goes to the end of the list.

## What Is Not There

* **Editing a file.** A replacement is a new file and the removed row of the old one:
  the names are unique, nothing is written over.
* **Deleting from the disk.** Deletion removes the row, and the file stays on the
  server's disk. The space is freed by the audit module for unused files, which the
  owner runs. The only exception is the attachments of `tool_run`: the files a command
  has not named as its own the store wipes from the disk itself, right after the call.
* **Reduced copies.** They are made by the program's image processing; the tool puts
  only the source file.

## The Table in Work

Adding and removing take `files_<entity>` into work for the duration of the call and mark it for the storefront's cache. If somebody else is holding the table — most often an open window of the program — both refuse, having written nothing. Loading only reads, and a busy table does not get in its way.

## In the Markup

The path to a file is assembled out of the row's fields, not out of a ready-made folder:

```html
<img src="{IMG:UPLOAD_TIME|path}{IMG:FILE_NAME}">
```

The `path` modifier is described in "[Modifiers](../Dev/tpl_mod.md)".
