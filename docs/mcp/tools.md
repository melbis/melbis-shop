# AI-tools

The MCP-tools are the same in any Melbis Store. These are not: they were written
by the owner of **this** Store for the work done here. Add a product the way this
particular person adds products; close an order the way they close one.

**Call them what the Program calls them** — "AI tools": that is the word the User
sees on the tab in the AI settings, and the word by which they will find what you
are talking about.

Their list is data, not code. So a new AI-tool appears for you without a new
version of the Program: the owner adds a row to the registry, you reconnect, the
AI-tool is there.

**An AI-tool is not a consolation prize for someone denied access to the database,
it is the main road.** Even with full `engine_db_*` rights, run an entity that an
AI-tool covers through that AI-tool: the module carries the rules of this Store —
the checks, the locks, the authorship, the hints in its refusals — which a pool
would have to repeat by hand and with no guarantee of getting them right. And the
other way round: if a person has no direct right, that is usually not the owner
forgetting but deciding — they opened this one narrow door instead. Leave plain SQL
for what the AI-tools do not cover.

## How to find out what there is

The list arrives with the answer to `session_connect`. To read it again on its own
there is **`tool_list`**: useful if the owner added an AI-tool in the middle of the
conversation, or the list has scrolled far back.

Every entry of the list says four things:

```
  melbis_agent_sample.php       Sample
      What the AI-tool is for, in the owner's own words: the entity it keeps and
      the bounds it works in
      may: CmdList CmdAdd
      CmdList           What the reading command answers
      CmdAdd            What the adding command writes
```

The words there are the owner's own and come in the language of the Store, so a
Russian shop answers in Russian — the shape is what stays the same.

- **the unit** — and that is what the AI-tool is called by: the file its Commands
  live in;
- **the name and the description** — written by the owner, and the only thing that
  says what the AI-tool does. Read the description in full: the conditions and the
  reservations that exist nowhere else are in it;
- **the Commands** — the words of this AI-tool, its own: a word and its
  description. An AI-tool is an entity, a Command is one job of it;
- **`may:`** — which of those Commands are granted to **you**. You work under the
  login of the User, the rights are counted for them and handed out one Command at
  a time (see [session.md](session.md)). The list itself shows **every** AI-tool
  and **every** Command whatever the rights, so `may: nothing` means "the AI-tool
  is there, none of its Commands were granted" — say exactly that to the User, and
  name the place they are granted: the server sent the path along with the list.

## How to run one

Two steps, and the first is that same list:

1. **`tool_list`** — with no unit it says what there is and what of it is yours
   (`may:`); with a unit it answers the same but deeper: every Command with the
   fields it takes — name, type, whether it is required, the default. Call it
   before the first run of an AI-tool you have not touched in this session;
2. **`tool_run`** — the unit, the Command and its parameters:

```json
{ "unit": "melbis_agent_sample.php", "command": "CmdAdd",
  "params": { "name": "Acme", "descr": "Optics and tripods" } }
```

The unit is the address of the AI-tool, and the engine meets it against the
registry: there is no path from here to a file of your own choosing. An unknown
Command it turns away at once, listing the declared ones; an ungranted one with a
refusal carrying the full address — which Command of which AI-tool, and where it is
handed out. Pass that address to the User as it is, it is ready for use.

The fields are met by the engine as well. A field the Command never declared is a
refusal listing the ones it does take: a typo in a name no longer disappears in
silence. The type has to agree — a number as a number, `yes`/`no` or `true`/`false`
for a flag, `date`/`time`/`datetime` as a written day and hour (`2026-08-31`,
`2026-08-31 14:30`), json as a list or an object; whatever does not agree comes back
in words rather than as a silent zero. A required field missing is a refusal; an
optional one with a default behind it in the registry gets that default by itself.
So take the fields from the signature and not from memory: the signature is now the
very thing the engine judges by.

A field sent as `null` counts as a field not sent: that is how you unsay one and let
its default stand.

