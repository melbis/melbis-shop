# engine_history_content

Gives out one saved version of a file: whose it is and its body.

## The Right

"Direct access → Development → Read data" (`AGENT_ENGINE_DEV_READ`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `id` | number | the number of the version from `engine_history_list` |

## The Answer

Two blocks: the data in JSON, then the body of the version as text — as it is, with no escaping.

```json
{"id": 7, "path": "./../templates/default/statics/melbis/main.css",
 "time": "2026-09-16 21:18:40", "user": "Administrator"}
```

| Field | What it is |
|---|---|
| `id` | the number of the version |
| `path` | the path of the file the version belongs to |
| `time` | when the version was saved |
| `user` | who saved it; empty if there is no such user in the store any more |

The version of an empty file comes with no text block.

## Refusals

| Answer | When |
|---|---|
| `Version not found: <id>` | there is no version with this number: either there never was one or it has been deleted beyond the limit of keeping |
| `id is required (take it from engine_history_list)` | `id` is not given, or it is not greater than zero |

The common refusals — "[Answers and Refusals](answers.md)".
