# AI Tools

An AI tool is a modular script that the store owner registers for the AI assistant. The agent sees the list of tools on connection and runs them by name — this is how a store gets its own "buttons": create a product the way it is done here, close an order by your rules.

The list is data, not code. A new tool appears for the agent without updating the program: rows in the registry plus a module in `units`. The owner maintains the registry in the program: **Development → AI Tools**.

## The Registry

| Table | What it holds |
|---|---|
| `agent_tool` | the tool: name, description, module (`unit`); a tree with folders |
| `agent_tool_command` | the tool's commands: the name — which is also the function name — description, order |
| `agent_tool_param` | the command's parameters: name, description, type, required flag, default, order |
| `agent_tool_right` | grants: a row gives one command (`command_id`) to a person (`user_id`) or a group (`group_id`) |

The registry is the only source of the signature: from it the agent learns both what the tool does (the descriptions) and how to call it (the commands with their fields). The module does not describe itself — all that is left in it are the command bodies.

Rights are granted to **people**: the agent signs in to the store under the login of the employee sitting at the keyboard — there is no separate AI user — and the grant is counted for them: either their own row or a row of their group. Hence the fact that the same tool can do different things for different employees. A right is a command: as many commands, as many rights. The holder of the "Change AI settings" right (`PUT_AGENT_OPTION`) owns all commands automatically. The list is open to everyone: the agent sees all tools and all commands, and the granted ones as a separate `may:` line; if it is empty, the agent tells the employee whom to ask for access.

## A Tool Is an Entity, a Command Is Its Function

One tool covers one entity in full — "Users", "Tasks" — and that is one module. A command names one piece of its work, and its name in the registry is the name of the module's function: `CmdList`, `CmdTaskAdd`. The `Cmd` prefix tells a command apart from the helper functions, which the agent does not see.

A right is granted on the whole command, with all of its parametric breadth: `CmdNoteAdd` writes both a comment and a task movement — one door, as in the program. If you want to separate "comment" and "move" by grant, that is two commands, not two values of a parameter.

## The Call Contract

The module declares a namespace by its own name in capitals, and the engine calls the command's function directly — there is no entry point and no dispatching `switch` in the module:

```php
namespace MELBIS_AGENT_USER;

/**
 * Function CmdList
 * The users of the store
 **/
function CmdList($mUserId, $mParam)
{
	// ...
}
```

- `$mUserId` — the id of **the employee whose session is open**, that is, the person
  at the keyboard. It is always real, there is no anonymous session, and there is no
  point checking it for zero. It signs authorship: of tasks, notes, changes.
- `$mParam` — the command's fields as a ready-made array, already weighed against the
  registry (see "The Routine Is the Engine's Job").

The response is an array, and it travels to the agent whole. The convention: `result` is an unambiguous `true` or `false`, `message` is human text that the agent will read out to the employee, and the remaining keys are the payload (`id`, `users`, `password`…). There are no machine code words in the response: a boolean result plus text is enough for the agent. Refusals from the engine reach the agent in the same contract — one language of refusal, whoever does the refusing.

## The Routine Is the Engine's Job

Before the module, the engine checks everything that is written in the registry, and what is left for the module are the rules of the subject area:

- **the module** — the tool's address; it is checked against the registry, and no
  path of one's own to someone else's file arrives from outside;
- **the command** — by name, in any case; an unknown one is refused with a list of
  the declared ones, one that has not been granted is refused with an address:
  which command of which tool, and where it is granted;
- **the fields** — by the rows of `agent_tool_param`. A field the command has not
  declared is refused with a list of the accepted ones: a typo does not disappear
  silently. A required field left unset is refused. A field with the value `null`
  counts as unnamed — that is how the agent "clears" a field;
- **the types** — the type has to match, there is no silent coercion: whatever does
  not match comes back in words rather than as a quiet zero;
- **the defaults** — a non-empty `value_def` is substituted into an unnamed field.
  Commands that change something existing have no defaults at all: an unnamed field
  must stay unnamed, otherwise a partial change turns into an overwrite.

| Type | What it accepts | What the module gets |
|---|---|---|
| `str` | any single value | string |
| `int` | an integer or its spelling as `"12"` | int |
| `float` | a number | float |
| `bool` | `true`/`false`, `yes`/`no`, `1`/`0`, `on`/`off` | bool |
| `date` | a day as `YYYY-MM-DD` | string in the database form |
| `time` | an hour as `HH:MM:SS`, the seconds may be omitted | string in the database form |
| `datetime` | a day, and an hour after it; the hour may be omitted | string in the database form |
| `json` | a list or an object; a scalar is refused | array |
| `jsonl` | a stream of rows: a list of objects or a file with them (see below) | array of rows |
| `files` | the call's attachments, not a field (see below) | rows of the file table in `$mParam['files']` |

**Days and hours** are brought to the form the database keeps them in, and "31 February" is rejected rather than sliding to the third of March. There is no need — and no reason — to check them in the module: the database is strict, and a word it cannot read ruins not the field but the whole answer.

