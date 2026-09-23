# session_connect

Opens the session: signs in to the store under the user's login and tells the agent everything it needs to know about the session in one answer. Which store and which login the server signs in under — "[Sign-in, Rights, License](access.md)".

## The Right

Not required: a login that has been granted nothing has to sign in too, to learn what it may do.

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `topic` | string | the topic of the conversation in one word of Latin letters, digits, `-` and `_`: `prices-sept`. Everything the agent does in the conversation and everything the tools write for it lies in the folder of this name, and after a break the same word brings the agent back to the folder and its data |

The store and the login are worked out without the agent.

## The Answer

Two blocks: first the session's data in JSON, then the text.

### The Data

```json
{
  "store":   {"name": "shop.example.com", "url": "https://shop.example.com",
              "folder": "D:\\Melbis\\shop.example.com", "pinned": false},
  "user":    {"id": 5, "login": "manager", "name": "Irina",
              "options": {"LANG": "ru"}, "from": "program"},
  "group":   {"id": 2, "skey": "MANAGER", "name": "Managers", "options": {}},
  "demo":    false,
  "folder":  "D:\\Melbis\\shop.example.com\\mcp\\claude-code\\prices-sept",
  "cache":   {"topics": 3, "size": 48213760},
  "build":   "6.5.1.552",
  "memory":  {"notes": 3, "critical": 1},
  "tools":   16,
  "tasks":   {"waiting": [{"state": "kNew", "name": "New", "count": 2}],
              "given":   [{"state": "kAccept", "name": "Accepted", "count": 1}]},
  "granted": ["session_init", "session_connect", "session_clear", "session_rules_accept", "shop_page", "shop_run", "engine_map_tree"]
}
```

| Field | What it is |
|---|---|
| `store` | the store: the name from the program's list of stores, the address, the folder on the computer; `pinned` — whether the server is pinned to it |
| `user` | the user: the number, the login, the name, the parameters; `from` — where the login came from: `program` — from the running program, `settings` — from the saved settings |
| `group` | the user's group: the number, `skey`, the name, the parameters; `null` if there is no group |
| `demo` | the store works in demo mode, without a license |
| `folder` | the folder of this conversation, `mcp\<application>\<topic>\` in the store folder: it holds both the agent's files and everything the tools write |
| `cache` | the folders of all the conversations of this application in the store: how many there are and how many bytes they take up |
| `build` | the build of the program the server is installed next to. If it changes in the middle of a conversation, the program has been updated under it, and the tool descriptions the host has already shown the agent are out of date: the new ones come with compacting the conversation or with a new conversation |
| `memory` | how many notes the agent can see and how many of them are critical; `null` — there is no right to read the memory |
| `tools` | how many AI tools the store has; `null` — there is no right to their list |
| `tasks` | the scheduler's open tasks by state, two lists: `waiting` — the move is the user's ("New", "Reassigned", "Requires clarification", "Completed" — handed in to them as the creator), `given` — the ones they have given to others, in all the open states; closed ones are not counted; `null` — the user has no right to the scheduler |
| `granted` | the MCP tools granted to this login |

**The parameters** are the options the owner sets in the "[Users and Access Rights](../User/users.md)" window, on the "User Options" and "Group Options" tabs (in the language your program runs in the captions may differ). The set takes in the parameters with a filled-in key (`skey`). A value from a list comes with its own key, and with its name when it has no key; free input comes as text. An empty set is `{}`.

### The Text

First — only what this very case calls for:

* a warning about demo mode;
* that the conversation folders have taken up more than a gigabyte, and the old ones
  are worth removing with [`session_clear`](session_clear.md);
* that the store's memory is closed by a right, and where that right is granted;
* that the store has no AI tools, or that their list is closed by a right, and
  where they are written or where that right is granted.

Then — the rules of the session: what the fields of the data mean, who the agent is in the store and whom it works with, the order of work, the sources, the store's memory, the server, the computer and the store's local folder.

The project map is not loaded at sign-in: each of its parts the agent requests with its own tool, when it is needed.

A repeated call opens the session anew: the list of rights, the accepted rules and the critical notes that have been read are reset. The conversation folder stays: with the same topic the agent returns to its data.

## Refusals

| Answer | When | What to do |
|---|---|---|
| `topic is required…`, `topic […] is not one word…` | there is no topic, or it holds something other than Latin letters, digits, `-` and `_`; the session already open stays as it was | name the conversation with one word |
| `NO_STORE:` | no store has ever been opened in the program on this computer | open a store in the program |
| `NO_SUCH_STORE:` | the server is pinned to a folder that is not in the program's list of stores, or no folder is given after `--store` | fix the server's configuration in the agent application, or open this store in the program once |
| `NO_SHOP_INI:` | `Shop.ini` cannot be read in the store folder | check the store folder |
| `NO_SETTINGS:` | there is nowhere for the password to come from: the program is not running on this store or did not answer, and the password is not saved. The text names what exactly the server saw. The second case is the program running on this store, but with its connection currently without a password | start the program on this store, open the store in it again, or turn on "store passwords" in the connection settings; in the second case — enter the password on the "Connection Parameters" tab of the "Connection" window and save |

Refusals of the license, the password, the address, the versions and the backup window come from the store and happen with any tool — "[Answers and Refusals](answers.md)".
