# AI Dispatcher

The AI dispatcher is an AI agent that works in the store all the time and takes up work on its own. It is woken by store events: an order has arrived, prices have been saved, a price list has been loaded. On each event it acts the way it has been taught — checks, counts, sets tasks for people — and writes down what it has done.

It is the same agent application as the [AI assistant](ai-assistant.md), and it is tied to the store by the same MCP server. The difference is in who leads the work.

## Assistant and Dispatcher

**The AI assistant is led: an employee leads it.** The employee sets a task, the assistant lays out a plan, waits for agreement, does the work, and reports. Until the person writes, the assistant stays silent.

**The AI dispatcher takes the initiative: events lead it.** There is nobody at the keyboard and nobody to ask, so everything it needs to know is written down in the store in advance — in its memory notes.

| | AI assistant | AI dispatcher |
|---|---|---|
| Who starts the work | an employee, with a task in the correspondence | a store event |
| Under whose login | under the employee's login, with their rights | under its own login, with its own rights |
| Where it runs | on the employee's computer, while they are working | on its own computer, all the time |
| How it knows what to do | from the employee's task and from memory | from memory only: a note for each event |
| If something is unclear | asks | sets a task for a person and finishes |
| How it finishes | with a report to the employee | with a task in the Scheduler and a record in its own repository section |
| What it remembers between conversations | memory and the repository; the correspondence stays with the employee | only memory and the repository: every run is a new conversation |

They do not replace each other. The assistant does what an employee has decided to do right now. The dispatcher does what must always happen in the store, without reminders: handling every new order, checking every upload, the morning summary.

## How an Event Goes

Take a new order in the demonstration store:

1. A customer places an order on the storefront — or a manager does it in the
   "[Orders](orders.md)" window. The store writes the `kNewOrder` event with the
   order number to the log.
2. On the AI dispatcher's computer the Melbis Shop program is running under its
   login. Its "[Dispatcher](dispatcher.md)" learns of the event within a second
   or two.
3. The "New order" notification is enabled in the Dispatcher, and the
   `claude.bat` command is set in it. The Dispatcher runs the command and tells
   the agent's MCP server the event number itself, together with the login.
4. The command opens a new conversation with the agent. The agent signs in to the
   store and receives the event at once, then reads its notes, the note on the
   `kNewOrder` event, and the order.
5. Following the note, it sets the administrator a task in the
   "[Scheduler](planner.md)": order number, customer, total — and makes a record
   in its own repository section.
6. The conversation ends. The administrator sees the new task in their
   Scheduler.

In the demo store it takes about 40 seconds from placing the order to the task.

## What It Can Be Given

An event is anything the store can notice:

* **an operation in the program** — any operation in the "Operation Catalog" can
  be given an event key (the "[Users and Access Rights](users.md)" window, the
  "Operations and Access Rights" tab), and the store writes an event after every
  execution of it: prices saved, a price list loaded;
* **a module event** — a store module writes the event itself when something of
  its own happens: an order, a review, a reply from a payment system, a scheduled
  task (see "[Event Tracking](../Dev/events.md#modules)").

How events reach the program and where they are set — "[Events](dispatcher.md#events)" in the "Dispatcher and Locks" section.

Examples of assignments:

| Event | Source | What the AI dispatcher does |
|---|---|---|
| New order | the order module; present in the demo store | sets a task for the administrator, marks the order in its section |
| Prices saved | an operation of the "Prices" window with an event key | finds products priced below the purchase price and sets a task with their list |
| Price list loaded | the import operation in the "Price Lists" window with an event key | checks new products for photos and descriptions, sets a task for what is missing |
| Working-day morning | a scheduled-task module, once a day | assembles a summary for yesterday — orders, totals, returns — and puts it into a shared repository section |
| New review | the reviews module on the storefront | checks the review for spam and rudeness, sets the moderator a task with its assessment |

An operation's event says only who performed which operation. What exactly has changed the agent finds out by itself — for example, by the modification time of the products.

## Event Cascade

Every run of the AI dispatcher is a conversation with the model: it costs money and takes tens of seconds. Calling it on every frequent event — every order, every price save — is expensive, and it is needed only in some of the cases.

So a frequent event first passes through a filter — a scheduled store module. It sorts out the event by simple rules and, only when the agent cannot be done without, writes its own, special event with the reason. The AI dispatcher is subscribed to the special event.

For example, once a minute the module looks through new orders and, for a large one — above a set amount — writes "Order needs attention". The agent handles only such orders; the rest pass it by.

The filter is written by a developer — see "[Event Cascade](../Dev/events.md#cascade)".

## How to Teach It

The AI dispatcher knows exactly as much about the store as is written in the store itself. It is taught with three things: memory, the repository, and AI tools — all of them in the "[AI Components](ai-tools.md)" window.

