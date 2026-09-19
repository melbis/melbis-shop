# engine_static_dir_remove

Deletes a statics folder with everything inside: the files, the nested folders and the bundle descriptions of those files. There is no undo. The built bundles are not rebuilt.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | the path of the folder to delete |

The group's own `statics/` is the root: it cannot be deleted.

## The Answer

```json
{"path": "./../templates/default/statics/banners", "bundled": 1}
```

| Field | What it is |
|---|---|
| `path` | the deleted folder's path |
| `bundled` | how many of the deleted files had a bundle description. If it is more than zero, the built bundles still hold those files — see "[Statics](engine_static.md)" |

## Refusals

| Answer | When |
|---|---|
| `No such folder in the statics tree: … engine_map_tree lists the folders under dirs.` | the folder is not among the statics folders |
| `… is the statics root of a template - it cannot be renamed or deleted…` | this is the group's `statics/` |
| `… is not a folder of the statics tree.` | the path lies outside the group's statics folder |
| `path is required…` | `path` is not named |

The common refusals — "[Answers and Refusals](answers.md)".
