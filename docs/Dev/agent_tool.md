# AI Tools

An AI tool is a modular script that the store owner registers for the AI assistant. On connection the agent learns how many of them the store has, takes the list with a `tool_list` call, and runs the tools by name — this is how a store gets its own "buttons": create a product the way it is done here, close an order by your rules.

The list is data, not code. A new tool appears for the agent without updating the program: rows in the registry plus a module in `units`. The owner maintains the registry in the program: **Development → AI Components**, the "AI Tools" tab; how to fill in the window — "[AI Tools](../User/ai-tools.md)" in the user guide.

## The Registry

The registry is the only source of the signature: from it the agent learns both what the tool does (the descriptions) and how to call it (the commands with their fields). The module does not describe itself — all that is left in it are the command bodies.

Rights are granted to **people**: the agent signs in to the store under the login of the employee sitting at the keyboard — there is no separate AI user — and the grant is counted for them: either their own row or a row of their group. Hence the fact that the same tool can do different things for different employees. A right is a command: as many commands, as many rights. The holder of the "Change AI settings" right (`PUT_AGENT_OPTION`) owns all commands automatically. The list is open to everyone: the agent sees all tools and all commands — the granted ones in the `may:` line, the rest in the `also:` line; with an empty `may:`, the agent tells the employee whom to ask for access.

## A Tool Is an Entity, a Command Is Its Function

One tool covers one entity in full — "Users", "Tasks" — and that is one module. A command names one piece of its work, and its name in the registry is the name of the module's function: `CmdList`, `CmdDone`. The `Cmd` prefix tells a command apart from the helper functions, which the agent does not see.

A right is granted on the whole command, with all of its parametric breadth — which is why commands are divided by right rather than by the values of a parameter. In the "Scheduler", `CmdNoteAdd` only comments, and it is granted to everyone who sees the task, while the task is moved by `CmdState`, `CmdPass`, `CmdDone`, and `CmdClose` — each of them can be granted separately.

## The Call Contract

The module declares a namespace by its own name in capitals, and the engine calls the command's function directly — there is no entry point and no dispatching `switch` in the module. The agent calls the tool by the same name: in the list and in `tool_run`, the module `melbis_agent_currency.php` is `MELBIS_AGENT_CURRENCY`, and next to it the list shows the path through the registry tree, `Business -> Currencies`:

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

The function returns an array, and it travels to the agent whole — how it is built is described below, in "The Command's Response".

## The Routine Is the Engine's Job

Before the module, the engine checks everything that is written in the registry, and what is left for the module are the rules of the subject area:

- **the module** — the tool's address, in capitals, as in the list; it is checked
  against the registry, and no path of one's own to someone else's file arrives
  from outside;
- **the command** — by name, in any case; an unknown one is refused with a list of
  the declared ones, one that has not been granted is refused with an address:
  which command of which tool, and where it is granted;
- **the fields** — by the ones declared in the registry. A field the command has not
  declared is refused with a list of the accepted ones: a typo does not disappear
  silently. A required field left unset is refused. A field with the value `null`
  counts as unnamed — that is how the agent "clears" a field;
- **the types** — the type has to match, there is no silent coercion: whatever does
  not match comes back in words rather than as a quiet zero;
- **the defaults** — a value set in the registry is substituted into an unnamed field.
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
| `jsonl` | a stream of rows: a list of objects (see below) | array of rows |
| `files` | the call's attachments, not a field (see below) | rows of the file table in `$mParam['files']` |

**Days and hours** are brought to the form the database keeps them in, and "31 February" is rejected rather than sliding to the third of March. There is no need — and no reason — to check them in the module: the database is strict, and a word it cannot read ruins not the field but the whole answer.

**A stream of rows.** A command that loads a batch of homogeneous rows — imported products, attribute values, price-list prices — declares a parameter of type `jsonl`. The assistant puts the rows there as an array — straight into the call or, when there are many of them, into the `params_source` file of the call's fields, which its script writes. **The module gets an array in both cases** and reads no files at all.

