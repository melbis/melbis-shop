# session_init

Shows where one can sign in, before signing in. It changes nothing and does not go to the store, so it may be called as often as you like — after the user has switched the store in the program, for example.

## The Right

Not required.

## Parameters

None.

## The Answer

The data in JSON and, if the case calls for it, a line of text.

### The Data

```json
{
  "stores":  [{"name": "shop.example.com", "folder": "D:\\Melbis\\shop.example.com"}],
  "store":   "D:\\Melbis\\shop.example.com",
  "pinned":  false,
  "program": {"state": "running", "store": ""},
  "connect": true
}
```

| Field | What it is |
|---|---|
| `stores` | the stores of this installation of the program: the name and the folder |
| `store` | the folder of the store `session_connect` signs in to: the one last opened in the program, or the one the server is pinned to |
| `pinned` | the server is pinned to the store (see "[Sign-in, Rights, License](access.md)") |
| `program.state` | where the program stands: `running` — it is running on this store, `other` — on another one, `silent` — it holds this store but did not answer, `closed` — it is not running |
| `program.store` | the folder the program holds, with `other`; empty if an old build of the program did not report it |
| `connect` | whether there is a store `session_connect` will sign in to: the pinned folder in the program's list, or the store opened last. Whether the password will be enough this field does not know |

### The Text

The program matters because it is the one that gives out the login and the password, and it holds one store at a time. The text says what follows from that — and only when something other than the usual sign-in follows:

| The case | The text |
|---|---|
| the program is on another store | the password of the store to sign in to is taken from the saved settings; if it has not been saved, the owner opens this store in the program |
| the program holds the store but did not answer | the password will be taken from the saved settings |
| the program is not running | the same: the password of the store to sign in to comes from the saved settings, otherwise the owner opens this store in the program |
| no store has ever been opened | the owner opens the store in the program |
| pinning with no folder, or to a folder that is not in the list of stores | the session will not open, the owner fixes the server's configuration |

When the program is running on the store that is wanted, there is no text: the sign-in will open under the person sitting at the program.

## Refusals

None: the tool always answers. If there is nowhere to sign in to, `connect` is `false`, and the text says why.
