# engine_files_load

Downloads element files to this computer — into the `files\` folder of the store folder, in the same structure as on the server. The program downloads them to the same place.

## The Right

"Direct access → Store files → Load" (`AGENT_ENGINE_FILES_LOAD`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `files` | list of objects | what to download |

The fields of an object:

| Field | Type | What it is |
|---|---|---|
| `entity` | string | the entity — the owner table — see "[Element Files](engine_files.md)" |
| [`id`] | number | one file by the id of its row |
| [`elem_id`] | number | all the files of the owner row — instead of `id` |

One call brings as many files as fit into the limit on the size of a batch; the rest get `skipped`, and they are requested again by `id`.

## The Answer

```json
{"folder": "D:\\Melbis\\shop.example.com", "total": 1, "sent": 1,
 "files": {"columns": ["entity", "id", "elem_id", "kind_key", "real_name", "pos", "size", "path", "state"],
           "rows": [["store", 1, 1, "kBase", "photo.png", 1, 73,
                     "files/2026/09_17/00_53/files_store_1_1.png", "ok"]]}}
```

| Field | What it is |
|---|---|
| `folder` | the store folder on this computer: the file lies at `folder` + `path` |
| `total` | how many file rows have been found |
| `sent` | how many files have come |
| `files` | the file rows: `entity`, `id`, `elem_id`, `kind_key`, `real_name`, `pos`, `size`, `path` and `state` |

| `state` | What it means |
|---|---|
| `ok` | the file has been downloaded |
| `missing` | the row is there, but the file is not on the server's disk |
| `skipped` | it did not fit into the batch — request it again by `id` |

An `id` that does not exist gives no refusal: `total` is zero and `rows` is empty.

## Refusals

| Answer | When |
|---|---|
| `Unknown element entity: …` | such an entity has no files table |
| `files is required: …`, `Every entry needs entity.` | the files or the entity are not named |

The common refusals — "[Answers and Refusals](answers.md)".