The difference is not one of convenience but of possibility: a batch of five hundred rows typed by the assistant into the call parameters is hundreds of thousands of characters in its own message, whereas a file written by its script does not pass through it at all.

**A list instead of a single value.** Any type except `json`, `jsonl`, and `files` takes the suffix `/json` — `int/json`, `str/json`, `datetime/json`. Such a field accepts both a single value and a list, and the module **always gets an array**, so the "one or many" fork never appears in the code:

```php
$list = implode(',', $mParam['store_id']);   // int/json: the elements are already weighed
```

Every element is weighed by the same type as a single value — which is why `int/json` is exactly what makes substituting a list into `IN ( … )` safe. An empty list, and a list inside a list, are refused.

Multiplicity is a property of the registry row, not of the field as such: the same `price` may be `float/json` in a reading command (a range, a search) and `float` in a writing one, because a list cannot be written into a column.

**Files.** A command that accepts attachments declares one parameter of type `files`. In the signature it shows up not as a field but as a mark on the command — files are attached to the call rather than passed in the parameters. The engine lays them out before the module, and the command reads ready-made rows from `$mParam['files']`; a required `files` with no attachments is refused, and attachments to a command without `files` are refused. A command that has accepted files names them in the response under the `files` key — that is how the engine knows the files have an owner; ownerless ones it clears away itself.

## Inside a Command

- **The rules are the command's business.** The login format, the existence of a row,
  the right to an object: rights to a section, a product, an item are counted from the
  row named in the parameters and stay in the module.
- **Locks are avoided where possible.** A taken table is a refusal to everyone who
  comes after, and a command that crashes in the middle of writing leaves it busy
  until it is released by hand. A lock is needed where the same rows are edited by
  several people at once: products are described and priced by many employees at the
  same time. A task is moved only by the one who has it in hand — the only person
  they can collide with is themselves at another computer, so the "Scheduler" writes
  without a lock.
- **Tables taken — writing only.** All reads and checks are done before the lock;
  between taking and releasing there is nothing but writing. Busy tables come back
  as a ready-made refusal advising a later retry. The lock is written on behalf of
  the session's employee: the program's lock list shows whose request holds it.
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
- **Demolition takes a second call.** A delete command first answers with what would
  go together with the rows and what would be left referring to them, and deletes only
  on a repeat call that says `apply`. That is the confirmation — in the command's
  contract rather than in persuasion. Rows of sets — rights, links — are deleted at
  once: the module passes `apply` itself.

## The Command's Response

The command's function returns an array. The engine hands it to the agent whole, the MCP server lays the rows of the tables out into files, and the agent gets an answer of one form, whoever answered: the command or the engine that refused before it.

### The Keys

There are five keys, and the response always has them:

| Key | What it is |
|---|---|
| `result` | `true` — done, `false` — a refusal; there is no third value |
| `message` | what happened and what to do next: short and in English — the model reads it and retells it to the person in its own words |
| `detail` | the values the command returned: `id` of a new row, `ids` of several, `found` — how many were found, the state of the object after the action |
| `tables` | rows: named lists `{"<name>": [{…}, {…}]}` |
| `files` | the attachments the command has kept for itself |

`result` and `message` are written by the command. Whichever of `detail`, `tables`, and `files` it has not written, the engine adds empty: `{}`, `{}`, and `[]`. Everything the command returns beyond rows and attachments is put into `detail`.

There are no machine code words in the response: `result` plus text is enough for the agent. The values in `detail` are named in words of the subject area — `id`, `version_id`, `found` — rather than `data1`.

