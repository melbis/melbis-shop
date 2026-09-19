# tool_export

Collects the store's AI tools into a single archive on this computer: the whole registry and the modules behind it. The rights to the commands do not go into the archive — they are about the store's people.

## The Right

"Export → AI Tools" (`AGENT_TOOL_EXPORT`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| [`output`] | string | where to write the archive — an absolute path or a path from the store folder. By default — `melbis-tools-<date>.zip` in the store folder |

## The Answer

```json
{"file": "D:\\Melbis\\shop.example.com\\melbis-tools-2026-09-17.zip", "bytes": 133400,
 "index": {"melbis": "…", "tree": ["…"], "files": {"units/melbis_agent_task.php": "…"}}}
```

| Field | What it is |
|---|---|
| `file` | where the archive is written |
| `bytes` | its size |
| `index` | the archive's `index.json`: the tree of the tools and the sum of every file |

What lies in the archive and how to compare the tools against it — "[AI Tools](../Dev/agent_tool.md)".

## Refusals

Only the common ones — "[Answers and Refusals](answers.md)".
