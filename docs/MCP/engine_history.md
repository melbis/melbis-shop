# Versions

The `engine_history_*` section reads the history of a file's saved versions: the list of the versions and the body of any of them.

## What a Version Is

Every save of a file through `engine_php_save`, `engine_html_save` or `engine_static_save` — and the same save in the program's Workbench — writes a version: the whole body of the file, the time and the user who saved it. The versions lie in the store's database.

Modules and root scripts, templates and statics have versions. Images, the server's journals and the element files have none: they are not saved but uploaded or written in another way.

## Switching On and the Limit

Whether a version is written and how many of them to keep is decided by the settings of the program's Workbench on the computer the save is made from: "Editor Settings → Optimization → File Version Management (Change History)".

| Setting | What it does |
|---|---|
| "Save versions of modified files" | without it no version is written |
| "Maximum number of versions per file" | how many versions to keep; on a save the oldest ones beyond the limit are deleted. 100 by default |

In the language your program runs in the captions may differ.

The MCP server takes these settings from the program on its own computer: for its saves they work the same way as for the saves from the program.

## The History Is Tied to the Path

The versions are kept by the path of the file. A rename and a move do not carry them over: the former versions are read by the old path, and at the new path the history starts anew. Deleting the file does not touch the history — the list of its versions is still read, and the body can be restored from it.

The time of a version is kept to the second. The versions saved within one second go in the order they were saved: the newer ones first.

## In the Program

The same history is visible in the editor of the Workbench: "Show/Hide File Versions", and the version chosen — "Move Version to Editor".
