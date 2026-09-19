# tool_run

Carries out one command of the store's AI tool.

## The Right

"Tools → Execute" (`AGENT_TOOL_RUN`); the commands of the AI tools themselves are granted separately, in the registry of AI tools.

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `unit` | string | the tool's address from `tool_list`: `MELBIS_AGENT_CURRENCY` |
| `command` | string | the command: `CmdList` |
| [`params`] | object | the command's fields, as `tool_list` with this `unit` declares them, each with a value of its own type |
| [`params_source`] | string | a JSON file on this computer with the same object of fields — an absolute path or a path from the store folder — instead of `params`, see "[Fields and attachments from a file](tool.md)" |
| [`files`] | list of objects | the attachments — for a command that accepts them |
| [`files_source`] | string | a JSON file on this computer with the same list of attachments — instead of `files` |
| [`debug`] | yes/no | add the number of the module's queries and their time to the time |
| [`into`] | string | the name of a job in one word, with no path and no dots: the answer's tables are appended into the file `<into>.<table>.jsonl` as well, see "[The `into` job](tool.md)" |

`params` and `params_source` are one and the same by two roads, and they cannot be named together; the same goes for `files` and `files_source`. A command without fields needs neither. A field with the value `null` counts as unnamed. What every field type accepts — "[AI Tools](../Dev/agent_tool.md)".

An attachment entry:

| Field | Type | What it is |
|---|---|---|
| `file` | string | the file on this computer — an absolute path or a path from the store folder |
| `entity` | string | the entity — the table that owns the file, see "[Element Files](engine_files.md)" |
| `elem_id` | number | the id of the row the file belongs to |
| [`kind_key`] | string | the kind of the file; `kBase` by default |
| [`real_name`] | string | the name for people; the file's name by default |

## The Path of the Call

The call goes through three stages, and at each of them the parameters change their form:

- **the MCP server** — on this computer: reads the files, assembles the request, and not everything goes on to the store;
- **Melbis Core** — the store's engine: checks the call against the registry, lays out the attachments, and only the fields reach the module;
- **the tool's module** — gets the person of the session and the ready-made fields in `$mParam`.

| Parameter | MCP server | Melbis Core | Module |
|---|---|---|---|
| `unit` | passes it on | finds the tool in the registry; takes the module's file from the registry, not from the call | — |
| `command` | passes it on | finds the module's function whatever the case of its name, checks whether the command is granted | — |
| [`params`] | passes it on | checks the fields against the registry, casts the types, fills in the defaults | `$mParam` |
| [`params_source`] | reads the file and sends it as `params` | as `params` | `$mParam` |
| [`files`] | reads the files into parts of the request; the `file` path does not go to the store | lays the files out on the disk and creates their rows | `$mParam['files']` — the files' rows with `id`, `path`, `size`… |
| [`files_source`] | reads the list and sends it as `files` | as `files` | as `files` |
| [`debug`] | passes it on as a flag beside the call | counts the module's queries | — |
| [`into`] | keeps it for itself: appends the answer's tables into the job's files | — | — |

The same path on one call of "File Import" from the distribution.

**1. The agent calls:**

```json
{"unit": "MELBIS_AGENT_IMPORT_FILES", "command": "CmdAdd",
 "params": {"profile": "catalog"}, "files_source": "mcp\\claude-code\\photos.json",
 "into": "photos", "debug": true}
```

and into `photos.json` its script has written:

```json
[{"file": "C:\\photos\\1532_front.jpg", "entity": "store", "elem_id": 1532, "real_name": "Front view"}]
```

**2. The MCP server** reads the list and the file and sends the store a request in which the file travels as the part `data_1`, and there is no local path. `into` the server keeps for itself:

```json
{"unit": "MELBIS_AGENT_IMPORT_FILES", "command": "CmdAdd", "params": {"profile": "catalog"},
 "debug": true, "files": [{"part": "data_1", "entity": "store", "elem_id": 1532, "real_name": "Front view"}]}
```

