# engine_history_list

Gives out all the saved versions of a file, the newer ones first.

## The Right

"Direct access → Development → Read data" (`AGENT_ENGINE_DEV_READ`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | the path of the file: `units/melbis_cataloge.php`. The path of a deleted file will do too |

## The Answer

```json
{"path": "./../templates/default/statics/melbis/main.css",
 "versions": {"columns": ["id", "time", "user"],
              "rows": [[7, "2026-09-16 21:18:40", "Administrator"],
                       [6, "2026-09-16 21:18:40", "Administrator"]]}}
```

| Field | What it is |
|---|---|
| `path` | the path of the file |
| `versions` | the table of the versions: `id` — the number for `engine_history_content`, `time` — when it was saved, `user` — who saved it; empty if there is no such user in the store any more |

If there are no versions, `rows` is empty and the reason comes as a second block:

| Reason | When |
|---|---|
| `Server journals keep no version history…` | the path leads to a journal of the server |
| `Files of the images tree keep no version history.` | the path leads to a file of the images |
| `The file exists but has no saved versions: … has it switched off.` | the keeping of versions is switched off in the program on this computer |
| `The file exists but has no saved versions yet…` | the file has not been saved yet |

## Refusals

| Answer | When |
|---|---|
| `No such file in the project: … Check the path against engine_map_tree.` | the file is not in the store's map and there are no versions at this path |
| `path is required` | `path` is not given |

The common refusals — "[Answers and Refusals](answers.md)".
