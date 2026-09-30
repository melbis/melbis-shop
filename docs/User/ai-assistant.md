# AI Assistant

The AI assistant is a separate agent application: you state the task in words, and it reads what it needs itself, does the work itself, and reports on what it did. It is tied to the store by a bridge — the **MCP server** that ships with Melbis Shop and speaks to the engine over the same protocol as the program itself.

Any application will do, as long as it speaks MCP. The main integrations are Claude Code, OpenAI Codex and OpenCode: for them the program prepares the settings itself. Melbis Shop is not bound to any of them: the application names itself on the first connection and gets all of its instructions there — nothing has to be set up for it in the store in advance.

Three things are required for this to work:

* **An agent application** installed on the same Windows machine as the Melbis
  Shop program. This is not a web service and not a browser extension: the
  assistant starts a local process, and that cannot be done from a browser.
* **Payment for the agent program** — separate from the Melbis license, and
  each employee has their own; how much and for what depends on the program
  chosen, and OpenCode can also work for free.
* **Rights from the "AI Assistant" branch**, granted to your store user. The
  assistant needs no separate account and never has one — see "Rights".

Do not confuse it with **AI Help** — that one is built into the Workbench editor, sees only the text you show it, and changes nothing (developer guide, "[AI Help](../Dev/ai_help.md)"). The rule is simple: fits in a single file — AI Help; needs the store itself — the assistant.

## What It Can Do

It sees the whole store:

| Area | What it does |
|---|---|
| Project files | reads and edits modules, templates, statics, images; keeps the version history, clears the cache |
| Data | reads and changes tables as a pool of steps over a single connection, with locks and cache marking |
| Trees | the catalog, reference directories, and any others: creates, moves, and deletes nodes with index recalculation |
| Item files | attaches product photos and attachments, and fetches them back |
| Store copy | fetches the store's entire code, profiles, or a database dump — the same as the program's "Copy store" button; `config.json` is not included in the copy |
| Storefront | opens store pages as a visitor and reads the debugger report |
| Documentation | reads the manuals and table descriptions straight from the distribution — which is why its answers about Melbis are specific rather than general |

## Setup

**"System → Connection → AI Assistant"** — and there is nothing to set up there. The tab holds a ready **MCP server configuration** and a **"Copy"** button: copy it and paste it into the settings of your agent application, where it asks about MCP servers. Nothing else is needed from you — which store is open the assistant works out by itself.

