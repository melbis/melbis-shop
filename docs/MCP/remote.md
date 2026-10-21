# The Server

The `remote` family works on the server where the store stands: it runs commands and moves files between this computer and the server. These are the same actions the owner performs in the "[Server](../User/server.md)" window: over SSH, as root.

| Tool | What it does |
|---|---|
| [`remote_exec`](remote_exec.md) | runs a bash command and gives back the output and the exit code |
| [`remote_load`](remote_load.md) | downloads a server file onto this computer |
| [`remote_save`](remote_save.md) | writes a file onto the server |

Everything done on the server itself — a folder listing, a move, a deletion, a new folder — is done by `remote_exec`. Separate tools are needed only where bytes travel between computers: a command's output will not carry a binary file and will not hold a big one. A file is edited like this: `remote_load`, the edit on this computer, `remote_save`.

## Access

The tools require no store rights: they do not turn to the engine. Access is given by the program, and only while in the "Server" window on the "Connection" tab:

* the window is open;
* the IP address and the root password are entered;
* the "Allow the AI assistant to access the server while this window is open" checkbox is ticked.

Before every call the MCP server asks the program for the address and the password. A closed window, an unticked checkbox or an empty password — and the call gets a refusal that names what is missing. When the window closes, the checkbox clears itself. The password reaches only the MCP server's process on this computer; it does not get into the correspondence.

The connection is kept between calls and is closed as soon as the program stops handing over the same server.

**The server's key** is remembered at the first connection in the file `mcp\known_hosts` of the store folder. If the server shows another key — it was reinstalled, or someone has stepped into the way — the connection is rejected. After the server is reinstalled, the line with its address is deleted from the file.

**Confirmation in Claude Code.** The `remote_*` tools are not in the list of allowed ones that the program puts into the store folder, so Claude Code shows every call of theirs for confirmation.

## The Rules

The family has rules of its own — `remote.md` in the `Engine\MCP` folder. The first call of any `remote_*` tool in a session refuses with them; after they are confirmed through [`session_rules_accept`](session_rules_accept.md), all the tools of the family work.

The main thing in them: this is a working server under root, and nothing can be undone. A command that deletes, stops, restarts, installs or changes a setting is first shown whole and runs after the owner agrees. A file is changed through a copy next to it. Passwords, keys and the values of `config.json` are not put into the correspondence.

While the window is closed, the agent dictates the commands and the owner runs them themselves — the order is described in the recipe `investigate_store_server_through_owner.md`.

## Refusals

Common to all three tools. The refusals of a particular tool are on its page.

| Answer | When |
|---|---|
| `Melbis Shop with this store open is not running here…` | the program with this store is not running |
| `The window «Server» is closed…` | the "Server" window is closed |
| `The window «Server» is open, but the assistant is not let in…` | the access checkbox is not ticked |
| `The address or the root password is empty in «Server»…` | the IP address or the password is not entered in the window |
| `This Melbis Shop hands no server over: it is older than this MCP server…` | the program is older than the MCP server, a new one is needed |
| `Melbis Shop did not answer the question about the server.` | the program did not answer |
| `No SSH to <address>: <reason>` | the server did not accept the connection: a wrong password, an unreachable address |
| `The server <address> showed a key other than the one remembered…` | the server's key does not match the remembered one |

The common refusals of the MCP server — "[Answers and Refusals](answers.md)".
