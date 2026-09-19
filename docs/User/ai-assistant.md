# AI Assistant

The AI assistant is a separate agent application: you state the task in words, and it reads what it needs itself, does the work itself, and reports on what it did. It is tied to the store by a bridge — the **MCP server** that ships with Melbis Shop and speaks to the engine over the same protocol as the program itself.

Any application will do, as long as it speaks MCP: Claude Code, Goose, Cherry Studio and others. Melbis Shop is not bound to any of them: the application names itself on the first connection and gets all of its instructions there — nothing has to be set up for it in the store in advance.

Three things are required for this to work, and all of them are mandatory:

* **An agent application** installed on the same Windows machine as the Melbis
  Shop program. This is not a web service and not a browser extension: the
  assistant starts a local process, and that cannot be done from a browser.
* **Payment for the application** — its own, separate from the Melbis license,
  and each employee has their own; how much and for what depends on the
  application chosen.
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

The same configuration, but tied to a single store, the program puts into the store's own folder — the `.mcp.json` file. An assistant started from it works **with that store only**, whatever is open in the program. This is for applications that open a folder as a project: open the store's folder, and the correspondence about different stores does not pile up in one heap. The file is rewritten every time the program starts, so adding anything of your own to it is pointless.

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

The first task in an unfamiliar store goes slowly: the assistant looks around — it reads the project map, the documentation, the modules. This is a preparation stage; after it things go noticeably faster, all the more so because it **offers to write down what it has understood into the store's own memory**: it names the rule, the name, the category, and the kind of the note, and writes it only if you agree. Then next time it does not start from scratch. That memory lives in the database rather than on the computer, so it travels with the store; the assistant's notes belong to the login they were made under — another employee has their own. Besides them, there are notes the administrator writes for a group of employees or for everyone at once: they take precedence over the assistant's own notes, and the assistant does not change them itself. Notes of the "Critical" kind — those for everyone, for groups, and its own — the assistant reads at the start of every session: until it has loaded them, the MCP server does not carry out any of its other commands. All of the memory is visible and editable in "Development → AI Components" on the "AI Memory" tab — and whoever can open this window sees the notes of all employees. While the window is open, the assistant will not write new notes — it will offer to put them into the "[Scheduler](planner.md)" if the scheduler tool has been granted to it. The `mcp` folder next to the store has nothing to do with this: it holds disposable material — downloaded pages, images, the assistant's drafts — and is wiped without regret.

Notes pile up quickly, so from time to time the assistant offers to review them, and it deletes what is no longer needed only with your consent. Once there are more than a hundred notes, it will tell you it is time to set up a **store charter**: the project's established rules are better kept in the store itself — as separate document products in a catalog section of their own. Who reads the charter and who edits it is decided by the section access rights in the "[Catalog](catalog.md)", and a note for everyone tells the assistant where the charter lies. The memory is then left with the everyday.

On connection, the assistant reports how many open tasks are waiting for you in the "[Scheduler](planner.md)", counted by state — if you have the right to the scheduler. How often to look into it after that, it will ask once and offer to remember the answer.

> The assistant is not a replacement for a developer. It quickly does what you understand yourself, and its edits are worth reading as a colleague's edits: before publishing to the live storefront, look them over.

## What It Can Do Beyond the Basics

Everything described above is the same in any Melbis store. On top of it the assistant has **AI Tools** — ready-made commands written for your store: create a product the way it is done here, close an order by your rules. The owner sets them up, and they are handed out one command at a time to each employee — see the next section, "[AI Tools](ai-tools.md)".