**The `/json` tail on a type means "one or a list".** `int/json` takes both `12` and
`[12,13]`; `float/json` on a price is a range, from and to; `datetime/json` is "from
this day to that one". A single value travels as a list of one — the module is handed a
list either way and never asks which of the two came — so what that one element means is
the module's to decide, and it is written in the description of that field and nowhere
else. An empty list is refused, and so is a list inside a list, both by the name of the
field.

## A field of type `jsonl`: rows from a file

A field of this type is **a stream of rows of one kind**: the goods of an import,
the values of a characteristic, the prices of a price list. There are two ways to
fill it:

```json
"goods": [ {"ref": "A1", "name": "..."}, {"ref": "A2", "name": "..."} ]
"goods": {"file": "C:\\...\\goods.jsonl"}
```

The second is a path to a file **on your machine**, one json object per line. The
rows travel to the Store straight from the file, past your answer; the module gets
the same list either way and knows nothing of any file.

**The rule is simple: anything you are not composing right now, send as a file.** A
row you have just thought up goes into the call itself, that is quicker all told.
But data that already exists somewhere — a price list, an export, the output of your
own scraping script, rows from `mcp\melbis\tables\*.jsonl` you read earlier —
must never be retyped: a hundred products dictated into parameters is hundreds of
thousands of characters of your answer, whereas as a file it is one call and one
second. A script writes the file, you only name it — see "The machine you work on"
in [index.md](index.md).

## Files attached to a call

A Command may take files as well — `tool_list` says which does, and the field of
type `files` is where they land. They are not parameters: they ride beside the call.

```json
"files": [ {"path": "C:\\...\\front.jpg", "entity": "store", "elem_id": 812} ]
"files": {"file": "C:\\...\\photos.jsonl"}
```

An entry names the local path and the element the file belongs to (`entity`,
`elem_id`, and `kind_key` for a slot other than `kBase`); the list itself may be a
jsonl written by a script, exactly as a stream of rows may. What each entry means in
detail is in [elements.md](elements.md).

One call is one request and cannot be split: a pack heavier than the transfer limit
of this Store is refused whole, and so is one holding more files than the php of the
Store accepts — the refusal names the number. A single file is never held to the
pack limit.

The Store lays the files down **before** the module runs, so an upload is a fact by
the time the AI-tool sees it. If the Command turns out not to keep them, the Store
sweeps them away again and says so in the answer — nothing is left lying around
owned by nobody.

## What to expect in the answer

The answer always holds `result`, an unambiguous `true` or `false` with no "almost"
in between, and `message`, human text that can be read out to the owner. Those two
belong to the AI-tool: `result: false` is a refusal — something said no — and
`message` is the reason, ready to be read out. It is one language for every refusal,
whoever refused: the engine stopping the call before the module (an unknown Command,
one not granted to you, a field the Command never declared, a value of the wrong
type) and the Command itself declining by the rules of its subject matter answer the
same way.

Everything else the module answered rides beside those two, at the same level — the
keys the Command answers with, whatever they are: the `id` of what was written, a
count, a word said once. There is no wrapping key around them. Only the Store knows
the shape of it; the description of the Command says what to look for.

**Rows of tables do not come into the conversation.** A Command that hands over data
puts it under the key `tables`, and every table is written to a file of its own,
`mcp\melbis\tables\<unit>.<command>.<table>.jsonl` — the unit without its `.php` —
one row per line. What stays
in the chat is the verdict and the first three rows of each table, clipped at 300
characters, so that the shape is visible; for the data itself go to the file. The
file is **overwritten by every call** of that Command: if you need the snapshot
later, copy it under a name of your own - or let the call gather it, as below.

## Walking a big selection

A search that finds more than one page is read page by page, and the file of the
Command is overwritten by each of them - which is exactly the wrong behaviour for a
walk. Name the job with **`into`**, and every table of the answer is also appended to
a file of that job, `mcp\melbis\tables\<into>.<table>.jsonl`:

```json
{"unit": "melbis_agent_sample.php", "command": "CmdQuery", "into": "walk",
 "params": {"query": { ... }, "limit": 1000}}
```

The answer then carries one line more per table - the file of the job and how many rows
it holds by now, so the walk can be seen while it runs:

```
goods: 1000 rows -> ...\melbis_agent_sample.CmdQuery.goods.jsonl
    + walk.goods.jsonl, 7000 rows in file
```

