# Users and Access Rights

The "Users" section (the "System" tab) — store employees, their groups, and access rights.

## Groups and Users

The **"Groups and Users"** tab: on the left — **"User Groups"**, on the right —
**"Users"** of the selected group. Adding, editing, and deleting groups and users is
available. The administrator group and administrator user cannot be deleted.

## Operations and Access Rights

The **"Operations and Access Rights"** tab contains the **"Operations Catalog"** — a tree
of store operations (commands to the server). For each operation, the following are
defined: **"Command for Server"**, the **"Allowed Time"** of its execution (from… to…),
**"Server Load Limit"** (by average load over 1 / 5 / 15 minutes; 0 — no restrictions),
**"Event After the Operation"** and **"Tables for Exchange with Server"**. Below are
**"Permissions for User Groups"** and **"Permissions for Users"**: an "Access" checkbox
at the intersection of an operation and an employee.

**Event After the Operation.** If an operation has an **"Event key"**, the store records
an event after each of its runs: who ran which operation. **"State"** is the word the
event goes into the log with; an empty field means `ALONE`. An empty key — no event is
written. A window's save operation writes the event on saving, a data-fetching operation —
on opening the window. For the "[Dispatcher](dispatcher.md)" to learn about the event, the
same key must stand in the "Settings Registry", the "Dispatcher" → "Event list" branch.

These are operational rights. Access to catalog sections — "Prices", "Descriptions",
"Placement", "Browser" — is configured separately, in "[Catalog](catalog.md)" →
"Access Rights".