# engine_image_rename

Renames or moves a file from the images folder.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | what the file is called now |
| `new_path` | string | what it will become |

One call changes one thing:

* **the same folder, a new name** — the file is renamed;
* **another folder, the same name** — the file is moved. Any images or statics folder
  will do, including one in another template group.

The links to the file in the markup and in the styles do not change: they are fixed separately.

## The Answer

```json
{"path": "./../templates/default/images/logo.png",
 "new_path": "./../templates/default/images/logo_old.png", "bundled": 0}
```

| Field | What it is |
|---|---|
| `path` | what the file was called |
| `new_path` | what it is called now |
| `bundled` | `1` if the file had a bundle description, otherwise `0` — see "[Statics](engine_static.md)" |

## Refusals

| Answer | When |
|---|---|
| `No such file in the images tree: …` | the file is not in the images folder |
| `… is a folder, not a file.` | the path leads to a folder: it is renamed by `engine_image_dir_rename` |
| `… is not a file of the images tree…` | the file lies outside the images folder |
| `Image already exists: <name>` | there is already a file with this name in the new place |
| `One act at a time: …` | `new_path` changes both the folder and the name |
| `No such folder: … engine_map_tree lists the folders of both trees under dirs.` | the new folder is not among the images and statics folders |
| `new_path is the same path - nothing to change.` | the path does not change |
| `The last part of new_path must be a plain name.` | the new name contains `..` |
| `path is required…`, `new_path is required…` | one of the paths is not named |

The common refusals — "[Answers and Refusals](answers.md)".
