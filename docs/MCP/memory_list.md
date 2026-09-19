# memory_list

Returns the list of the notes the agent sees: its own, those of its groups and those for everyone. The texts of the notes are not in the list — `memory_load` reads them.

## The Right

"Memory → Get list" (`AGENT_MEMORY_LIST`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| [`find`] | string | only the notes that have this word in the name, the category, the description or the text |

## The Answer

```json
{"notes": {"columns": ["id", "name", "kind_key", "level", "group", "category", "info", "size", "edit_time"],
           "rows": [[1, "who-is-the-user", "kCritical", "own", "", "people",
                     "The store's owner works under the admin login", 825, "2026-09-15 16:04:49"]]}}
```

| Column | What it is |
|---|---|
| `id` | the number of the note — for `memory_load`, `memory_save` and `memory_remove` |
| `name` | the name |
| `kind_key` | the kind |
| `level` | the level: `all`, `group` or `own` |
| `group` | the name of the group for a group's note, empty otherwise |
| `category` | the category |
| `info` | the line of description |
| `size` | the size of the text in bytes |
| `edit_time` | when the note was changed last |

The order and the levels — "[Memory](memory.md)". If there are no notes, `rows` is empty, and the reason comes as a second block: `Memory of this store is empty.` or, if `find` is named, `No note matches …`.

## Refusals

Only the common ones — "[Answers and Refusals](answers.md)".
