# repo_write

Writes into the repository: creates, edits and removes topics, adds records.

## The Right

"Repository → Modify data" (`AGENT_REPO_WRITE`). Every action is also let through by a section flag — "[Repository](repo.md)".

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `action` | string | `topic_create`, `topic_update`, `topic_remove` or `store_write` |
| [`repo`] | number | the section's number from `repo_list` — for `topic_create` |
| [`topic`] | number | the topic's number from `topic_list` — for the other actions |
| [`name`] | string | the topic's name, up to 255 characters; required for `topic_create` |
| [`descr`] | string | what is in the topic: by it others decide whether to read it |
| [`params`] | string | the topic's free words, one line up to 255 characters — the topic is found by them too |
| [`author`] | string | who writes the record: the model, its effort and the like, up to 100 characters; required for `store_write` |
| [`comment`] | string | one line up to 255 characters about what the record changed |
| [`body`] | string | the text of the record — the whole state of the topic; required for `store_write` |
| [`body_source`] | string | a UTF-8 file on this computer — an absolute path or a path from the store folder — with the text of the record, instead of `body` |
| [`appendix`] | string | data to the record, read only when it is needed |
| [`appendix_source`] | string | a UTF-8 file with the appendix, instead of `appendix` |

## topic_create

A new topic in the section, with no records yet. A topic name is unique within a section, and case is not told apart.

```json
{"topic": 1, "name": "Levenhuk telescopes", "saved": "created"}
```

## topic_update

Changes the topic's `name`, `descr` and `params`; a field not named stays as it was.

```json
{"topic": 2, "name": "Eyepieces", "saved": "updated"}
```

## topic_remove

Removes the topic together with all its records at once.

```json
{"topic": 2, "name": "Eyepieces", "records": 5}
```

`records` is how many records went together with the topic.

## store_write

A new record of the topic. A record does not change: the last one is the topic's current state, the earlier ones are its history. The record's person is the session's login, and the store sets the time. A byte order mark at the beginning of a `*_source` file is dropped.

```json
{"record": 2, "topic": 1, "name": "Levenhuk telescopes", "size": 158}
```

`size` is the size of `body` in bytes.

## Refusals

| Answer | When |
|---|---|
| `action is one of topic_create, topic_update, topic_remove, store_write` | there is no `action`, or it is another one |
| `repo is the number of a section…`, `topic is the number of a topic…` | the number is not given, or it is not a whole number greater than zero |
| `No section … is open to you - repo_list shows yours` | there is no such section, it is a folder, or the agent has not a single flag in it |
| `No topic … - topic_list of its section shows them` | there is no topic with this number |
| `ACCESS_DENIED: your rights in section […] do not hold …` | the section lacks the flag the action needs |
| `name is required - the name of the topic` | the new topic has no name, or it is empty |
| `The section has topic […] already, id … - write into it` | the section already has a topic with this name — on creation |
| `The section has topic […] already, id … - give this one another name` | the same on renaming |
| `Nothing to change - topic_update takes name, descr or params` | `topic_update` without fields |
| `author is required…` | the record has no `author` |
| `body is required - the text of the record` | the record has no `body` |
| `body or body_source, not both…`, `appendix or appendix_source, not both…` | both are named |
| `No such file: …`, `… is not utf-8 text - save it in utf-8.` | the `*_source` file does not exist or is not in UTF-8 |

The common refusals — "[Answers and Refusals](answers.md)".
