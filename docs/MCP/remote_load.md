# remote_load

Downloads a file from the store's server onto this computer byte for byte, of any size and kind: a log, a settings file, a dump, a picture.

## The Right

Requires no store rights: access is given by the program's "Server" window — "[The Server](remote.md)".

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | the file on the server, an absolute path from `/` |
| [`output`] | string | where to put the file — an absolute path or a path from the store folder. `remote\<file name>` in the conversation folder by default |

## The Answer

```json
{"path": "/var/melbis/nginx/default.conf", "file": "D:\\Melbis\\shop.example.com\\mcp\\claude-code\\nginx-check\\remote\\default.conf", "bytes": 1840}
```

| Field | What it is |
|---|---|
| `path` | the file on the server |
| `file` | where it is downloaded to |
| `bytes` | the size |

## Refusals

| Answer | When |
|---|---|
| `path is the absolute path of the file on the server, from /.` | the path does not start with `/` |
| `path names a folder; remote_load brings one file.` | the path ends with `/` |
| `Cannot bring <path>: <reason>` | there is no such file or it cannot be read; `Failure` — the path leads to a folder |
| `No file channel on <address>: <reason>` | the server did not open the file transfer channel (SFTP) |

The refusals of access and connection — on "[The Server](remote.md)" page.
