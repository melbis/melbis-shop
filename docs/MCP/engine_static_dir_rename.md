# engine_static_dir_rename

Renames or moves a statics folder with everything inside. The bundle descriptions of the files inside follow it, the built bundles are not rebuilt.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | what the folder is called now |
| `new_path` | string | what it will become |

One call changes one thing:

* **the same parent, a new name** — the folder is renamed;
* **another parent, the same name** — the folder is moved. Any statics or images folder
  will do, including one in another template group.

The group's own `statics/` is the root: it cannot be renamed or moved.

## The Answer

```json
{"path": "./../templates/default/statics/banners",
 "new_path": "./../templates/default/statics/promo", "bundled": 2}
```

| Field | What it is |
|---|---|
| `path` | what the folder was called |
| `new_path` | what it is called now |
| `bundled` | how many of the files inside had a bundle description. If it is more than zero, the bundles are not rebuilt — see "[Statics](engine_static.md)" |

## Refusals

| Answer | When |
|---|---|
| `No such folder in the statics tree: … engine_map_tree lists the folders under dirs.` | the folder is not among the statics folders |
| `… is the statics root of a template - it cannot be renamed or deleted…` | this is the group's `statics/` |
| `… is not a folder of the statics tree.` | the path lies outside the group's statics folder |
| `One act at a time: …` | `new_path` changes both the parent and the name |
| `No such folder: … engine_map_tree lists the folders of both trees under dirs.` | the new parent is not among the statics and images folders |
| `A folder cannot be moved inside itself.` | the new parent lies inside the folder itself |
| `Folder already exists: <name>` | a folder with this name is already in the new place |
| `new_path is the same path - nothing to change.` | the path does not change |
| `The last part of new_path must be a plain name.` | the new name holds `..` |
| `path is required…`, `new_path is required…` | one of the paths is not named |

The common refusals — "[Answers and Refusals](answers.md)".
