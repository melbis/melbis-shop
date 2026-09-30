# AI Components

The **"Development → AI Components"** window holds everything the store gives the AI assistant, on three tabs:

* **"AI Tools"** — commands written for your store, and the employees'
  permissions for each of them;
* **"AI Memory"** — the assistant's notes about the store and about working with
  each employee;
* **"AI Repository"** — sections in which the employees' assistants keep working
  data for themselves and for each other.

How the assistant uses all of this is described in "[AI Assistant](ai-assistant.md)". The window is edited **in lock mode**: changes are applied with the "Save" / "Save and Exit" buttons (see "[Three Data Working Modes](basics.md)"). The right "Development → AI Components → Get" allows opening the window, "Save" allows saving it.

## AI Tools {#tools}

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

On the left is the **"Tool Catalog"**, on the right the **"Tool Commands"** of the selected tool, and below them the selected command has two tabs: **"Command Parameters"** and **"Command Rights"**.

The catalog is arranged like the other trees in the program: folder sections for grouping, with the group flag set by a button on the toolbar. **A folder is not a tool** — it has neither commands nor permissions, and the assistant does not see it.

A command can be moved to another tool: rows dragged from the command table onto a tool in the catalog move over to it. A folder does not accept them.

### The Tool Card

| Field | What it defines |
|---|---|
| Name | the name of the row in the catalog; the assistant sees it as the tool's title |
| Module | the module file name in the `units` folder; **this is also what the assistant calls the tool by** |
| Description for the AI assistant | what the tool does and when to call it |

**The description is read by the model, not by a person, and it is the only thing the model uses to decide whether to take the tool.** So it is written as an instruction, not as an interface caption: what it does, when to call it, what it does not do, what the caveats are. Anything the description leaves out, the assistant will make up for itself.

The module is prepared by a developer — how such a module is arranged is described in the "[AI Tools](../Dev/agent_tool.md)" section of the developer guide.

### Commands

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

### Rights

A right is granted **per command** — by a checkmark at the intersection of "command × employee or group". A tool has as many separate permissions as it has commands: you can allow reading the list without allowing adding and changing. The rights of an employee and of their groups add up.

**Rights are handed out to people, not to the assistant.** The assistant works on behalf of the employee who started it (see "[AI Assistant](ai-assistant.md)"), and the grant is counted for that person. Hence the fact that the same tool can do different things for different employees.

**The command, the grant, and the fields are all checked by the engine itself, before the module.** An unknown word, a command that has not been granted, and a field that does not match all come back as a ready-made refusal, and the module never sees them — what is left for it are the rules of the subject area.

> The assistant sees the full list of tools and commands regardless of rights — and about a command that has not been granted it answers with an address rather than "access denied": which command of which tool, and where it is granted. That address is what to pass to the administrator as is. An employee who is allowed to save AI settings owns all commands automatically — which is what the footnote under the permission tables says.

### Ready-Made Tools

The demonstration store ships with a ready-made set: products with their prices and descriptions, the catalog and attributes, files and image profiles, the settings registry, employees, scheduler tasks. The exact list does not live in the documentation — it is the store's data: the full and always fresh list stands in "Development → AI Components" on the "AI Tools" tab, and the assistant takes this same list before its first task.

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

## AI Memory {#memory}

Notes the assistant keeps about the store: working rules, employees' directives, decisions and their reasons. The assistant reads them at the start of work, so the next session — on another computer, in another application — does not start from scratch. The whole memory is visible here: whoever can open the window sees the notes of all employees.

On the left is the **"Users"** tree: "All users", under it the groups, and in each group its employees. The node decides whose note it is:

| Node | Whose note | Who writes it |
|---|---|---|
| "All users" | for all employees | the administrator, in this window |
| group | for the group's employees, main or additional | the administrator, in this window |
| employee | the employee's own | this employee's assistant, with their consent; the administrator — here |

On the right are the **"Notes"** of the selected node, with rows coloured by type, and under the table the text of the current note. The order is changed with "Move Up" and "Move Down".

**To hand a note over**, drag it from the table onto another node of the tree: the note moves there. With **Ctrl** held down it is copied — this is how a rule the assistant wrote down for one employee is given to the whole group while the author keeps it.

While the window is open, the assistant can neither write a new note nor delete its own: the window holds the memory in work. When you are done, close it.

### The Note Card

| Field | What it defines |
|---|---|
| Type | the kind of note, see below |
| Category | free words by which notes are grouped; the list offers the ones already in use, new ones can be typed in |
| Time | when the note was last edited; set automatically |
| Name | the note's short name |
| Description for the AI assistant | one line on what the note is about |
| Note text | the note itself; a double click opens the visual editor, the field's second button the source HTML |