**Rows — only in `tables`.** Everything that may turn out longer than a few rows the command puts into `tables`. The MCP server writes every table into a file on the agent's computer and leaves in the answer the number of rows, the path to the file, and the first three rows. A list put into `detail` the agent will get whole, straight into the correspondence: a thousand products is hundreds of thousands of characters in its memory. Related data is returned as separate tables: the rows refer to one another by `id`, and the agent joins them with its own script. This is how the `melbis_inc_agent_table` library answers a read:

```php
return [
    'result'  => true,
    'message' => 'The tables asked for',
    'tables'  => ['currency' => $rows]
    ];
```

The table's name becomes part of the file's name, so it is one word: `currency`, `goods`, `errors`.

**Attachments.** A command that accepts files names the accepted ones under the `files` key (see "The Routine Is the Engine's Job"). If it has not named them — it refused or forgot — the engine clears away all the files of the call and appends to `message`: `The 2 file(s) sent with this call are gone: the command [CmdAdd] did not take them.`

### A Refusal

A refusal is `result: false` and a `message` that names the next step. Who refused does not matter to the agent: the engine before the module refuses in the same form.

| Who | When | Example of `message` |
|---|---|---|
| the engine | an unknown command | `Unknown command [CmdNope]. The tool has: CmdList, CmdAdd, CmdUpdate, CmdRemove` |
| the engine | the command is not granted | `The command [CmdRemove] of the tool [Business -> Currencies] is not granted to you…` — and where it is granted |
| the engine | a field the command has not declared | `The command [CmdAdd] has no field [nme] - …` — and which fields it accepts |
| the engine | a required field is missing | `The command [CmdAdd] needs [name] - …` — and the field's description |
| the engine | the type did not match | `The [id] takes a whole number, and [abc] is not one` |
| the command | a rule of the subject area | `Nothing was named to change` |
| the command | the table is busy | `The [currency] tables are busy` |
| the command | a deletion without `apply` | `Deleting 1 row(s) of <table>, and with them 3 <table>. Say apply` |

The last row is the first step of a demolition that takes a second call: the agent shows the person what will go along with it, and after their consent repeats the call with `apply: true`.

### A Crash

A refusal is an answer. A crash is when there is no answer: there is no tool with that address, the module is not found, it has no function for the command, the function broke off with an exception or answered without `result`. Then the engine answers with the text of the error, and the agent gets it as a refusal without JSON:

- `ACCESS_DENIED: no such tool: <UNIT>`;
- `Module not found! [units/<module>.php]`;
- `Function not found! [<UNIT>\<command>]`;
- `<UNIT>\<command> failed: <exception text>`;
- `<UNIT>\<command> answered without result`.

A command that catches the error itself and answers `result: false` with words is better than a crash: from the text of an exception the agent will not understand what to do next.

### What the Agent Sees

The MCP server gives the agent the command's response with the same keys. It writes the rows of every table into a file and puts in their place where they lie, and alongside it adds `time`:

```json
{"result": true, "message": "The tables asked for",
 "detail": {},
 "tables": {"currency": {"rows": 3,
                         "file": "D:\\Melbis\\shop.example.com\\mcp\\claude-code\\prices-sept\\tables\\MELBIS_AGENT_CURRENCY.CmdList.currency.jsonl",
                         "head": ["{\"id\":\"1\",\"code\":\"USD\"}", "…", "…"]}},
 "files": [],
 "time": {"server": 11, "module": 1, "trip": 46}}
```

- `tables` — for every table: `rows` — how many rows, `file` — where they lie,
  `head` — the first three rows as text, cut to 300 characters; with the `into` job
  also `job` and `job_rows`;
- `time` — `server`, `module`, and `trip`; with `debug` also `sql` and `sql_ms`.

The table's file is overwritten by the next call of the same command. `result: false` comes to the agent with the mark of error and the same JSON. Everything the call accepts and gives back — "[tool_run](../MCP/tool_run.md)".

### Examples

**Adding.** The module returns:

```php
return [
    'result'  => true,
    'message' => 'The row of currency is added',
    'detail'  => ['id' => 15]
    ];
```

