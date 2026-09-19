# The MCP Server

The Melbis Shop MCP server is a bridge between the store and an application with artificial intelligence. The application calls the server's tools, the server turns every call into a request to the store's engine and returns the answer as text.

This section is a reference on the tools: what each of them does, what parameters it takes and what it answers. How to connect the assistant and grant it rights is described in the user guide — "[AI Assistant](../User/ai-assistant.md)". How to write tools of your own for the store — "[AI Tools](../Dev/agent_tool.md)".

## How It Works

```
agent application ──(stdio)──> MelbisMCP.exe ──(HTTP)──> the store's engine
                                    │
                                    └──> the store folder on the computer
```

* **The agent application** starts `MelbisMCP.exe` on the same computer as a child
  process. They exchange JSON-RPC messages through standard input and output. The
  server has no network port. The application names the version of the MCP protocol on
  connection: the server speaks versions 2024-11-05, 2025-06-18 and 2025-11-25, and if
  the application asks for another one, the server answers with its newest, 2025-11-25,
  and the application decides whether that suits it.
* **MelbisMCP.exe** lies in the program's installation folder. It works out by
  itself which store to sign in to, takes the login and the password itself, and
  signs the requests with the license — the same way the program does.
* **The engine** accepts the server's requests at its entry point `core/mcp.php`,
  checks the rights the same way as for the program, and carries out the command.

The server does not decide what to do: what is allowed is decided by the user's rights, and what is needed — by the agent together with the person. The rules of working with the tools, though, the server hands out itself, and it does not run a tool until the agent has accepted them.

## Terms

### The Participants

**A store** is a project on the Melbis Shop platform: the site and its database on the server. A session works with exactly one store.

**The program** is the Melbis Shop client for Windows, an employee's workplace. Stores are switched in it.

**The engine** is the PHP part of the platform on the store's server. Every read and every change of the store is carried out by it.

**An agent application** is a program with a language model that speaks MCP: Claude Code, Goose, Cherry Studio and others. It starts the MCP server and names itself on the first connection. The description of the MCP protocol calls it the host.

**The AI assistant**, or **the agent**, is the language model inside the agent application. It reads the descriptions of the tools, decides which one to call, and parses the answer.

**The user** is the person who writes to the agent: the owner, a developer or an employee.

The captions of the program's windows and fields are given here in English: in the language your program runs in they may differ.

### The Session and the Rights

**A session** is work with one store under one login. It is opened by `session_connect`, and it lives until the agent application restarts the server. The store, the login and the list of rights are remembered at the moment it opens. Grant a new right, and the session is opened anew.

An application opened on the store folder starts a server tied to this store: switching in the program does not affect it, and two stores can be run in two windows. A server without a tie signs in to the store last opened in the program and follows it: while another store is open there, the tools refuse with `STORE_SWITCHED`.

**Signing in under a user.** There is no separate user for the AI in the store: the agent works under a person's login, with their rights and in their name. The login-password pair the server takes itself — from the running program, and if it is closed, from the Windows registry (only when the "store passwords" box is ticked). The password never passes through the correspondence.

**A right** is a row of the "AI Assistant" branch in the store user's rights. One right can open several MCP tools at once: "Direct access → Development → Read data", for example, opens the file map, the search, and loading a module. Five tools require no rights: `session_init`, `session_connect`, `session_rules_accept`, `shop_page`, `shop_run`. In detail — "[Sign-in, Rights, License](access.md)".

**The license** is a daily token that every request is signed with. The tokens are obtained by the program, the MCP server only reads them from the store folder. A license is issued for a "domain + login" pair and is shared by the user and their agent.

### The Tools

**An MCP tool** is one operation of the MCP server: load a module, run a query against the database, open a storefront page. The set is built into the platform and is the same in any store. The agent sees the whole set whatever the rights are: the granted ones are listed in the answer of `session_connect`, and a call of the rest comes back as a refusal.

**The parameters** are the call's arguments, a JSON object. A parameter with the value `null` counts as unnamed. The schema of the parameters and the description of every tool the agent application receives on connection. The schemas and the descriptions are written in English: it is the model that reads them. On the tools' pages the name of an optional parameter stands in square brackets: [`reload`].

