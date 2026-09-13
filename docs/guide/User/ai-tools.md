# AI Tools

Everything the AI assistant can do is the same in any Melbis store: files, data, trees, the storefront. **AI tools are the exception: the owner writes them for their own store.** Create a product exactly the way this store creates one, with its duplicate check and its price calculation; close an order by its rules.

The list of tools is **data, not code**: a row you add appears for the assistant on its next connection, and neither the program nor the engine has to be updated for that.

A tool is worth creating when you see one of two things:

* **the work is being assembled by hand.** What the assistant puts together from a
  pool of queries every time is worth writing up as a module once — then it becomes
  a button both for the assistant and for the employees who have no database rights
  at all. There is no need to wait for the third repetition: the assistant will
  suggest it itself as soon as it has done such work by hand, and will name the
  price — how many queries and tables it took;
* **the store has its own order of business.** The assistant should not have to
  guess which field you count duplicates by or where you take the price from: that
  is written in the module once.

## Where They Are Created

**"Development → AI Tools"**. On the left is the tool catalog, on the right are the commands of the selected tool and the permissions for each of them.

The catalog is arranged like the other trees in the program: folder sections for grouping, with the group flag set by a button on the toolbar. **A folder is not a tool** — it has neither commands nor permissions, and the assistant does not see it.

## The Tool Card

| Field | What it defines |
|---|---|
| Name | the name of the row in the catalog; the assistant sees it as the tool's title |
| Module | the module file name in the `units` folder; **this is also what the assistant calls the tool by** |
| Description for the AI assistant | what the tool does and when to call it |

**The description is read by the model, not by a person, and it is the only thing the model uses to decide whether to take the tool.** So it is written as an instruction, not as an interface caption: what it does, when to call it, what it does not do, what the caveats are. Anything the description leaves out, the assistant will make up for itself.

The module is prepared by a developer — how such a module is arranged is described in the "[AI Tools](../Dev/agent_tool.md)" section of the developer guide.

## Commands

| Field | What it defines |
|---|---|
| Command | the word by which the assistant names one piece of the tool's work |
| Description for the AI assistant | what the command does, when to call it, and what it does not do |

The order of rows is set with the arrows — the assistant sees the commands in that same order.

**A command's name is the name of the module function** that carries it out: `CmdList`, `CmdAdd`, `CmdUpdate`, `CmdRemove`. The `Cmd` prefix tells a command apart from the module's internal functions, which the assistant does not see.

The list of commands is also a guard against invention: a word the tool has not declared is rejected by the engine straight away, and the reply lists the declared ones.

**A command's fields are declared here too**, one row per field: name, description, type (number, string, yes/no, day, hour, json, line stream, files), required flag, and default value. The engine checks every call against these rows before the module: an extra field, a missing required one, or a number sent as a word all come back as a refusal — the module's developer never gets this routine at all.

**`/json`** can be appended to a type — then the field accepts both a single value and a list: `int/json` for "one product or several at once", `float/json` for a price range, `datetime/json` for "from this day to that one". Every value of the list is checked by the engine against the same type, so a list stays as well guarded as a single value.

The **line stream** type (`jsonl`) stands apart — it is for tools that load a batch of uniform records: products from a price list, attribute values, prices. The assistant passes either the lines themselves into such a field or a file from its own machine, one record per line — and then the data travels to the store bypassing the conversation. The difference is tangible: a hundred products take the assistant minutes to dictate, and a second to load from a file its own script has written.

## Rights

A right is granted **per command** — by a checkmark at the intersection of "command × employee or group". A tool has as many separate permissions as it has commands: you can allow reading the list without allowing adding and changing. The rights of an employee and of their groups add up.

**Rights are handed out to people, not to the assistant.** The assistant works on behalf of the employee who started it (see "[AI Assistant](ai-assistant.md)"), and the grant is counted for that person. Hence the fact that the same tool can do different things for different employees.

**The command, the grant, and the fields are all checked by the engine itself, before the module.** An unknown word, a command that has not been granted, and a field that does not match all come back as a ready-made refusal, and the module never sees them — what is left for it are the rules of the subject area.

> The assistant sees the full list of tools and commands regardless of rights — and about a command that has not been granted it answers with an address rather than "access denied": which command of which tool, and where it is granted. That address is what to pass to the administrator as is. An employee who is allowed to save AI settings owns all commands automatically — which is what the footnote under the permission tables says.

## Ready-Made Tools

The demonstration store ships with a ready-made set: products with their prices and descriptions, the catalog and attributes, files and image profiles, the settings registry, employees, scheduler tasks. The exact list does not live in the documentation — it is the store's data: the full and always fresh list stands in "Development → AI Tools", and it is the same one the assistant sees on every connection.

A couple of examples of what that looks like:

- **Scheduler** — the agent creates a task on behalf of an employee, and it
  appears for the assignee as a new one (see "[Scheduler](planner.md)");
- **Prices** — reads and changes a product's price fields with exactly the same
  columns as the program's price window;
- **Profiles** — the recipes for derived images, the very ones set in the
  program's image editor — in plain words instead of XML (see
  "[Files](files.md)");
- **Product Import** and **File Import** — a pair for a batch: the assistant
  prepares the data as a file (a site parser, a price list, an export) and creates
  hundreds of products with their attributes in one go, and with a second call
  lays out the photographs, cutting the derived images by the profile along the
  way. Products are born **hidden** and "out of stock" — a batch is looked at
  first and opened to the customer afterwards.

They also serve as samples for developing your own.
