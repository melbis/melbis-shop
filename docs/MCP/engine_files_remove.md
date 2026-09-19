# engine_files_remove

Removes the rows of element files. The file itself stays on the server's disk; the space is freed by the audit module for unused files. There is no undo.

## The Right

"Direct access → Store files → Delete" (`AGENT_ENGINE_FILES_REMOVE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `files` | list of objects | what to remove |

The fields of an object:

| Field | Type | What it is |
|---|---|---|
| `entity` | string | the entity — the owner table — see "[Element Files](engine_files.md)" |
| `id` | number | the id of the file's row |

## The Answer

```json
{"removed": 1}
```

| Field | What it is |
|---|---|
| `removed` | how many rows have been removed; the ids that are already gone do not count |

## Refusals

| Answer | When |
|---|---|
| `Unknown element entity: …` | such an entity has no files table |
| `The table files_… is in work right now, and nothing was written…` | somebody else is holding the files table |
| `files is required: …`, `Every entry needs entity and id.` | the files or their fields are not named |

The common refusals — "[Answers and Refusals](answers.md)".
