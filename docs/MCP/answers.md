# Answers and Refusals

## The Form of the Answer

Every call of a tool gets one answer of one or several blocks and a mark of error (`isError` in the MCP protocol). The agent application passes the blocks to the model, and the mark tells it whether the call was carried out or not.

* **Success** — up to three blocks, in order:
  1. **the data** in JSON — for the tools that return it; the form is described on the
     tool's page;
  2. **the text** — what this case asks of the agent, the bodies of files, explanations;
  3. **the image** — for an image that has been downloaded.

  An empty block is not sent. Anything bulky — pages, images, large selections — the
  server puts into the conversation folder as a file and names the path.

  If on connection the application has agreed on protocol version 2025-06-18 or a newer
  one, the data also comes in the result's `structuredContent` field — the same JSON
  object, but as data rather than text. The text block with the data remains in place:
  the MCP standard advises this for the sake of applications that read only text.
* **A refusal** — always one text block with a mark of error. It usually begins with a code
  in capitals and a colon, and after it the explanation: what is wrong and who fixes it.

The texts of the answers are in English: it is the model that reads them. The paths of rights and the names from the store come in the store's language.

Below are the refusals common to all the tools. The refusals that only one tool has are described together with it.

## Refusals of the MCP Server

These answers the server gives itself, without turning to the store.

| Answer | When | What to do |
|---|---|---|
| `Not connected. Call "session_connect" first.` | the session is not open: there has been none yet, the sign-in failed or the agent application restarted the server | sign in anew |
| `STORE_SWITCHED:` and both stores | the server is not tied to a store, and the store open in the program is not the one the session is open in | ask the user: `session_connect` signs in to the program's store, while returning the program to the former store continues the session |
| `ACCESS_DENIED: this user may not run AGENT_…` and the path in the tree of rights | the login has not been granted the right to this tool | the owner grants the right at the named path, the agent opens the session anew |
| `RULES_REQUIRED:` and the text of the rules | the tool's rules have not been accepted in this session | read the rules, call `session_rules_accept` with the code from the refusal, repeat the call |
| `The store asks you to read its kCritical notes…` | the critical notes of the memory have not been read; until then only `memory_list` and `memory_load` work | read the notes |
| `LICENCE_NONE:` | a licensed store has no license file for today; the text names the folder the server looked in | open the store in the program: an open session starts working by itself as soon as the file appears, and on sign-in — repeat `session_init` and `session_connect` |
| `Unknown tool:` | there is no tool with such a name | — |
| `ERROR:` | an internal error of the server | report the text of the answer to the developer |

## Refusals of the Store

These answers come from the engine: the request reached the store and the store did not accept it. In the language your program runs in the captions may differ.

| Answer | When | What to do |
|---|---|---|
| `WRONG_PASSWORD` | the login-password pair did not fit | sign in to the program anew, so that it passes the right pair, or save the password again |
| `ACCESS_DENIED` without a path | the user is blocked, or the right was taken away in the middle of the session | open the session anew: if the right has been taken away, the refusal comes with a path |
| `IP_BLOCKED` | the computer's address is not in the "Access from IPs" list | "[Installation](../User/installation.md)", the "Security" section |
| `SHOP_IS_LOCK` | the store is closed for the time of the works: `shop.lock` lies in the site's root | wait for the end of the works |
| `BACKUP_TIME: <start> - <end>` | the daily backup window is on, users are not let into the store | wait for the end of the window |
| `BACKUP_SOON: <start> - <end>` | there is less than half an hour to the backup window; roughly every tenth request gets this refusal instead of an answer | repeat the request and finish the work before the window begins |
| `LICENCE_ERROR` | the token does not fit the login and the day | open the store in the program, so that it obtains the license |
| `LICENCE_DOMAIN` | the address of the request does not match the domain the license is issued for | check the store's address in the connection settings |
| `LICENCE_DATE` | the computer's clock differs from Greenwich time by more than 10 minutes | set the clock |
| `VERSION_SERVER:` | the program needs a newer engine | update the server in place: the "Server" window, the "Maintenance" tab, "Update Server". Not "Installation" — it puts the server up from scratch and wipes the store |
| `VERSION_CLIENT:` | the engine needs a newer program | update the program |
| `VERSION_FAIL:` | the versions of the program and the engine do not match exactly; the text names both | bring the versions to one |
| `COMMAND_TIMEOUT: <start> - <end>` | the owner has allowed this command in these hours only | wait for the allowed time or ask the owner to change the hours in the user's rights |
| `COMMAND_LOAD_MAX: <1> <5> <15>` | the server's load is above the limit the owner has set for this command; the three numbers are the average load over 1, 5 and 15 minutes | repeat later |
| a reason without a code: `Unit not found: <name>`, `Module not found! […]`, `PHP Runtime Exception…` | the engine could not carry out the command: there is no file, no module, an error in the module | act by the text |

## Refusals the Server Turns into Words

Some of the engine's answers the server does not retell with a code but explains at once.

| Answer | When |
|---|---|
| `The table … is in work right now, and nothing was written…` | the table is held by another employee or by an open window of the program; nothing was written. Who holds it — `engine_db_locks` |
| `File not found: … Check the path against engine_map_tree.` | loading a file that does not exist: the path is checked against `engine_map_tree` |
| `This store has no endpoint for the agent: core/mcp.php is missing…` | a web page came instead of the engine's answer. Usually it means that there is no `core/mcp.php` on the server and the storefront answered; the file appears with an update of the engine. The same page can be given out by a bot protection in front of the site as well |
| `The server of the store refused the request as too large (HTTP 413)…` | the request is larger than the store's server accepts: a list of files is split into several calls, and for the sake of one large file the owner raises the server's limit |
| `Unexpected answer:` | something came that looks neither like the engine's answer nor like a web page; the text shows the beginning |
| `The store answered with something that is not json…` | the engine answered, but inside it is not json |