The agent gets:

```json
{"result": true, "message": "The row of currency is added",
 "detail": {"id": 15}, "tables": {}, "files": [],
 "time": {"server": 14, "module": 3, "trip": 52}}
```

**A change in which nothing is named** — a refusal by the command itself:

```json
{"result": false, "message": "Nothing was named to change",
 "detail": {}, "tables": {}, "files": [],
 "time": {"server": 9, "module": 1, "trip": 44}}
```

**A deletion.** The first call, without `apply`:

```json
{"result": false, "message": "Deleting 1 row(s) of <table>, and with them 3 <table>. Say apply",
 "detail": {}, "tables": {}, "files": []}
```

The second call, with `apply: true`, after the person's consent:

```json
{"result": true, "message": "1 row(s) of <table> gone…",
 "detail": {}, "tables": {}, "files": []}
```

**A refusal by the engine** — the module was not started, so there is no `module` in `time`:

```json
{"result": false, "message": "Unknown command [CmdNope]. The tool has: CmdList, CmdAdd, CmdUpdate, CmdRemove",
 "detail": {}, "tables": {}, "files": [],
 "time": {"server": 6, "trip": 47}}
```

**Attachments not taken** — the command refused, and the two files sent have been cleared away:

```json
{"result": false,
 "message": "… The 2 file(s) sent with this call are gone: the command [CmdAdd] did not take them.",
 "detail": {}, "tables": {}, "files": []}
```

## The Descriptions Share the Work

The agent assembles a call from three layers of text, each with its own depth:

- the tool's `descr` — what it does and when to call it;
- the command's `descr` — the meaning and the behavioral caveats ("the password is not
  supplied and is returned only once");
- the parameter's `descr` — what this value is and what it should be; a dictionary of
  allowed values is named here in words (`a value of STORE_KIND_KEY`).

The `tool_list` list carries only the beginning of the tool's description — its first sentence — and its command words. The command descriptions and the parameters the agent takes one tool at a time, once it has settled on working with it.

## Libraries

The shared part of the tools lives in the library modules `melbis_inc_agent_*` (`system`, `table`, `query`, `store`, `file`). A module includes a library through the manifest (`includes: melbis_inc_agent_system.php=1`) and declares an alias:

```php
use MELBIS_INC_AGENT_SYSTEM as SYS;
// ...
$mine = SYS\RightOne('topic', $mUserId, 'descr', $topic_id);
```

The aliases it publishes are named by the library in its own manifest, in the `param_info` field — the engine checks `use` against them when saving.

## Backup and Update

**Development → AI Components → Export** (the `TOOL_EXPORT` right; for the agent — `tool_export`) collects the whole registry and the modules behind it into a single archive: `index.json` with the tree and the md5 sum of every file, `tools/<unit>.json` per tool — the commands with their fields — and `units/` with the modules, the manifests, and the libraries found through those manifests. Grants do not go into the archive: they are about the store's people, not about the tools.

There is no import door, and that is a decision: after installation the owner adapts the tools to their own needs, and an update is always a comparison rather than an overwrite. The agent unpacks the container, finds what has diverged by the sums in the index, and transfers it point by point: the registry by direct queries, the module by saving the file.

The MCP server documentation tells the agent where the current set of the distribution lies and how to update the store's tools from it — just ask.

## Reference Implementations

Live samples in the platform's demonstration store:

- `melbis_agent_key_value.php` — a compact entity: the dictionaries of the settings
  registry, five commands;
- `melbis_agent_user.php` — an entity with four commands: password generation,
  deletion guarded by history, a partial `CmdUpdate`;
- `melbis_agent_task.php` — a mirror of the program's scheduler: open tasks and the
  archive per person, the way the window loads them; task movement by separate
  commands, by the right of the assignee and of the author, as in the task card;
  privacy expressed by a single formula in every query.