Whose note it is is not set in the card: it is the tree node under which the note was added.

**The assistant reads memory in two passes:** first the names and descriptions of all notes, then the text of those that are relevant. So the description is written for that choice — what the note is about, not a retelling of it.

| Type | What it is |
|---|---|
| Critical | what is observed in every session; the assistant reads these notes before any other work |
| Directive | an employee's instruction |
| Skill | how to work with this person |
| Default | everything else |

Custom types are added in the "[Settings Registry](registry.md)", on the "Basic Settings" tab; the assistant treats them as "Default".

**When notes disagree,** "Critical", "Directive" and "Skill" outrank the other types, and among them a note for everyone outranks a group note, and a group note outranks a personal one.

## AI Repository {#repo}

Sections in which the employees' assistants keep working data — for themselves for later and for each other: unfinished work, intermediate results, parsed sites, ready scripts. Sections and their rights are set here, while topics and records are written by the assistants — in the window they are only read.

On the left are the **"Repository Sections"** — a tree with the usual actions: add a section or subsection, edit, delete, move, drag a branch. A folder (the group flag) only groups sections: it has neither topics nor rights. A new subsection gets a copy of its parent's rights; after that they are edited separately.

### The Section Card

| Field | What it defines |
|---|---|
| Type | "Personal" — working data of one employee's assistant; "Shared" — what the assistants of all employees need; "Default" — no mark |
| Name | the section's name in the tree |
| Key | a short name by which the assistant can find the section |
| Description for the AI assistant | what the section is for: by it the assistant decides what to put here |

The type restricts nothing: who sees the section is decided by its rights.

### Access Rights

The **"Access Rights"** tab of the selected section: **"Permissions for User Groups"** and **"Permissions for Users"**, six checkboxes per row. A click on a cell sets or clears the checkbox.

| Checkbox | What it allows the assistant |
|---|---|
| Topics — List | see the section's topics |
| Topics — Create | create new topics |
| Topics — Edit | change the name, description and parameters of any topic of the section |
| Topics — Delete | delete any topic together with all its records |
| Data — Read | read the topics' records |
| Data — Write | add records |

The rights of an employee and of their groups — main and additional — add up. A section without a single checkbox is invisible to the assistant. An employee with the right to save this window has all sections open with all checkboxes. A row with all checkboxes cleared is removed when the window is saved.

The checkboxes decide which action goes through inside a section. The repository itself is opened to the assistant by the employee's rights in the "AI Assistant → Repository" branch (see "[Users and Access Rights](users.md)").

**An employee's personal section.** Create a section with the "Personal" type, name its owner in the description, and give rights only to that employee. If everyone has a personal section, one memory note for everyone can tell the assistants how to find their own: for example, a "Personal" section whose key equals the employee's number (ID).

### Topics and Data

The **"Topics and Data"** tab shows what the assistants have written into the selected section. Everything comes from the server by buttons:

* **"Get Section Topics"** — the **"Topics"** table: topic, description and
  parameters — free words by which the topic is found;
* **"Get Topic Data"** — the records of the selected topic in the **"Data"**
  table.

**A record does not change:** each new one is added to the topic, the latest is where the topic stands now, the earlier ones are its history. So "Get Topic Data" fetches only the records newer than those already received, and a record's text arrives when the cursor lands on it.

A record has three tabs:

* **"Summary"** — the time; the user — the employee whose assistant wrote it; the
  author — the assistant itself: the model and its settings; the comment — one
  line on what the record changed;
* **"Content"** — the whole state of the topic;
* **"Appendix"** — data attached to the record, which the assistant reads only
  when it is needed.

The content and the appendix are shown as **text**, as a **script** (with Python highlighting) or as an **HTML document** — the display is chosen in the list.

Viewing topics and records is allowed by the rights "Development → AI Components → Repository → Get topics" and "Get data". The section checkboxes act on the assistants, not on this window.

## Export {#export}

The **"Export"** button on the tool catalog toolbar collects all tools with their commands and fields, their modules and libraries into one archive `<store> tools <date>.zip`. This is how a set of tools is moved to another store or kept in reserve. Permissions do not go into the archive — they are about the people of this store; nor do memory and the repository.

The archive is assembled by the server from what has already been sent to it: unsaved changes in the window will not get into the archive, which the program warns about. The right "Development → AI Components → Export" is required.

There is no loading back, and that is a decision: in every store the tools are refined for its own needs, so the archive is not rolled onto a store but compared with it. Give the archive to the assistant and ask it to carry over what has diverged — it will compare and carry things over point by point.
