# engine_files_add

Attaches files from this computer to the store's rows. The rest the engine does itself: the id, the name, the folder and the file's row.

## The Right

"Direct access → Store files → Add" (`AGENT_ENGINE_FILES_ADD`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `files` | list of objects | the files, an object per file |

The fields of an object:

| Field | Type | What it is |
|---|---|---|
| `entity` | string | the entity — the owner table: `store`, `info`, `brand`… — see "[Element Files](engine_files.md)" |
| `elem_id` | number | the id of the row the file belongs to |
| `file` | string | the file on this computer — an absolute path or a path from the store folder |
| [`kind_key`] | string | the kind of the file; `kBase` by default |
| [`real_name`] | string | the name for people; the file's name by default |

**Batches.** A long list goes off in several requests, by size — `MaxFileSize` in `Shop.ini` on this computer, 8192 KB by default. The engine checks the whole batch before writing — the entities, the owner rows, the kinds — and a refusal of the check leaves nothing of the batch. The batches that went earlier stay added.

**The number of files in one request** is limited by the server's PHP as well, by the `max_file_uploads` parameter: one file fewer fits into a batch, because the call itself counts too.

## The Answer

```json
{"files": {"columns": ["entity", "elem_id", "id", "kind_key", "real_name", "path", "size", "pos"],
           "rows": [["store", 1, 1, "kBase", "photo.png",
                     "files/2026/09_17/00_53/files_store_1_1.png", 73, 1]]}}
```

| Column | What it is |
|---|---|
| `entity`, `elem_id` | whose the file is |
| `id` | the id of the file's row — the file is downloaded and removed by it |
| `kind_key` | the kind of the file |
| `real_name` | the name for people |
| `path` | the file's path on the site |
| `size` | the size in bytes |
| `pos` | the place in the group of its own kind |

## Refusals

| Answer | When |
|---|---|
| `Unknown element entity: …` | such an entity has no files table |
| `Element not found: … id …` | there is no `elem_id` row |
| `No group […] among the files of a … - it keeps: …` | the kind is not in the table's registry; the allowed ones are listed |
| `The table files_… is in work right now, and nothing was written…` | somebody else is holding the files table |
| `This php takes … upload(s) in one request…` | there are more files in the batch than the server's PHP accepts |
| `No such file: …` | there is no `file` on this computer |
| `files is required: …`, `Every entry needs entity and file.` | the files or their fields are not named |

If the refusal came after the first batches had gone, it starts with `Added before this:` and lists the files already added.

The common refusals — "[Answers and Refusals](answers.md)".
