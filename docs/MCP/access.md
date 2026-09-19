# Sign-in, Rights, License

How the MCP server gets into the store, what the agent is allowed there and what every request is signed with. The sign-in tools themselves are described on their own pages: [`session_init`](session_init.md), [`session_connect`](session_connect.md), [`session_rules_accept`](session_rules_accept.md).

## Sign-in

**The store.** A session opens in the store last opened in the program. While another store is open in the program, the session's tools refuse with `STORE_SWITCHED`. The server can be tied to one store: in the agent application's configuration it is started with `--store <store folder>`. Then it signs in to that store only, whatever is open in the program, and a tie to a folder that is not in the program's list of stores does not open a session at all.

**The login and the password.** If the program is running on this store, the pair comes from it — it is the person sitting at the program right now. If it is not — the login is taken from `Shop.ini` in the store folder, and the password from the Windows registry, where the program keeps it encrypted when the "store passwords" box is ticked. There is no third way: the password does not pass through the correspondence and the agent does not see it.

**Who the agent works as.** There is no separate user for the AI in the store: the agent works under a person's login and with their rights. Everything the agent does the store signs with that person's name: the author of a task, the author of a file's version, the owner of a note, the one who holds a table.

## Rights

Behind every MCP tool there is a command of the engine — `AGENT_` and the name of the tool in capitals: `engine_php_save` → `AGENT_ENGINE_PHP_SAVE`. The commands are granted by the rights of the "AI Assistant" branch in the user's rights. In the language your program runs in the captions may differ.

| Branch | Right | Right key | Tools |
|---|---|---|---|
| Memory | Get list | `AGENT_MEMORY_LIST` | `memory_list` |
| | Load | `AGENT_MEMORY_LOAD` | `memory_load` |
| | Save | `AGENT_MEMORY_SAVE` | `memory_save` |
| | Delete | `AGENT_MEMORY_REMOVE` | `memory_remove` |
| Tools | Get list | `AGENT_TOOL_LIST` | `tool_list` |
| | Execute | `AGENT_TOOL_RUN` | `tool_run` |
| Direct access → Store files | Load | `AGENT_ENGINE_FILES_LOAD` | `engine_files_load` |
| | Add | `AGENT_ENGINE_FILES_ADD` | `engine_files_add` |
| | Delete | `AGENT_ENGINE_FILES_REMOVE` | `engine_files_remove` |
| Direct access → Database | Get data structure | `AGENT_ENGINE_DB_TABLES` | `engine_db_tables` |
| | Read data | `AGENT_ENGINE_DB_SELECT` | `engine_db_select` |
| | Modify data | `AGENT_ENGINE_DB_EXECUTE` | `engine_db_execute` |
| | Get lock list | `AGENT_ENGINE_DB_LOCKS` | `engine_db_locks` |
| | Release table locks | `AGENT_ENGINE_DB_UNLOCKS` | `engine_db_unlocks` |
| Direct access → Development | Read data | `AGENT_ENGINE_DEV_READ` | `engine_map_tree`, `engine_map_units`, `engine_search`, `engine_history_list`, `engine_history_content`, `engine_php_load`, `engine_html_load`, `engine_static_load`, `engine_image_load`, `engine_whole_load` |
| | Modify data | `AGENT_ENGINE_DEV_WRITE` | every `add`, `save`, `rename` and `remove` of modules, templates, statics, images and their folders, and of template groups as well |
| | Get configuration | `AGENT_ENGINE_DEV_CONFIG` | `engine_dev_config` |
| | Clear cache | `AGENT_ENGINE_DEV_CACHE_CLEAR` | `engine_dev_cache_clear` |
| Export | AI Tools | `AGENT_TOOL_EXPORT` | `tool_export` |
| | Store | `AGENT_SHOP_DOWNLOAD` | `shop_download` |

`session_init`, `session_connect`, `session_rules_accept`, `shop_page` and `shop_run` work without rights. One exception inside them: `shop_page` with `debug=true` reads the debugger's code from the configuration and therefore requires the "Get configuration" right.

The "Development" branch divides the work with the project's files into reading and changing only, and this is not a simplification: a login that may save a module can already do to the store everything the store itself can do. That is why "Modify data" includes "Read data": a save begins with loading the file.

**The rights belong to the person.** The agent signs in under the user's login, so with one employee it can do more than with another, in one and the same store.

**When they are checked.** The list of rights comes on sign-in, and a call of a tool that has not been granted the server refuses itself, without turning to the store — and it names the missing right as a path in the tree of rights:

```
ACCESS_DENIED: this user may not run AGENT_ENGINE_PHP_SAVE.
The owner grants it in the program, to the user you signed in as
- in the user rights, <path in the store's language>.
The session has to be opened again afterwards.
```

After the right is granted the session is opened anew: the list does not change in the middle of the work. If a right was taken away in the middle of a session, the call passes the server and it is the store that refuses — with a bare `ACCESS_DENIED`, without a path. It means the login's rights have changed; after reconnecting the refusal comes with a path.

## License

Every request is signed with a daily token. The tokens are obtained by the program while it runs, and it puts them into the store folder, into `tokens\<login>-<date>.lic`. The MCP server only reads them and does not request a license itself.

A license is issued for a "domain + login" pair, so the token is shared by the employee and their agent: there is no separate license for the AI.

No file for today — and on a licensed store the session will not open: `session_connect` answers `LICENCE_NONE` and names the folder it looked in. There is one cure for it: open the store in the program, then `session_init` and `session_connect` anew. The engine refuses a token with codes of its own — `LICENCE_ERROR`, `LICENCE_DOMAIN`, `LICENCE_DATE` (see "[Answers and Refusals](answers.md)"). The server looks for the token anew at every request, so the change of day in the middle of a session does not close it: until the program has written the file for the new day, every command gets a `LICENCE_NONE` refusal, and afterwards it works again.

## Demo Mode

While `admin.here` lies in the store's root, the engine checks neither the license nor the login with the password, and anyone who signs in becomes an administrator. The server recognizes this itself, and the answer of `session_connect` requires the agent to warn the user first thing. How to leave the demo mode — "[Installation](../User/installation.md)".
