# engine_image_dir_remove

Deletes an images folder with everything inside it: the files and the nested folders. There is no undo: image files have no versions.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | the path of the folder being deleted |

The group's own `images/` is the root: it cannot be deleted.

## The Answer

```json
{"path": "./../templates/default/images/banners", "bundled": 0}
```

| Field | What it is |
|---|---|
| `path` | the path of the deleted folder |
| `bundled` | how many of the deleted files had a bundle description — see "[Statics](engine_static.md)" |

## Refusals

| Answer | When |
|---|---|
| `No such folder in the images tree: … engine_map_tree lists the folders under dirs.` | the folder is not among the images folders |
| `… is the images root of a template - it cannot be renamed or deleted…` | this is the group's `images/` |
| `… is not a folder of the images tree.` | the path lies outside the group's images folder |
| `path is required…` | `path` is not named |

The common refusals — "[Answers and Refusals](answers.md)".