Three things about it:

- **it only ever appends, and nobody clears up after it** - the file of a Command is
  overwritten by the next call and so looks after itself; a file of a job is touched by
  nobody. It is yours: delete it when a walk begins, or the new walk continues the last
  one, and delete it again when the work on it is done;
- **the name is a bare name** - it becomes a file name, so a path in it is refused;
- **the ordinary file is written as always**, `into` takes nothing away from it.

And the point of the whole thing: that file feeds the next AI-tool without the rows ever
coming here. Any parameter takes `{"file": "..."}`, so ten thousand rows go from a
search into a change and never pass through the conversation.

## How long it took

The last line of the answer is the time, and it holds three different numbers:

```
time: 21 ms server (module 10, engine 11), sql 9 in 10 ms, 62 ms round trip
```

- **server** — how long php held the request, from the way in to the answer going
  out;
- **module** — how much of that was the AI-tool itself, **engine** — the door around
  it: the login, the rights, the fields against the registry, the packing;
- **round trip** — how long it took by the clock of this machine, the road included.

Read it like this. A long **module** is the work of the AI-tool itself: queries,
locks, images. A long **engine** with a quick module is the engine and its
start-up. A quick **server** with a long round trip is the road and your own
processing — the Store has nothing to do with it: do not blame the server before
you have looked at this line.

**`debug: true`** in the call adds `sql N in M ms` — how many queries the module ran
and how long they took together. Ask for it when a call turned out slow: an AI-tool
that is slow and one that is talkative are different troubles, and only the count of
queries tells them apart.

## Caution

**There is somebody else's code behind an AI-tool, and it can do more than it
says.** The owner writes the description; the module does what is written in the
module.

Permission to run is a grant: the owner gave this Command to this person
deliberately, and asking again on top of that is pointless. The real brake sits in
the Commands themselves: one that takes something away along with its tail first
answers **what** would follow, and acts only on a second call carrying the flag its
own description names. That answer is the one to read out to the User — it is the
place where the decision is made.

The refusal `ACCESS_DENIED: no such tool: <unit>` means one thing: there is no such
unit in the registry — call `tool_list` and check. The unit, the Command, the grant
and the fields are all checked by the engine **before** the module; the Command, the
grant and the fields refuse in the same language as the modules — `result: false` and
a `message` with a hint — while an unknown unit comes back as the plain refusal above.

## If there are no AI-tools

The answer "This store has no AI-tools of its own yet" means exactly that: the
registry is empty. That same answer names where the owner writes them — the exact
path through the menu of the Program, and the **AI tools** tab: the row of the
AI-tool, its Commands and the handing out of rights.

Do not confuse it with the other answer, the one that says the right to see the list
was not granted to you. That is a closed door, not an empty registry, and the User
must not be told there are none.

Whether to offer to write them is governed by the next section. An empty registry
simply means the occasion will come up on the very first task. How such a module is
written — [units.md](units.md).

## If there is no AI-tool for the job

You do the work anyway — with a pool of queries, by editing tables, however it goes.
But once it is done, **say what it cost**, without waiting to be asked the same
thing a second time:

> "Product added: eleven queries, three tables, the dictionary of kinds had to be
> checked by hand, the tables were held for a minute. As an AI-tool of the Store
> this would have been one Command — shall I write one?"

The numbers are in your hands at that very minute, and they tell the owner
incomparably more than "it took a while". Name the second gain as well, the less
obvious one: an AI-tool opens this work to **staff with no rights to the database**
— right now it is out of their reach entirely, whereas through an AI-tool the owner
hands it out one Command at a time, to whoever they see fit.

Two limits. **Do not offer to formalise a one-off job** — there is no gain in it.
And having offered once, do not come back to it: the decision is the owner's, not
yours.

If they agree, carry on with "If you are asked to write an AI-tool" below.

## If you are asked to write an AI-tool

Do not invent the shape — it already exists. In order:

1. **Read the "AI tools" chapter of the developer documentation** —
   `../Guide/Russian/Dev/agent_tool.md` (the path is relative to the folder of
   these notes): the registry, the contract of a call, the reference router, the
   rules for commands.
