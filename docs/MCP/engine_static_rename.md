# engine_static_rename

Renames or moves a statics file. The bundle description follows the file, the built bundles are not rebuilt.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | what the file is called now |
| `new_path` | string | what it will become |

One call changes one thing:

* **the same folder, a new name** — the file is renamed;
* **another folder, the same name** — the file is moved. Any statics or images folder
  will do, including one in another template group; a file is not moved into a module's
  folder this way.

## The Answer

```json
{"path": "./../templates/default/statics/melbis/extra.css",
 "new_path": "./../templates/default/statics/melbis/extra_new.css", "bundled": 1}
```

| Field | What it is |
|---|---|
| `path` | what the file was called |
| `new_path` | what it is called now |
| `bundled` | `1` if the file had a bundle description, otherwise `0`. The bundles are not rebuilt after this — see "[Statics](engine_static.md)" |

## Refusals

| Answer | When |
|---|---|
| `… is not a static file: statics lie in…` | the path lies outside the statics: the statics are `templates/<group>/statics/`, the files next to a module's templates except `.htm`, and the files in the site's root except php |
| `File not found: <name>` | there is no such file |
| `File already exists: <name>` | a file with this name is already in the new place |
| `One act at a time: …` | `new_path` changes both the folder and the name |
| `No such folder: … engine_map_tree lists the folders of both trees under dirs.` | the new folder is not among the statics and images folders |
| `new_path is the same path - nothing to change.` | the path does not change |
| `The last part of new_path must be a plain name.` | the new name holds `..` |
| `path is required…`, `new_path is required…` | one of the paths is not named |

The common refusals — "[Answers and Refusals](answers.md)".
