# memory_load

Returns the notes whole, by their numbers — several in one call.

## The Right

"Memory → Load" (`AGENT_MEMORY_LOAD`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| [`ids`] | list of numbers | the numbers of the notes from `memory_list` |
| [`id`] | number | one number — instead of `ids` or together with them |
| [`output`] | string | a folder on this computer — an absolute path or a path from the store folder — for the texts of the notes |

At least one number is needed.

With `output` the text of every note goes into the file `<folder>\<id>.html`, and the note carries the path `file` instead of `body`. The text of a critical note comes in the answer all the same: it is read before any work.

A critical note that has been loaded is counted to the session: when all of them are loaded, the server carries out the other commands — see "[Memory](memory.md)".

## The Answer

```json
{"notes": [{"id": 1, "name": "who-is-the-user", "kind_key": "kCritical", "level": "own",
            "group": "", "category": "people",
            "info": "The store's owner works under the admin login",
            "body": "<p>The owner works with the store…</p>"}],
 "missed": [999999]}
```

| Field | What it is |
|---|---|
| `notes` | the notes: `id`, `name`, `kind_key`, `level`, `group`, `category`, `info` and the text `body`; with `output` — the path `file` |
| `missed` | the numbers that are not among the notes the agent sees |

## Refusals

| Answer | When |
|---|---|
| `ids is required - memory_list numbers every note.` | not a single number is named |
| `id is the number of a note, as memory_list shows it` | the number is not an integer greater than zero |
| `Cannot write …`, `Cannot create folder …` | the `output` folder could not be created or written into |

The common refusals — "[Answers and Refusals](answers.md)".