**A path** is the address of a file from the store's root, the same for all the file tools: `units/<module>.php`, `templates/<group>/units/<module>/<template>.htm`. A new name for a rename is passed in `new_path`, and for a template group — in `new_template`.

**The rules** are a text for the agent: how to work with a family of tools and what not to do. They lie as md files in English in the `Engine\MCP` folder of the distribution. The first call of a tool that has rules the server does not carry out: the rules and a code come back in the answer. The agent confirms them with the [`session_rules_accept`](session_rules_accept.md) tool and repeats the call. The code works only in the current session and changes together with the text of the rules. The rules that are tied to no tool — who the agent is in the store and whom it works with, the order of work, the sources, the store's memory — come in the answer of [`session_connect`](session_connect.md). For the documentation the rules refer to the English version of the guide, `Engine\Guide\English\`, which is built from the Russian one.

**An AI tool** is a tool the owner has written for their store: a module and its description in "Development → AI Components". Each of them consists of **commands**. The server runs them with two of its own MCP tools: `tool_list` shows what there is, `tool_run` carries out one command.

**The store's memory** is the agent's notes, kept in the store's database rather than on the computer. Notes of the "Critical" kind the agent is obliged to read at the start of a session: until then the server refuses every tool except reading the memory.

### The Name of a Tool

The name is made of the family, the subject and the action: `engine_php_save` — the engine, a php file, save.

| The start of the name | The family |
|---|---|
| `session_` | signing in to the store and accepting the rules |
| `engine_` | the store's files, data and settings through the engine |
| `memory_` | the store's memory |
| `tool_` | the store's AI tools |
| `shop_` | the store from outside: a storefront page, a module of one's own, a copy of the store |

The actions repeat from family to family: `load` — read, `save` — write an existing file, `add` — create, `rename` — rename or move, `remove` — delete. `dir_` before the action means the same for a folder.

### The Folders on the Computer

**The distribution** is the program's installation folder. In it lie `MelbisMCP.exe` and the `Engine\MCP` folder — the texts for the agent: the starting instructions and the tools' rules that the server hands out, and the recipes in `Engine\MCP\Recipes\` — the order of actions for particular situations, which the agent reads itself by the links from the rules. Installation replaces the `Engine` folder as a whole, so texts of one's own are not put there: the rules of a particular store are kept in its memory.

**The store folder** is the store's local folder on the computer. In it the program keeps the data and the settings, and the MCP server its own working folders:

| File or folder | What it is |
|---|---|
| `Shop.ini` | the connection settings: the server's address, the login, the engine's version, the transfer limits |
| `DATABASE.FDB` | the program's local database: the data downloaded from the server and the edits not saved yet |
| `FormDesign.ini` | the position and the size of the program's windows |
| `*.xml` | the settings of the program's windows, the profiles among them |
| `tokens\` | the daily license files: the program obtains them, the server only reads them |
| `files\` | a copy of the element files folder from the server: both the program and `engine_files_load` fill it |
| `mcp\<application>\` | the agent's working folder: reports, scripts, what has been downloaded to be studied. The name is the one the agent application called itself by |
| `mcp\melbis\` | the server's disposable folder: downloaded pages, images, large query results. It can be wiped at any moment |
| `.mcp.json`, `.claude\`, `.codex\`, `AGENTS.md` | how applications opened on this folder as a project start the server, tying it to this store, and call its tools without asking for permission — once the user has trusted the folder to the application. The program lays them down from `Engine\MCP\Setup\` of the distribution at every start, rewriting them whole |

## The Answer

Every call gets one answer: one block or several, and a mark of whether the call succeeded.

* **Success** — the data in JSON, if the tool returns any, and then the text: what this
  case calls for, the contents of a file, explanations. A downloaded image comes as a
  separate block — an image that the model sees. Anything bulky the server puts into
  `mcp\melbis\` as a file and names the path.
* **A refusal** — one text block with a mark of error. The first word is usually a code
  in capitals with a colon (`ACCESS_DENIED:`, `NO_SETTINGS:`, `RULES_REQUIRED:`), and
  after it the explanation: what is wrong and who fixes it.

The server's answers are written in English. The paths of rights and the names from the store come in the store's language. All the common refusal codes are gathered on the page "[Answers and Refusals](answers.md)", and the refusals that only one tool has — in its description.
