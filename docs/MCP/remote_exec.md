# remote_exec

Runs a bash command on the store's server as root and gives back the output and the exit code.

## The Right

Requires no store rights: access is given by the program's "Server" window — "[The Server](remote.md)".

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `command` | string | what to run: one command or a whole script, possibly in several lines |
| [`timeout`] | number | after how many seconds to stop the command, from 1 to 600. 60 by default |

The command runs in a separate bash launch: the variables and the current folder of one call do not carry over to the next. The error stream comes mixed with the output, and nothing comes to the input, so a command that waits for input gets its end at once. A command stopped by the timeout ends with code 124.

The command's size is not limited: a long script goes to the server as a file in the home folder and is deleted after the launch. The output comes byte for byte, the way the server printed it.

## The Answer

The first block is JSON, the second is the command's output, cut to 8000 bytes.

```json
{"exit": 0, "bytes": 212, "time": 640, "file": "D:\\Melbis\\shop.example.com\\mcp\\claude-code\\nginx-check\\remote\\exec-20261021-114502-118.txt", "cut": false}
```

| Field | What it is |
|---|---|
| `exit` | the command's exit code; `null` if the end of the output did not come back |
| `bytes` | the size of the whole output |
| `time` | how long the call took, in milliseconds |
| `file` | the file where the whole output lies — in `remote\` of the conversation folder |
| `cut` | whether the output in the second block is cut |

Output that is not UTF-8 text does not get into the second block and lies only in the file.

## Refusals

| Answer | When |
|---|---|
| `command is required: what bash runs on the server.` | the command is not given |
| `timeout is a whole number of seconds, 1 to 600.` | `timeout` is not a whole number or is out of range |
| `The command did not run: <reason>` | the connection broke before the answer |

The refusals of access and connection — on "[The Server](remote.md)" page.
