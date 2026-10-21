# remote_save

Writes a file onto the store's server: creates it, or replaces it whole if it already exists. A replaced file keeps its owner and permissions.

## The Right

Requires no store rights: access is given by the program's "Server" window — "[The Server](remote.md)".

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | the file on the server, an absolute path from `/` |
| [`content`] | string | the text of the file |
| [`content_source`] | string | a file on this computer — an absolute path or a path from the store folder; it goes as it is and may be binary |

Exactly one of `content` and `content_source` is needed. The folder on the server must exist: a new one is created by [`remote_exec`](remote_exec.md).

## The Answer

```json
{"path": "/var/melbis/nginx/default.conf", "bytes": 1872}
```

| Field | What it is |
|---|---|
| `path` | the file on the server |
| `bytes` | how much is written |

## Refusals

| Answer | When |
|---|---|
| `path is the absolute path of the file on the server, from /.` | the path does not start with `/` |
| `content or content_source - one of them…` | neither of them is given, or both are |
| `Cannot put <path>: <reason>` | there is no folder, no write permission, or the file on this computer could not be read. In `/tmp` and other shared folders even root cannot replace someone else's file: that is how Linux protects it |
| `No file channel on <address>: <reason>` | the server did not open the file transfer channel (SFTP) |

The refusals of access and connection — on "[The Server](remote.md)" page.
