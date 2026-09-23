# session_clear

Shows this agent application's conversation folders in the store and removes the ones that are named. A conversation folder holds everything the agent has made in the conversation and everything the tools have written for it: answer tables, storefront pages, query results, images, copies of the store. The folders do not go away by themselves: the server does not clear them.

## The Right

Not required: the folders lie on this computer, and the tool does not go to the store.

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| [`topics`] | list of strings | the topics of the folders to remove. Without it the tool only shows the folders |

## The Answer

Without `topics` — the list of the folders:

```json
{"current": "prices-sept",
 "topics": [{"topic": "prices-aug", "size": 41230512, "changed": "2026-08-30 17:42"},
            {"topic": "prices-sept", "size": 6983248, "changed": "2026-09-21 10:15"}],
 "size": 48213760}
```

| Field | What it is |
|---|---|
| `current` | the topic of this session |
| `topics` | the conversation folders: the topic, the size in bytes, the time of the last write |
| `size` | how much the application's folder takes up as a whole |

With `topics` — what has been removed:

```json
{"removed": ["prices-aug"], "missing": []}
```

| Field | What it is |
|---|---|
| `removed` | the removed folders |
| `missing` | the named topics that have no folder |

The folder of a conversation going on right now in another window cannot be told from an old one by the list — the time of the last write gives a hint. So only what the user has named is removed. The folder of the current conversation can be removed too: the next write creates it anew, but without the files it held before.

When the conversation folders take up more than a gigabyte together, the answer of [`session_connect`](session_connect.md) says so.

## Refusals

| Answer | When |
|---|---|
| `Not connected…` | there is no session |
| `topics is a list…` | `topics` is not a list |
| `topics holds […], which is not a topic…` | the list holds something that is not one word of Latin letters, digits, `-` and `_`; then nothing is removed |
| `Cannot delete …`, `Cannot remove …` | the system did not let a file or a folder be removed — for example, the file is open in another program; what was removed before it is named in the answer |
