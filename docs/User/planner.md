# Scheduler

The Scheduler is a small CRM system that allows employees to assign tasks to one
another and track their completion. A task can also be assigned to yourself — as
a reminder.

## Window Layout

The window consists of three lists and an auxiliary panel:

* **Users** — store employees;
* **Tasks** — tasks of the selected user; the task's text is shown below the list;
* **Responses** — responses and comments on the selected task; below the list are
  the text of the selected response and the **"Comment"** button.

The texts of tasks and responses are shown with their formatting and images; the
response list itself shows only the text.

The **"Embedded Web Module"** panel displays additional information about the task
(see "[Basic Operating Principles](basics.md)").

## Viewing Tasks

When the Scheduler is opened, active tasks for all users are loaded. Selecting a
user on the left switches to their task list. The **"Tasks"** toggle determines
which tasks to display:

* **in progress** — tasks assigned to this user;
* **created** — tasks created by them for other employees.

The **"Start"** and **"Due"** columns show the task's dates; an empty cell means
there is no date. Clicking a column header sorts the list, by due date for
example.

The **"State"** and **"Type"** quick filters narrow the list (the "< Any >" value
clears the filter). Task list buttons:

* **"Refresh current task list"** — reload active tasks;
* **"Load completed tasks"** — retrieve completed tasks of the selected user from
  the archive (the archive retention period is set by the developer in the user
  settings);
* **"Table Designer"** — configure the displayed columns.

To learn about new tasks at once, set the "Event key" of the operations
"Business → Scheduler → Tasks → Add" and "Business → Scheduler → Comments → Add"
and tick these events in the Dispatcher (see "[Events](dispatcher.md#events)").
Such an event reaches everyone: it says that a task or a note has appeared in
the Scheduler, not that it is yours.

## Creating a Task

The **"Add Task"** button opens the task window with the following fields: **Name**,
**Creator**, **Assignee**, **Type**, **State**, **Start**, **Due**, and **Notes**.
The notes are written in the HTML editor: the two buttons at the end of the line
open it in visual mode and in code mode, and an image can be inserted right into
the text (see "[HTML Editor](html-editor.md)"). The **"Private"** checkbox makes
the task visible only to the creator and the assignee. A new task can only have
the state **"New"**. Clicking **"Done"** creates the task and adds it to the list;
its first condition is duplicated below the list for convenience.

The checkboxes next to the **"Start"** and **"Due"** labels turn these dates on:
a cleared checkbox means no date, a ticked one fills in the current date and
time, which can be corrected. A new task starts at the moment it is created and
has no due date. A task for the future gets a start ahead, a task with a deadline
gets a due date; both are visible in the list at once.

## Responses and State Changes

A response to a task changes its state, so only the **current assignee** can add
one — using the **"Add Response to Task"** button. If you only need to add a note
to someone else's task without changing its state, click **"Comment"**: the HTML
editor opens, and once saved, the comment is added to the task at once.

The task state and its assignee are governed by the following rules:

* the assignee can only be changed when the state is **"Reassigned"**, **"Requires
  clarification"**, or **"Completed"**;
* for the states **"Reassigned"** and **"Requires clarification"**, a new assignee
  is required;
* the state **"Completed"** returns the task to the creator — they either accept
  the work or reassign it;
* the state **"Closed"** (archiving) can only be set by the task creator.

Only the task creator changes the start and the due date: for everyone else these
fields are closed in the response window. If the assignee needs more time, they
respond with the state **"Requires clarification"** and pass the task to the
creator, who moves the due date and reassigns the task again.

## Configuring Types and States

The value lists for the task **Type** and **State** fields can be extended by the
developer in the "Development" → "Settings Registry" section, under components →
"Scheduler" (see "[Settings Registry](registry.md)").