**A stream of rows.** A command that loads a batch of homogeneous rows — imported products, attribute values, price-list prices — declares a parameter of type `jsonl`. The assistant puts either the rows themselves there as an array, or a path to a jsonl file on its own machine: `{"file": "..."}`, one row per record. **The module gets an array in both cases** — the file is unfolded on the assistant's side, before the call, and the module never has to read files at all.

The difference is not one of convenience but of possibility: a batch of five hundred rows typed by the assistant into the call parameters is hundreds of thousands of characters in its own message, whereas a file written by its script does not pass through it at all.

**A list instead of a single value.** Any type except `json`, `jsonl`, and `files` takes the suffix `/json` — `int/json`, `str/json`, `datetime/json`. Such a field accepts both a single value and a list, and the module **always gets an array**, so the "one or many" fork never appears in the code:

```php
$list = implode(',', $mParam['store_id']);   // int/json: the elements are already weighed
```

Every element is weighed by the same type as a single value — which is exactly what makes substituting a list into `IN ( … )` safe. An empty list, and a list inside a list, are refused.

Multiplicity is a property of the registry row, not of the field as such: the same `price` may be `float/json` in a reading command (a range, a search) and `float` in a writing one, because a list cannot be written into a column.

**Files.** A command that accepts attachments declares one parameter of type `files`. In the signature it shows up not as a field but as a mark on the command — files are attached to the call rather than passed in the parameters. The engine lays them out before the module, and the command reads ready-made rows from `$mParam['files']`; a required `files` with no attachments is refused, and attachments to a command without `files` are refused. A command that has accepted files names them in the response under the `files` key — that is how the engine knows the files have an owner; ownerless ones it clears away itself.

## Inside a Command

- **The rules are the command's business.** The login format, the existence of a row,
  the right to an object: rights to a section, a product, an item are counted from the
  row named in the parameters and stay in the module.
- **Checks first, writing under a lock.** All reads and validations come before the
  tables are taken into work; between the lock and its release there is writing only.
  Busy tables come back as a ready-made refusal advising a later retry. A tool's lock
  is written on behalf of the storefront — in the program's lock list it shows up as
  `MELBIS SYSTEM`.
- **A refusal is a hint.** A missed login answers with a list of logins, a missed id
  with the name of the list command: the agent will correct itself in one move if the
  answer names the next step.
- **Partiality.** A command that changes something existing touches only the fields it
  was given; the ones it was not are pulled from the current row.
- **Secrets do not travel.** A password is not accepted as a parameter and is not
  stored in the clear: it is generated in the module, kept in the database as md5, and
  goes out once in the response, with an instruction to change it.
- **Self-protection.** A command that saws off the branch it sits on (blocking or
  deleting the employee whose session is running) is rejected with `result=false`.
- **Demolition takes a second call.** A command that deletes something with a tail
  behind it first answers with exactly what would follow, and acts only on a repeat
  call that says `recursive`. That is the confirmation — in the command's contract
  rather than in persuasion.

## The Descriptions Share the Work

The agent assembles a call from three layers of text, each with its own depth:

- the tool's `descr` — what it does and when to call it;
- the command's `descr` — the meaning and the behavioral caveats ("the password is not
  supplied and is returned only once");
- the parameter's `descr` — what this value is and what it should be; a dictionary of
  allowed values is named here in words (`a value of STORE_KIND_KEY`).

The first two travel in the list on every connection; the parameters the agent takes one tool at a time, once it has settled on working with it.

## Libraries

The shared part of the tools lives in the library modules `melbis_inc_agent_*` (`util`, `tree`, `store`, `info`, `file`). A module includes a library through the manifest (`includes: melbis_inc_agent_util.php=1`) and declares an alias:

```php
use MELBIS_INC_AGENT_UTIL as UTIL;
// ...
$lock = UTIL\Lock([$table]);
```

The aliases it publishes are named by the library in its own manifest, in the `param_info` field — the engine checks `use` against them when saving.

## Backup and Update

**Development → AI Tools → Export** (the `TOOL_EXPORT` right; for the agent — `tool_export`) collects the whole registry and the modules behind it: `index.json` with the tree and the md5 sum of every file, `tools/<unit>.json` per tool — the commands with their fields — and `units/` with the modules, the manifests, and the libraries found through those manifests. The program lays this out straight into the chosen folder, clearing it first; the agent gets a zip archive. Grants do not go into the export: they are about the store's people, not about the tools.

The current set of the distribution lies in the same form on GitHub: [melbis/melbis-shop/ai_tools](https://github.com/melbis/melbis-shop/tree/master/ai_tools).

There is no import door, and that is a decision: after installation the owner adapts the tools to their own needs, and an update is always a comparison rather than an overwrite. The agent unpacks the container, finds what has diverged by the sums in the index, and transfers it point by point: the registry by direct queries, the module by saving the file.

## Reference Implementations

Live samples in the platform's demonstration store:

- `melbis_agent_key_value.php` — a compact entity: the dictionaries of the settings
  registry, five commands;
- `melbis_agent_user.php` — an entity with four commands: password generation,
  deletion guarded by history, a partial `CmdUpdate`;
- `melbis_agent_task.php` — a mirror of the scheduler's doors: `CmdTask* / CmdNote*`,
  one door `CmdNoteAdd` for both comment and movement, task privacy expressed by a
  single formula in every query.
