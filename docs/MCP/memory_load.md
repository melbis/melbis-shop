# memory_load

Returns the notes whole, by their numbers — several in one call.

## The Right

"Memory → Load" (`AGENT_MEMORY_LOAD`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| [`ids`] | list of numbers | the numbers of the notes from `memory_list` |
| [`id`] | number | one number — instead of `ids` or together with them |

At least one number is needed.

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
| `notes` | the notes: `id`, `name`, `kind_key`, `level`, `group`, `category`, `info` and the text `body` |
| `missed` | the numbers that are not among the notes the agent sees |

## Refusals

| Answer | When |
|---|---|
| `ids is required - memory_list numbers every note.` | not a single number is named |
| `id is the number of a note, as memory_list shows it` | the number is not an integer greater than zero |

The common refusals — "[Answers and Refusals](answers.md)".