2. **Read the units of the AI-tools this Store already keeps** as living examples,
   with `engine_php_load` — `tool_list` names their units.
3. **Keep to the conventions**: an AI-tool is an entity and a unit, and a Command is
   a function of that unit — `CmdList`, `CmdAdd`; the `Cmd` prefix is what tells a
   Command from a helper. A unit declares a namespace of its own name in upper case,
   and the engine calls `MELBIS_AGENT_X\CmdList($mUserId, $mParam)` directly — no
   entry point, no dispatching `switch`, no description of itself in the code: the
   signature lives in the registry. What is common goes into the
   `melbis_inc_agent_*` libraries: included through the manifest and taken in with
   a `use ... as` alias. The unit, the command, the grant and the fields have already
   been checked by the engine; what is left for the module is the rules of the
   subject matter, and every refusal is `result=false` with a `message` hinting at
   the next step.
4. **The rows of the registry** are created by the owner in the Program, in the AI
   settings — the server named the path: the AI-tool itself (`agent_tool`), its
   Commands (`agent_tool_command`), the fields of each Command (`agent_tool_param`)
   and the rights. Prepare the finished texts of the words, the descriptions and the
   fields for them — that is the bulk of the writing there. If you hold the right to
   write to the database, a pool will do as well, announced like any other change of
   data.

And the general rules for modules still hold: the naming conventions (`rules.md`) and
the other sections from the table in [units.md](units.md) — before you write any code.

## Backup

**`tool_export`** puts the whole registry and the modules behind it into one zip on
**this** machine: `index.json` with the tree of AI-tools and a sum per file,
`tools/<unit>.json` per AI-tool — the Commands with their fields — and `units/` with
the modules, their manifests and the libraries. Grants do not go into the archive:
they are about the people of the Store.

For the owner this is a backup to take before touching anything. For you it is what
you compare against: unpack it, check the sums from `index.json` against what is in
the Store now, and read in full only what has drifted apart. There is deliberately
no import door — updating AI-tools is a negotiation, not a rollout: what is new, what
the owner adjusted for themselves and what may be carried over is for the two of you
to decide, and it is carried over piece by piece — the registry by queries, the
module by saving the file.

## The current set of the Distribution

The Distribution seeds its AI-tools into a Store once; from then on they are the
owner's. The platform keeps adding tools and reworking old ones, and none of that
reaches a Store by itself. The current set is the Distribution itself, public at
`https://github.com/melbis/melbis-shop`; a file comes straight from
`https://raw.githubusercontent.com/melbis/melbis-shop/master/` plus its path:

- `core/init/agent_tool.sql` — the registry as `INSERT` statements: the tree
  (`agent_tool`), the Commands (`agent_tool_command`), their fields
  (`agent_tool_param`);
- `units/<unit>.php` and `units/<unit>.json` — the module of each AI-tool, named in
  the `unit` column;
- the libraries its manifest names under `includes` — `melbis_inc_agent_*` and the
  like, in the same `units/`.

Go there when the User asks — what is new, or how a tool of the Store differs from
the current one. Take `tool_export` of the Store for the other side, then match by
meaning, never by id — the ids of the script are the Distribution's own: an AI-tool
by `unit`, a Command by its name inside the AI-tool, a field by its name inside the
Command. Compare modules by content: the header of each carries the build it came
with, so no sum of a Store's module ever matches. The `@version` line at the top of
`agent_tool.sql` names the build of the set; a tool newer than the engine of the
Store may lean on what that engine does not have yet. Carry over as above — by
agreement, piece by piece.

## What the module gets

The function of a Command is called by the engine as `CmdList($user_id, $params)`:
the id of the member of staff whose session this is, and the fields of the Command
as a ready array. The id is always a real one — there is no such thing as an
anonymous session — and it is what the module signs authorship with. By that moment
the fields have been weighed against the registry: nothing extra is in the array,
the required ones are in place, the types are cast, the defaults are filled in, and
the files attached to the call are laid down and lie under `files`. By that same
moment the engine has checked the person, the unit, the Command and the grant; what
is left for the module is the rules of the subject matter.
