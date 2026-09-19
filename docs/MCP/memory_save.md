# memory_save

Writes a note of the agent's own: a new one, or an existing one by its number.

## The Right

"Memory → Save" (`AGENT_MEMORY_SAVE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| [`id`] | number | the number of the agent's own note that is being changed; without it a new one is written |
| [`name`] | string | a short name; required for a new note |
| [`category`] | string | free words for grouping |
| [`info`] | string | one line of description — the list shows it |
| [`body`] | string | the text in simple HTML: `<p>`, `<ul>` and `<li>`, `<b>`, `<code>` |
| [`kind_key`] | string | a kind from the registry: `kCritical`, `kDirect`, `kSkill`, `kDefault` or a kind of the owner's own |

**A field that is not named keeps its previous value:** the text can be rewritten without touching the kind, and the kind changed without touching the text. A new note without `kind_key` gets `kDefault` and stands last.

While a window of the program holds the `agent_memory` table, the write does not go through — see "[Memory](memory.md)".

## The Answer

```json
{"id": 22, "name": "zz-mcp-probe", "saved": "created"}
```

| Field | What it is |
|---|---|
| `id` | the number of the note |
| `name` | its name |
| `saved` | `created` — a new one, `updated` — a changed one |

## Refusals

| Answer | When |
|---|---|
| `No note of yours with id … - memory_list shows your notes` | the agent has no note of its own with this number — including when it is a group's note or one for everyone |
| `name is required` | the new note has no name |
| `No kind […] among the notes - the store keeps: …` | the kind is not in the registry; the allowed ones are listed |
| `id is the number of a note, as memory_list shows it` | the number is not an integer greater than zero |
| `The table agent_memory is in work right now, and nothing was written…` | a window of the program holds the table |

The common refusals — "[Answers and Refusals](answers.md)".
