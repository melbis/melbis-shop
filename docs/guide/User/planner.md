# Scheduler

The Scheduler is a small CRM system that allows employees to assign tasks to one
another and track their completion. A task can also be assigned to yourself — as
a reminder.

## Window Layout

The window consists of three lists and an auxiliary panel:

* **Users** — store employees;
* **Tasks** — tasks of the selected user;
* **Responses** — responses and comments on the selected task; below it is the
  **"Add Comment"** field.

The **"Embedded Web Module"** panel displays additional information about the task
(see "[Basic Operating Principles](basics.md)").

## Viewing Tasks

When the Scheduler is opened, active tasks for all users are loaded. Selecting a
user on the left switches to their task list. The **"Tasks"** toggle determines
which tasks to display:

* **in progress** — tasks assigned to this user;
* **created** — tasks created by them for other employees.

The **"Status"** and **"Type"** quick filters narrow the list (the "< Any >" value
clears the filter). Task list buttons:

* **"Refresh current task list"** — reload active tasks;
* **"Load completed tasks"** — retrieve completed tasks of the selected user from
  the archive (the archive retention period is set by the developer in the user
  settings);
* **"Table Designer"** — configure the displayed columns.

To avoid refreshing the list manually, enable notifications: "System" →
"Dispatcher" → "Settings" tab → "Notifications" → options "You have a new task"
and "Your task has been completed" (see "[Dispatcher and Locks](dispatcher.md)").

## Creating a Task

The **"Add Task"** button opens the task window with the following fields: **Name**,
**Creator**, **Assignee**, **Type**, **Status**, and **Notes**. The **"Private"**
checkbox makes the task visible only to the creator and the assignee. A new task
can only have the status **"New"**. Clicking **"Done"** creates the task and adds
it to the list; its first condition is duplicated below the list for convenience.

## Responses and Status Changes

A response to a task changes its status, so only the **current assignee** can add
one — using the **"Add Response to Task"** button. If you only need to add a note
to someone else's task without changing its status, use the quick **"Comment"**
option.

The task status and its assignee are governed by the following rules:

* the assignee can only be changed when the status is **"Reassigned"**, **"Requires
  Clarification"**, or **"Completed"**;
* for the statuses **"Reassigned"** and **"Requires Clarification"**, a new assignee
  is required;
* the status **"Completed"** returns the task to the creator — they either accept
  the work or reassign it;
* the status **"Closed"** (archiving) can only be set by the task creator.

## Configuring Types and Statuses

The value lists for the task **Type** and **Status** fields can be extended by the
developer in the "Development" → "Settings Registry" section, under components →
"Scheduler" (see "[Settings Registry](registry.md)").