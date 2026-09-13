# Dispatcher and Locks

## Dispatcher

The **"Dispatcher"** window opens automatically when the program starts. The
**"Users"** tab displays **"Groups and Users"** (who is currently in the store),
**"User Locks"** (their current locks), and the **"Send Message"** chat.

The **"Settings, Notifications"** tab contains: status, lock, message, and notification
update intervals; **"Available Notifications"** (for example, "You have a new task"
and "Your task has been completed"); **"Sound Alerts"**; **license** information and
program updates. The Dispatcher also reminds you of the need to
"[Reload](basics.md#recreate)" the local database when it becomes outdated.

## Locks {#locks}

To maintain data integrity, some tables are temporarily locked during editing.
The **"Locks"** window displays the **"Lock List"**: start time, user, lock type, and
locked tables. Features: **"Refresh Lock List"**, **"Release My Locks"**, and
**"Release My and Others' Locks"** — for emergency situations (unsaved data will be
lost when a lock is released).