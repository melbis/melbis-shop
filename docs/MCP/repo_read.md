# repo_read

Reads the repository: a section's list of topics, a topic's history, or records.

## The Right

"Repository → Read data" (`AGENT_REPO_READ`). Every action is also let through by a section flag — "[Repository](repo.md)".

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `action` | string | `topic_list`, `store_list` or `store_read` |
| [`repo`] | number | the section's number from `repo_list` — for `topic_list` |
| [`find`] | string | only the topics that have this word in `name`, `descr` or `params` — for `topic_list` |
| [`offset`] | number | how many rows to skip before the page; 0 by default |
| [`topic`] | number | the topic's number from `topic_list` — for `store_list` and `store_read` |
| [`topics`] | list of numbers | several topics at once — for `store_read` |
| [`record`] | number | one record of the history by its number from `store_list` — for `store_read` instead of topics |
| [`appendix`] | yes/no | give back the records' `appendix` as well — for `store_read` |
| [`output`] | string | a folder on this computer — an absolute path or a path from the store folder — for the texts of the records, for `store_read` |

A page is up to 100 rows; `total` says how many there are in all, `offset` skips the ones already read.

## topic_list

The section's topics, the ones written to last going first; topics without records go at the end.

```json
{"repo": 2, "path": "Trial -> Prices",
 "topics": {"columns": ["id", "name", "descr", "params", "records", "last_time", "last_author", "last_user"],
            "rows": [[1, "Levenhuk telescopes", "Competitors' prices for Levenhuk telescopes: a summary by store",
                      "levenhuk telescope competitors", 2, "2026-09-28 02:26:40",
                      "Claude Opus 5.5, effort medium, Claude Code", "Administrator"]]},
 "total": 1, "offset": 0}
```

| Column | What it is |
|---|---|
| `id` | the topic's number |
| `name`, `descr`, `params` | the topic's name, description and words |
| `records` | how many records the topic has |
| `last_time`, `last_author`, `last_user` | when, by whom and by whose assistant the last record was made; empty if there are no records |

If nothing matches `find`, the second block is `No topic of the section matches …`.

## store_list

The topic's history without the texts, the new records going first.

```json
{"topic": 1, "name": "Levenhuk telescopes",
 "records": {"columns": ["id", "author", "user", "date_time", "comment", "size"],
             "rows": [[2, "Claude Opus 5.5, effort medium, Claude Code", "Administrator",
                       "2026-09-28 02:26:40", "Teleskop-Market added, the Astroscope price went up", 158]]},
 "total": 2, "offset": 0}
```

`size` is the size of `body` in bytes.

## store_read

The topics' current records — the last record of each of `topic` and `topics` — or one record of the history by `record`.

```json
{"records": [{"id": 2, "topic": 1, "name": "Levenhuk telescopes",
              "author": "Claude Opus 5.5, effort medium, Claude Code", "user": "Administrator",
              "date_time": "2026-09-28 02:26:40", "comment": "Teleskop-Market added, the Astroscope price went up",
              "size": 158, "appendix_size": 130, "body": "# Levenhuk …"}],
 "empty": [2], "missed": [99]}
```

| Field | What it is |
|---|---|
| `records` | the records: the number, the topic and its name, the author, the person, the time, `comment`, the size and the text of `body`; `appendix_size` is the size of the appendix, and `appendix` itself comes only with `appendix: true` |
| `empty` | the topics that have no records yet |
| `missed` | the numbers under which there are no topics |

With `output` the texts go into the folder named: `body` into `<topic>.<record>.txt`, `appendix` into `<topic>.<record>.appendix.txt`, and the record carries the paths `file` and `appendix_file` instead of them.

## Refusals

| Answer | When |
|---|---|
| `action is one of topic_list, store_list, store_read` | there is no `action`, or it is another one |
| `repo is the number of a section…`, `topic is the number of a topic…`, `record is the number of a record…`, `topics are the numbers of topics…` | the number is not given, or it is not a whole number greater than zero |
| `topics is required…` | for `store_read` there is neither `topic`, nor `topics`, nor `record` |
| `offset is the number of rows to skip, 0 or more` | `offset` is not a whole number from zero |
| `No section … is open to you - repo_list shows yours` | there is no such section, it is a folder, or the agent has not a single flag in it |
| `No topic … - topic_list of its section shows them` | there is no topic with this number |
| `No record … - store_list shows the history of a topic` | there is no record with this number |
| `ACCESS_DENIED: your rights in section […] do not hold …` | the section lacks the flag the action needs |
| `Cannot write …`, `Cannot create folder …` | the `output` folder could not be created or written into |

The common refusals — "[Answers and Refusals](answers.md)".