### Memory: Its Own Notes

The assistant learns in conversation: it offers to write down what it has understood, and the employee agrees. The dispatcher has nobody to learn from, so its notes are written by the administrator, in advance, under the dispatcher's login in the "AI Memory" tree.

Three kinds of notes are needed:

| Note | Type | What is in it |
|---|---|---|
| Working procedure | Critical | who it is: an agent with no person at the keyboard; how to read an event; what to do if there is no note for the event or something is unclear — set the administrator a task and finish; what never to do — delete, overwrite, and send what cannot be undone |
| Working with the repository | Critical | which section is its own, how to keep topics and records in it (see below) |
| Event `<key>` | Directive | one note per key: what to do step by step, with which tool, whom to set a task for, and what to write into the section |

Notes of the "Critical" type it reads at the start of every conversation, an event note — when its event arrives. A new assignment is a new event note and a subscription to the event in the Dispatcher; nothing else has to be rewritten.

**Notes for everyone are written for people.** The dispatcher reads them too, and much of what is in them — "ask who you are talking to", "plan first, then agreement" — does it harm: it will stop and wait for an answer that will never come. Such a note begins with a line saying that it is not for the dispatcher's login. Notes that everyone needs — for example, where the [charter](../charter.md) lies — leave shared: the dispatcher reads the charter the same way the employees do.

That is how it is done in the demo store. Under the `agent` login lie "AI dispatcher at work" (the working procedure), "Storing data in the repository" (working with the repository), and "Event kNewOrder" (the directive for a new order). The shared "Working with a person" and "Storing data in the repository" begin with the line "Not for the user with the login "agent"". The demo store's notes are written in English — translate them into the store's language, as you do the charter.

### Repository: Memory Between Runs

Every run is a new conversation: about the previous run the agent knows only what it has written down itself. So the dispatcher has its own section in the "[AI Repository](ai-tools.md#repo)" — of the "Personal" type, with rights for its login only:

* **a topic** — one event key: `kNewOrder`, `kPricesSaved`;
* **a record** — one handled event: the time, what has been done, what is left.

The last record of a topic is where the work on that key stands; the agent reads it before taking up a new event. That way it will not handle an order twice and will carry on with what was interrupted. People see the records in the "AI Components" window on the "AI Repository" tab — it is a log of everything the dispatcher has done, with an explanation of why.

A result for people — a summary, a report, a list — goes not into the dispatcher's personal section but into a shared section that the employees' assistants see; which one, the event note says.

In the demo store it is the "AI-Dispatcher" section with the key `agent`, the topic `kNewOrder`, one record per order.

### AI Tools: Its Hands

The dispatcher does the same things as the assistant, but more strictly: through the store's [AI tools](ai-tools.md#tools), not with direct database queries. The tool already holds the store's rules — how a task is set, how an order is read — and the dispatcher does not have to guess them.

Grant its login only the commands its notes need: usually marking the event handled, setting a task, and reading what the event is about. A new command in its note means a new tick in the rights to that command.

## Rights and Security

The only limits on the dispatcher are its rights: there is no person beside it to stop anything superfluous.

* **Its own group.** Give the dispatcher a group with narrow rights rather than
  putting it among the administrators. In the demo store it is in the
  administrators group only to keep the example simple.
* **Data are not instructions.** A customer's name, an order comment, the text of
  a review can be written by anyone. For the agent this is material to examine;
  it takes instructions only from its own notes.
* **Nothing irreversible.** Anything doubtful is a task for a person, not an
  action. That is exactly what the demo store's working procedure says.
* **Do not subscribe it to its own events.** If an operation that the dispatcher
  performs itself writes an event it is subscribed to, it will keep waking itself
  up without end.

## Assignments with an Employee's Rights {#proxy}

An event can assign the dispatcher work with the rights of a particular employee — this employee is called the proxy. The employee's number is written into the event by a store module, and only an event addressed to the dispatcher by number or by group passes rights on. An event for everyone passes no proxy.

* **Its own rights first.** Everything granted to the dispatcher it does itself
  and in its own name.
* **What is missing — with the proxy's rights.** An AI tool command that the
  dispatcher has not been granted but the employee has, the server performs with
  the employee's rights: with their catalog sections and orders, and the store
  signs it with the employee's name.
* **The agent chooses nothing.** It neither names nor changes the proxy: the
  server decides for every command by itself and writes in the answer whose
  rights it went through.
* **Its own stays its own.** The dispatcher's MCP tools, memory, and repository
  always work with its own rights.

So whatever the dispatcher must do only with employees' rights is simply not granted to it. For example, without a price change command of its own, the dispatcher changes prices only on assignment from an employee who is allowed to, and only in that employee's sections. How AI tool commands are granted — "[AI Tools](ai-tools.md)".

