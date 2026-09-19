# tool_list

Gives back the store's AI tools: the list of them all, or one tool in full — with every command and its fields.

## The Right

"Tools → Get list" (`AGENT_TOOL_LIST`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| [`unit`] | string | the tool's address as the list gives it: `MELBIS_AGENT_CURRENCY`. Without it — the list of them all |

## The Answer

Without `unit`:

```json
{"grant": "Development -> AI Components",
 "tools": {"columns": ["unit", "path", "descr", "may", "also"],
           "rows": [["MELBIS_AGENT_TASK", "Business -> Scheduler",
                     "The scheduler of the program: the tasks and the feed of notes under every one of them.",
                     ["CmdList", "CmdAdd", "CmdNoteList", "CmdNoteAdd"], []]]}}
```

| Field | What it is |
|---|---|
| `grant` | where in the program the tools are created and the rights to their commands are granted |
| `tools` | the tools: `unit` — the address, `path` — the place in the tree of AI tools, `descr` — the description up to 160 characters, a long one cut at the end of a phrase, `may` — the granted commands, `also` — the rest |

If the store has no tools, `rows` is empty, and the second block is `This store has no AI-tools of its own yet…` with the window and the tab of the program where they are created.

With `unit`:

```json
{"unit": "MELBIS_AGENT_TASK", "path": "Business -> Scheduler",
 "descr": "The scheduler of the program…",
 "commands": [{"name": "CmdAdd", "may": true,
               "descr": "Adds a task and opens its feed with the first note…",
               "params": {"columns": ["name", "type", "required", "default", "descr"],
                          "rows": [["name", "str", true, "", "one line of what to do"],
                                   ["kind_key", "str", false, "kDefault", "a value of TASK_KIND_KEY"]]},
               "files": null}]}
```

| Field | What it is |
|---|---|
| `unit`, `path` | the address and the place in the tree |
| `descr` | the tool's description in full |
| `commands` | the commands: `name`, `may` — whether it is granted to this login, `descr` — the description, `params` — the command's fields, `files` — whether it accepts attachments |
| `params` | the fields: `name`, `type`, `required` — whether it is required, `default` — the default value, `descr` |
| `files` | `null` if the command accepts no attachments; otherwise `required` and `descr` |

## Refusals

| Answer | When |
|---|---|
| `ACCESS_DENIED: no such tool: <unit>` | there is no tool with that address in the registry |

The common refusals — "[Answers and Refusals](answers.md)".
