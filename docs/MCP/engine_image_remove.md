# engine_image_remove

Deletes a file from the images folder. There is no undo: image files have no versions.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | the path of the file being deleted |

## The Answer

```json
{"path": "./../templates/default/images/logo_old.png", "bundled": 0}
```

| Field | What it is |
|---|---|
| `path` | the path of the deleted file |
| `bundled` | `1` if the file had a bundle description, otherwise `0` — see "[Statics](engine_static.md)" |

## Refusals

| Answer | When |
|---|---|
| `No such file in the images tree: …` | the file is not in the images folder |
| `… is a folder, not a file.` | the path leads to a folder: it is deleted by `engine_image_dir_remove` |
| `… is not a file of the images tree…` | the file lies outside the images folder |
| `path is required…` | `path` is not named |

The common refusals — "[Answers and Refusals](answers.md)".