## Installation

### 1. Computer

A separate computer or virtual server running Windows that works around the clock. On it:

* **Melbis Shop**, connected to the store under the dispatcher's login. This is a
  separate workplace: the dispatcher's login needs its own license, like any
  employee.
* **An agent application for the terminal** (CLI) with a paid subscription — Claude
  Code, Codex CLI or OpenCode. It has to be the terminal version: the Dispatcher
  launches the agent with a command, with no window and no person. The application
  must speak MCP; Claude Code is taken below as an example.
* **Python** — preferably right away. The agent processes data with its own
  scripts: it parses an export, reconciles lists, prepares a file for import. A
  script does this in seconds and does not spend the subscription on going through
  rows in the conversation. During installation tick "Add python.exe to PATH" so
  that the agent finds it by name.

Each application signs in the way its vendor prescribes. Claude Code signs in with a long-lived key: run `claude setup-token`, and write the key you receive into an environment variable:

```bat
setx CLAUDE_CODE_OAUTH_TOKEN "key"
```

The variable is visible to programs started after this command — restart Melbis Shop. The key is access to your subscription: do not forward it or show it to anyone.

Codex signs in once with the `codex login` command or with a key in the `CODEX_API_KEY` variable. Codex reads the settings from the store folder only if it has been trusted with that folder: open `codex` in the store folder once and confirm the trust. OpenCode — with the `/connect` command in its window: there you choose the model provider, for example DeepSeek, and enter its key.

### 2. User

In "[Users and Access Rights](users.md)" — a separate user for the dispatcher, with its own group and password. The demo store already has one: the login `agent`, "AI Dispatcher"; it has no password set, and nobody can sign in under it until you set one.

The group's rights in the "Operation Catalog":

| Operation | Why |
|---|---|
| "Business → Dispatcher → Notification check" | the program's Dispatcher receives events |
| "AI Assistant → Memory → Get list", "Load" | the agent reads its notes |
| "AI Assistant → Repository" — all three operations | the agent keeps its section |
| "AI Assistant → Tools → Get list", "Execute" | the agent works with AI tools |

Then, in "AI Components": its notes, its repository section, the AI tool commands for its login.

### 3. Event Subscription

An event reaches the Dispatcher if its key is in the "[Settings Registry](registry.md)", the "Dispatcher" → "Event list" branch; "New order" (`kNewOrder`) is there from the very start.

On the dispatcher's computer, in the "Dispatcher" window, the "Notifications" tab: tick the event in "Notification Activation" and enter the "Command" that starts the agent. The command receives the event's key, number, and time in the environment variables `MELBIS_EVENT`, `MELBIS_ID`, and `MELBIS_TIME`. The ready agent commands do not read them: the agent's MCP server receives the event number from the program, and the agent sees the event itself when it signs in to the store.

The ready commands lie in the store folder: `claude.bat` — for Claude Code, `codex.bat` — for Codex, `opencode.bat` — for OpenCode with the DeepSeek V4 Flash model. The program puts them there on every start, from the `Engine\MCP\Setup\` folder of the distribution: the file opens a conversation with the agent. The model and the flags are changed in the `Engine\MCP\Setup\` folder itself — the copy in the store folder is rewritten on every start. For another application such a file is written by you.

### 4. Check

* **"Test"** next to the command saves it and runs it at once, with the event
  number `0`. The agent signs in to the store, learns that this is a test run,
  and finishes its work without writing anything. That it has signed in is seen
  by the conversation folder `mcp\<application>\event\<day>\0` in the store
  folder: the server creates it at sign-in.
* **A real event** — for example, a test order on the storefront. Check the task
  in the Scheduler, the record in the dispatcher's section, and the `Command =>`
  line with the event key and number in the "[Activity Log](journal.md)".
* To watch the agent at work with your own eyes, run the command in the store
  folder with the `-test` key: `claude.bat -test` opens the conversation in a
  window, as with the assistant, while `codex.bat -test` and `opencode.bat -test`
  show the course of the work and do not close the window at the end.

### 5. Putting It into Service

The program on the dispatcher's computer is always running, under its login; Windows does not go to sleep and does not end the session. The Dispatcher window can be closed: the request to the server goes on without it.

Every event is a separate conversation and a separate spend of the agent application's subscription. A new assignment is added the same way: an event note, a subscription, a check.

**Conversation folders.** Every run works in its own folder `mcp\<application>\event\<day>\<event number>` in the store folder, and the folders pile up. They are cleaned by the owner — for example, a scheduled task writes its own event once a day, and the Dispatcher on the dispatcher's computer runs a command on it that deletes the days older than a week.