The same configuration, but tied to a single store, the program puts into the store's own folder — for applications that open a folder as a project: `.mcp.json` and `.claude\settings.json` are read by Claude Code, `.codex\config.toml` and `AGENTS.md` by Codex, `opencode.json` and the same `AGENTS.md` by OpenCode. An assistant started from them works **with that store only**, whatever is open in the program: open the store's folder, and the correspondence about different stores does not pile up in one heap. Once the application asks whether you trust this folder and you confirm, the assistant will call its tools without asking permission for every call: its limits are set by your rights in the store. The files are rewritten every time the program starts, so adding anything of your own to them is pointless; they are taken from the `Engine\MCP\Setup\` folder of the distribution.

There is no login or password here: the assistant signs in to the store **as you**, with the same pair you used to sign in to the program on the "Connection Parameters" tab.

It obtains that pair on its own, by one of two routes. **The program is running** — it answers the assistant straight from its own memory. **The program is closed** — from the Windows registry, where the password is stored encrypted, but only if the "store passwords" box is ticked. There is no third route: **naming the password in correspondence is not allowed** — the assistant will not accept it, and it never appears there.

The **"Check Connection"** button walks the whole path for real — license, password, rights — and explains in words what is wrong, before the first session.

## Rights

The AI assistant **works in your store with your own hands**, so the question of what it is allowed to do is settled where it is settled for people: in the user's rights.

It has no separate account. What it has is a **separate branch in the operations registry — "AI Assistant"** — and that is not a formality: the `Load` right for a person and the `AI Assistant → Direct access → Development → Read data` right for their assistant are different rows. So you can keep your own hands full and leave the assistant with read access only — and the other way around, whoever has not been granted the branch at all simply has no assistant.

Three consequences follow, worth keeping in mind:

* **the same assistant can do different things for different employees** — according to their rights;
* **its actions are signed with their name** — in the activity log, in the version
  history, in task authorship; you cannot tell "did it themselves" from "did it
  through the assistant" by the name;
* **no separate license is needed** — it works with the same domain+login pair as
  the program, and they share the daily token.

The assistant does not work around prohibitions: when refused, it names the missing right — and names it **as a path through the rights tree**, not as an internal command name, because a path is exactly what you see in the program.

The only exception to all of the above is a store that has just been installed: while the `admin.here` file lies in its root, the engine checks neither the license nor the password, and the assistant works right away. It will warn you about this itself: in such a store anyone who knows the address becomes an administrator, so finish the installation before going live.

## Working with It

State the task as a goal, not as a sequence of actions: "the home page needs a bestsellers block" rather than "open such-and-such file and add such-and-such line." It will work out the arrangement itself and **lay out a plan before changing anything** — it gives separate warning about editing files and about changing data, and waits for your agreement.

The first task in an unfamiliar store goes slowly: the assistant looks around — it reads the project map, the documentation, the modules. This is a preparation stage; after it things go noticeably faster: it **offers to save what it has understood in the store itself**, and next time it does not start from scratch — see "[What the Assistant Remembers](#memory)".


On connection, the assistant reports how many open tasks are waiting for you in the "[Scheduler](planner.md)", counted by state — if you have the right to the scheduler. How often to look into it after that, it will ask once and offer to remember the answer.

> The assistant is not a replacement for a developer. It quickly does what you understand yourself, and its edits are worth reading as a colleague's edits: before publishing to the live storefront, look them over.

## What the Assistant Remembers {#memory}

The correspondence stays in the agent application on your computer. Everything that has to outlive the conversation the assistant keeps in the store itself, in its database: that way it is available from any computer and from any application, and it travels with the store.

Where to put what is decided by one question: is it needed in every session, later, or by other employees — or only in this conversation.

| Where | What is there | Who writes |
|---|---|---|
| [Store charter](../charter.md) | the business's established rules: processes, employee duties, the procedure for working with orders, suppliers, the warehouse, and products | employees, according to their rights to the charter's sections |
| Memory | what is important and lasting for the assistant's work: your instructions, how to work with you, the decisions made and their reasons; read at the start of every session | the assistant — its own notes, with your consent; the administrator — notes for groups and for everyone |
| Repository | working data that is read when the task calls for it: unfinished work, intermediate results, analysed sites, ready-made scripts | the assistant, when it has been sent there; the administrator sets up the sections and the rights |
| The `mcp` folder next to the store | the disposable material of this conversation: downloaded pages, images, drafts; only on this computer, wiped without regret | the assistant |

**Charter.** A new store gets a sample charter right away, and the assistant turns to it from the first session; in a store without a charter it will offer to set one up. Who reads the charter and who edits it is decided by the section access rights in the "[Catalog](catalog.md)". Established rules belong in the charter, not in memory: the memory is left with the everyday.

**Memory.** The assistant offers to write down what it has understood: it names the rule, the name, the category, and the type of the note, and writes it only if you agree. Its notes belong to the login they were made under — another employee has their own memory. Notes for a group or for everyone are written by the administrator: they take precedence over the assistant's notes, and the assistant does not change them itself. Notes of the "Critical" type — those for everyone, for groups, and its own — the assistant reads at the start of every session: until it has loaded them, the MCP server does not carry out any of its other commands.

Notes pile up quickly, so from time to time the assistant offers to review them, and it deletes what is no longer needed only with your consent. All of the memory is visible in the "AI Components" window, and whoever can open it sees the notes of all employees. While the window is open, the assistant will not write new notes — it will offer to put them into the "[Scheduler](planner.md)" if the scheduler tool has been granted to it.

**Repository.** Sections are set up by the administrator: a personal one — for a single employee, shared ones — for everyone; who sees what in a section is decided by its rights. The assistant writes there when you have sent it there or when a memory note says so — the same note may also say which section is your personal one. In other cases it only offers: "this is worth saving in your personal section" — and writes it down if you agree. Having written it, it names the section and the topic, so that it is clear where to look.

A record in the repository does not change: each new one is added to the topic, so the whole history of the work is visible. Your personal section is not seen by the assistants of other employees — except the administrator: whoever can save the "AI Components" window has all sections open. If you have no personal section, the assistant will advise you to ask the administrator for one.

How the administrator maintains the memory and the repository — "AI Components", the "[AI Memory](ai-tools.md#memory)" and "[AI Repository](ai-tools.md#repo)" tabs.

## What It Can Do Beyond the Basics

Everything described above is the same in any Melbis store. On top of it the assistant has **AI Tools** — ready-made commands written for your store: create a product the way it is done here, close an order by your rules. The owner sets them up, and they are handed out one command at a time to each employee — see "[AI Tools](ai-tools.md#tools)".