**3. Melbis Core** finds the tool and the command, checks the grant and the `profile` field, puts the file into `files/2026/09_18/14_05/`, creates a row in `files_store` and calls the module:

```php
CmdAdd($mUserId, [
    'profile' => 'catalog',
    'files'   => [['entity' => 'store', 'id' => 841, 'elem_id' => 1532, 'kind_key' => 'kBase',
                   'file_name' => 'files_store_1_841.jpg', 'path' => 'files/2026/09_18/14_05/',
                   'real_name' => 'Front view', 'size' => 184233, 'pos' => 1, …]]
    ]);
```

The fields the agent has written on an attachment beyond the ones the store knows travel in the same row: a tool can ask more about a file than Melbis Core knows.

**4. The module** sees only the person of the session and the ready-made fields: it knows nothing of `debug`, `into`, the local paths or `files_source`. Its answer goes the way back — "The Path of the Answer" below.

## The Answer

```json
{"result": true, "message": "The tables asked for",
 "detail": {},
 "tables": {"currency": {"rows": 3,
                         "file": "D:\\Melbis\\shop.example.com\\mcp\\melbis\\tables\\MELBIS_AGENT_CURRENCY.CmdList.currency.jsonl",
                         "head": ["{\"id\":\"1\",\"name\":\"USD\"}", "…", "…"]}},
 "files": [],
 "time": {"server": 11, "module": 1, "trip": 46}}
```

| Field | What it is |
|---|---|
| `result` | `true` — done, `false` — a refusal |
| `message` | what happened and what to do next |
| `detail` | the values the command returned: the `id` of a new row, `found` and others — their meaning is written in the command's description; `{}` if there are none |
| `tables` | the answer's tables: `rows` — how many rows, `file` — where they lie, `head` — the first three rows as text, cut to 300 characters; with `into` also `job` — the job's file, and `job_rows` — how many rows it holds now; `{}` if there are no tables |
| `files` | the attachments the command has kept for itself; `[]` if there are none |
| `time` | `server` — how long the call lasted on the server, `module` — how much of that the module took, `trip` — the whole round trip by this computer's clock; with `debug` also `sql` and `sql_ms` |

The table's file is overwritten by every call of that command, the job's file only grows — see "[AI Tools](tool.md)". How the command builds this answer — the keys, the tables, the refusals and the crashes, with examples — is described in "The Command's Response" of the developer guide, "[AI Tools](../Dev/agent_tool.md)".

The attachments belong to the command that named them as its own in `files`. If the files sent are not named there, the store clears them all away, and `The N file(s) sent with this call are gone: the command [<command>] did not take them.` is appended to `message`. If the module was not found or broke off with an error, the files sent with the call are already written: they stay with the products and the other elements they were sent to.

## The Path of the Answer

The answer goes back through the same three stages, and what the module returned the agent gets in another form: the rows go off into files, the missing keys are added, the time appears.

| Key | Module | Melbis Core | MCP server | The agent gets |
|---|---|---|---|---|
| `result` | must return `true` or `false` | no `result` means a crash: `<UNIT>\<command> answered without result` | passes it on as is | `true` or `false` |
| `message` | what happened and what to do next | if it has cleared away files that were not taken, appends a sentence about it | passes it on as is | `message` |
| `detail` | its own values: `id`, `found`… | not returned — sets `{}` | passes it on as is | `detail` |
| `tables` | rows as lists under names | not returned — sets `{}` | writes every table into `<UNIT>.<command>.<table>.jsonl`; with `into` also appends it into the job's file | for every table `rows`, `file`, `head`; with `into` also `job`, `job_rows` |
| `files` | the attachments taken | not returned — sets `[]` | passes it on as is | `files` |
| other keys | everything it returned beyond the five | leaves them after the five keys | passes them on as is | as the module returned them |
| time | does not write it | the time of the whole request and the module's time; with `debug` — the number of the module's queries and their time | adds the whole round trip by this computer's clock | `time`: `server`, `module`, `trip`; with `debug` also `sql`, `sql_ms` |
| crash | an exception, no module or no function | answers with the text of the error instead of JSON | passes the text on | a refusal as text, with no JSON |

