# tool_pool

Carries out many commands of AI tools in one call, in order: a batch of one command or a chain where a step takes what the step before it has done.

Every call costs the agent a turn, and a hundred rows written one at a time are a hundred turns. The pool sends the same `tool_run` calls one after another from this computer: the store sees ordinary calls, and a step refuses or answers the same way a separate call would.

## The Right

"Tools → Execute" (`AGENT_TOOL_RUN`) — the same right as that of `tool_run`; every command of the pool is checked by its own grant in the registry of AI tools.

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| [`pool`] | list of objects | the steps in the order in which they are carried out |
| [`pool_source`] | string | a JSON file on this computer with the same list of steps — an absolute path or a path from the store folder — instead of `pool` |

One of the two is needed, and they cannot be named together.

A step:

| Field | Type | What it is |
|---|---|---|
| [`name`] | string | the word by which the following steps refer to this one; no dot |
| `unit` | string | the tool's address from `tool_list` |
| `command` | string | the command |
| [`params`] | object | the command's fields, as in `tool_run` |
| [`params_source`], [`debug`], [`into`] | | as in `tool_run` |

A step is pure JSON: a command that accepts files is carried out by a separate [`tool_run`](tool_run.md) call.

## A Reference to a Step

The string `"@name.field"` in a step's `params` is replaced with the value of `detail.field` of the step with this `name` carried out earlier; the value keeps its type — a number stays a number. A reference works at any depth of `params`, lists included. The path goes deeper too: `"@goods.row.id"`, and in a list — by the number of the element, counting from zero.

```json
{"pool": [
  {"name": "cur", "unit": "MELBIS_AGENT_CURRENCY", "command": "CmdAdd",
   "params": {"skey": "EUR", "name": "Euro", "multiplex": 44.2}},
  {"unit": "MELBIS_AGENT_CURRENCY", "command": "CmdPos",
   "params": {"type": "POS", "data": [{"id": "@cur.id"}]}}
]}
```

A string in which `@` is followed by a name that belongs to no step before it goes as it is: it is a word of data. A reference to a field that the step's `detail` does not have stops the pool at this step.

## How It Goes

- The whole list is checked before the first step: every step has a `unit` and a `command`, the names carry no dot and do not repeat, there are no files.
- The steps go in order. The first `result: false` or crash stops the pool.
- There is no rollback: what the steps before the stop wrote stays written.

## The Answer

```json
{"result": true,
 "steps": [{"num": 1, "name": "cur", "unit": "MELBIS_AGENT_CURRENCY", "command": "CmdAdd",
            "result": true, "message": "The row of currency is added", "detail": {"id": 12},
            "tables": {}, "files": [], "time": {"server": 33, "module": 13, "trip": 78}},
           {"num": 2, "name": "", "unit": "MELBIS_AGENT_CURRENCY", "command": "CmdPos",
            "result": true, "message": "…", "detail": {}, "tables": {}, "files": [],
            "time": {"server": 21, "module": 6, "trip": 63}}],
 "time": {"trip": 145}}
```

| Field | What it is |
|---|---|
| `result` | `true` — all the steps are carried out, `false` — the pool is stopped |
| `steps` | for every step carried out: `num` — the number, `name`, `unit`, `command` and the command's response — as in `tool_run`; the step on which the pool stopped stands last |
| `time` | `trip` — the full round of the whole pool by the clock of this computer |

A step's tables are written into the file `tables\step<number>.<unit>.<command>.<table>.jsonl` in the conversation folder: one command may stand in the pool many times, and every step has its own file. The next pool overwrites these files.

A stopped pool comes with the mark of error and the same JSON; the reason is in the `message` of the last step. If a step did not reach the store — the reference was not found or the call crashed — it has only `result: false` and `message`.

## Refusals

| Answer | When |
|---|---|
| `pool is required…` | there is neither `pool` nor `pool_source`, or the list is empty |
| `pool or pool_source, not both…` | both are named |
| `Step N is not an object…` | a step is not an object |
| `Step N needs unit and command…` | a step has no `unit` or `command` |
| `Step N carries files…` | a step has `files` or `files_source` |
| `The name of step N carries a dot…` | there is a dot in `name` |
| `Two steps are named […]…` | two steps with one name; case is not told apart |
| `@<name>.<field> names a field the detail of that step does not have.` | the step's `detail` has no such field — in the `message` of the step on which the pool stopped |
| `… holds no list of steps…`, `… is not whole json…`, `No such file: …`, `… is not utf-8 text…` | the `pool_source` file is not found, is not in UTF-8, is cut off, or holds no list |

The refusals of the steps themselves are as in `tool_run`. The common refusals — "[Answers and Refusals](answers.md)".
