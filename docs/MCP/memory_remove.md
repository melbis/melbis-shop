# memory_remove

Deletes a note of the agent's own by its number. There is no undoing it.

## The Right

"Memory → Delete" (`AGENT_MEMORY_REMOVE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `id` | number | the number of the agent's own note from `memory_list` |

The notes of groups and those for everyone the tool does not delete — they are the administrator's.

## The Answer

```json
{"id": 22, "name": "zz-mcp-probe", "removed": 1}
```

| Field | What it is |
|---|---|
| `id` | the number of the note |
| `name` | its name |
| `removed` | `1` — deleted; `0` — the agent has no note of its own with this number, and then `name` does not come |

## Refusals

| Answer | When |
|---|---|
| `id is required - memory_list shows the number of every note.` | `id` is not named |
| `id is the number of a note, as memory_list shows it` | the number is not an integer greater than zero |
| `The table agent_memory is in work right now, and nothing was written…` | a window of the program holds the table |

The common refusals — "[Answers and Refusals](answers.md)".