With `result: false` the agent gets the same answer, but marked as an error of the call (`isError`): by this mark the agent application understands that the command has not been carried out.

The same path on an answer of "Currencies", `CmdList`.

**1. The module** returns:

```php
return ['result' => true, 'message' => 'The tables asked for', 'tables' => ['currency' => $rows]];
```

**2. Melbis Core** adds the missing keys and sends the answer in its own envelope, together with the time:

```json
{"ok": true, "ms": 11, "unit_ms": 1,
 "data": {"result": true, "message": "The tables asked for", "detail": {},
          "tables": {"currency": [{"id": "1", "name": "USD"}, …]}, "files": []}}
```

**3. The MCP server** takes the envelope off, writes the table's rows into a file and assembles `time`. The agent gets:

```json
{"result": true, "message": "The tables asked for", "detail": {},
 "tables": {"currency": {"rows": 3, "file": "…\\MELBIS_AGENT_CURRENCY.CmdList.currency.jsonl",
                         "head": ["{\"id\":\"1\",\"name\":\"USD\"}", "…", "…"]}},
 "files": [], "time": {"server": 11, "module": 1, "trip": 46}}
```

An application on protocol version 2025-06-18 or newer also gets the same object as data, in `structuredContent` — see "[Answers and Refusals](answers.md)". What every key means for the one who writes a tool — "The Command's Response" in "[AI Tools](../Dev/agent_tool.md)".

## Refusals

**`result: false`** — a refusal by the command, or by the engine before it: an unknown command, an ungranted one, a field too many or a required one missing, a wrong type. The answer comes with the mark of error and the same JSON as a successful one; the reason is in `message`.

```json
{"result": false, "message": "Unknown command [CmdNope]. The tool has: CmdList, CmdAdd, CmdUpdate, CmdRemove, CmdPos",
 "detail": {}, "tables": {}, "files": [],
 "time": {"server": 6, "trip": 47}}
```

| Answer | When |
|---|---|
| `ACCESS_DENIED: no such tool: <unit>` | there is no tool with that address |
| `Module not found! [units/<module>.php]` | the tool's module is not found on the server |
| `Function not found! [<UNIT>\<command>]` | the module has no function for that command |
| `<UNIT>\<command> failed: <text>` | the module broke off with an error; the text names it |
| `<UNIT>\<command> answered without result`, `The command answered without result…` | the command answered without `result`: whether it was done or refused cannot be known |
| `The command [<command>] takes no files…` | attachments were sent to a command that does not accept them |
| `The command [<command>] carries the files themselves - …` | the command requires attachments and there are none |
| `Unknown element entity: <entity>`, `Element not found: <entity> id <id>`, `No group […]` | the attachment points at an unknown entity, at an element that does not exist, or at a file kind that is not its own |
| `The table files_… is in work right now, and nothing was written…` | somebody else holds the attachments table; not one of them was written |
| `unit is required…`, `command is required…` | the tool or the command is not named |
| `into is the bare name of a job…` | `into` holds a path or a dot |
| `These files come to more than … KB together…` | the attachments together are heavier than `MaxFileSize` from `Shop.ini`: if the command takes every file separately, they are sent in smaller batches, otherwise the owner raises the limit; a single file passes at any size |
| `Every entry needs file - a path on this machine.`, `No such file: …` | an attachment has no `file`, or the attachment's file, the `params_source` or the `files_source` is not there |
| `params or params_source, not both…`, `files or files_source, not both…` | both are named |
| `params is an object of fields…`, `files is a list of entries…` | the value is not of that kind |
| `… is not utf-8 text - save it in utf-8.` | the `params_source` or `files_source` file is not in UTF-8, or it holds a zero byte |
| `… is not whole json…`, `… holds no object of fields…`, `… holds no list of entries…` | the `params_source` or `files_source` file is cut off, or it holds a value of the wrong kind |

The common refusals — "[Answers and Refusals](answers.md)".
