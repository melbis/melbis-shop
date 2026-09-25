# AI Tools

The `tool` family works with the store's AI tools — the modules the owner has written for the agent to work in this particular store: create a product the way it is done here, close an order the way it is closed here. The MCP tools are the same in every store, the AI tools are each store's own.

How the tools are built and written — "[AI Tools](../Dev/agent_tool.md)" in the developer documentation; how the owner creates them and grants the rights — "[AI Tools](../User/ai-tools.md)" in the user guide.

## The Rules

The family has rules of its own — `tool.md` in the `Engine\MCP` folder. The first call of any `tool_*` tool in a session is refused with them; once they are confirmed through [`session_rules_accept`](session_rules_accept.md), every tool of the family works.

## A Tool and a Command

A tool is an entity, a command is one piece of work with it: `CmdList`, `CmdAdd`. The list of tools is registry data rather than code: a tool the owner added today the agent sees in the next `tool_list`, with no new version of the program. `session_connect` tells how many tools the store has.

| What | Where it comes from |
|---|---|
| the address — `unit` | the tool's module in capitals: `melbis_agent_currency.php` is called as `MELBIS_AGENT_CURRENCY` |
| the path — `path` | where the tool stands in the program's tree of AI tools, its own name last |
| the description — `descr` | the owner's words: what the tool does and within what bounds |
| the commands | `may` — the ones granted to this login, `also` — the rest |

The list shows **all** the tools and **all** their commands whatever the rights are: an empty `may` means "the tool is there, but not one of its commands has been granted to this login".

## The Call

`tool_run` names the tool's address, the command and its fields. Before the module, the engine checks everything against the registry: the address, the command, the right to it, the fields and their types; what is left for the module are the rules of the subject area. The field types, `/json`, the `jsonl` line stream and the `files` attachments are described in "[AI Tools](../Dev/agent_tool.md)". How every parameter of the call passes through the MCP server, Melbis Core and the module, and what it turns into on the way — "The Path of the Call" on the "[tool_run](tool_run.md)" page.

Many commands in one call — [`tool_pool`](tool_pool.md), on the same right "Tools → Execute": a batch or a chain where a step takes the `id` from the `detail` of the step before it.

**Fields and attachments from a file.** Everything that is not typed out right now — the rows of an import, a long product description, a query — a script on this computer writes into a file, and the call names the file instead of the value: `params_source` is a JSON file with the same object of fields as `params`, `files_source` is a JSON file with the same list of attachments as `files`. The values in the file are already of their own types: the description as a string, the records as a list, the query as an object. The file goes to the store past the correspondence; it has to be in UTF-8, and a BOM mark at the beginning is stripped.

**The attachments** travel in one request and are not split: a batch heavier than the store's transfer limit, or with more files than the server's PHP accepts, is refused whole; a single file is not subject to the batch limit. The store lays the files out before the module starts, and if the command's response does not name them, it clears them all away; a list of which the command has kept only a part it sorts out itself.

**A pool** goes step by step in order, and every step is an ordinary `tool_run` call without attachments. The first refusal stops the pool, and what the steps before it wrote stays: there is no rollback.

## The Answer

The full form of the answer — the keys, the tables, the refusals, the crashes and the examples — is described in "The Command's Response" of the developer guide, "[AI Tools](../Dev/agent_tool.md)"; what comes to the agent for a call — "[tool_run](tool_run.md)". In short:

A command's response always has the same keys. `result` is `true` or `false` with no "almost", `message` is human text that can be read out to the owner. `result: false` is a refusal, whoever does the refusing: the engine before the module, or the command itself by the rules of the subject area. `detail` holds the values the command returned, and their meaning is written in its description; `tables` holds its rows, and `files` the attachments it has kept for itself.

**The rows of the tables do not come into the correspondence.** A command gives the data back under the `tables` key, and every table is written into its own file `tables\<unit>.<command>.<table>.jsonl` in the conversation folder, one JSON row per line of the file, and a pool step's tables into `tables\step<number>.<unit>.<command>.<table>.jsonl`. Every call of that command overwrites the file. What stays in the answer is the number of rows, the path, and the first three rows, cut to 300 characters.

**The `into` job** appends the tables into the file `tables\<into>.<table>.jsonl` in the conversation folder as well — that is how a large selection read page by page is gathered into one file. This file only grows, and it goes only together with the conversation folder.

**The time.** The answer carries how long the call lasted on the server, how much of that the module took, and the whole round trip by this computer's clock; with `debug` — also the number of the module's queries and their total time. A long module is the tool's own work, a long server with a fast module is the engine around it, a fast server with a long round trip is the road.

## Backup and the Current Set

`tool_export` collects the whole registry and the modules behind it into a single archive on this computer; what lies in it — "[AI Tools](../Dev/agent_tool.md)". There is no import door: updating the tools is a comparison and a transfer piece by piece.

The distribution puts its AI tools into the store once, and after that they belong to the owner. The current set lies in the public repository `github.com/melbis/melbis-shop`, a file is taken from the address `https://raw.githubusercontent.com/melbis/melbis-shop/master/` plus the path:

| File | What it holds |
|---|---|
| `core/init/agent_tool.sql` | the registry as `INSERT` rows: the tree, the commands, the fields; the `@version` line at the beginning names the build of the set |
| `units/<unit>.php`, `units/<unit>.json` | every tool's module and its manifest |
| `units/melbis_inc_agent_*` and the other libraries from the manifest's `includes` | the tools' shared code |
