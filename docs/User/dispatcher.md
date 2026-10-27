# Dispatcher and Locks

## Dispatcher

The **"Dispatcher"** window opens automatically when the program starts. The
**"Users"** tab displays **"Groups and Users"** (who is currently in the store),
**"User Locks"** (their current locks), the **"Send Message"** chat, and **license**
information and program updates. Here, too, the Dispatcher reminds you of the need
to "[Reload](basics.md#recreate)" the local database when it becomes outdated. An
employee is online if their program has contacted the server within the last 2
minutes, by the server's clock.

The **"Notifications"** tab sets which store [events](#events) the Dispatcher
responds to and how: **"Notification Activation"**, **"Sound Notification"**, and
**"Command"**.

The Dispatcher keeps one request open to the server: the server answers as soon as
a message or an event arrives, and in silence — after 25 seconds, refreshing the
user and lock lists along the way. The program shows its own saves in the lock list
right away. The request keeps running while the Dispatcher window is closed.

The **license** for the working day renews itself once a day, by Greenwich time,
even while the Dispatcher window is closed. If the license server did not answer,
the program retries every minute; if it answered but there is no license for
today — after an hour.

## Events {#events}

An event is a record in the store's event log saying that something has happened:
an order has arrived, prices have been saved, a price list has been loaded. The log
is kept by the store's server, so every workplace sees an event at once. An event
has a key — a short code such as `kNewOrder` by which it is recognized — a number,
and a time.

**Where events come from.**

* **Program operations.** Any operation in the "Operation Catalog" can be given an
  "Event key" (the "[Users and Access Rights](users.md)" window, the "Operations
  and Access Rights" tab). After every execution of such an operation the store
  writes an event: who performed which operation. A window's save operation writes
  it on saving, a data-fetching operation — on opening the window. For example, a
  key on the "Products → Prices → Save" operation will mark every price save.
* **Store modules.** A module writes an event itself when something of its own
  happens. That is how the demo store's "New order" (`kNewOrder`) works: the order
  module writes it for every new order — placed on the storefront or in the
  "[Orders](orders.md)" window. Module events of their own are set up by a
  developer — see "[Event Tracking](../Dev/events.md#modules)".

An event can be addressed to everyone, to individual employees, or to groups.
Operation events are for everyone.

**How an event reaches the program.** It is brought by the same Dispatcher request
as chat messages: the program learns of the event within a second or two, even
while the Dispatcher window is closed. The Dispatcher receives events addressed to
everyone, to you, or to your group. It responds to events from the moment the
program starts: what happened while the program was closed triggers no
notifications.

**Notifications.** "Notification Activation" on the "Notifications" tab lists the
events the Dispatcher can respond to. The list is set in the "[Settings
Registry](registry.md)": the "Basic Settings" tab, the "Dispatcher" → "Event list"
section; "Key" is the event key, "Value" is its name in the list. "New order" is
there from the very start. A new event appears in the list when the Dispatcher
window is opened again.

On a ticked event the Dispatcher responds like this: it shows a pop-up message with
the event's name, plays the **"Sound Notification"** if a sound is chosen, and runs
the **"Command"** if one is set. The command starts minimized, in the store folder;
it receives the event's key, number, and time in the environment variables
`MELBIS_EVENT`, `MELBIS_ID`, and `MELBIS_TIME`. The **"Test"** button saves the
command and runs it at once, with the number `0`. Every run of the command is
recorded in the "[Activity Log](journal.md)".

Ticks, sound, and command are settings of this computer for this store: on another
workplace they are set again. Most often the command starts the
[AI dispatcher](ai-dispatcher.md) — an agent that handles the event by itself and
sets tasks for people.

**How to set up an event of your own** — using price saving as an example:

1. In the "Operation Catalog", enter an "Event key" for the "Products → Prices →
   Save" operation, for example `kPricesSaved`.
2. In the "Settings Registry", in the "Event list", add the key `kPricesSaved` with
   the value "Prices saved".
3. On the workplaces that should respond, open the Dispatcher again and tick
   "Prices saved" in "Notification Activation".

## Locks {#locks}

To maintain data integrity, some tables are temporarily locked during editing.
The **"Locks"** window displays the **"Lock List"**: start time, user, lock type, and
locked tables. Features: **"Refresh Lock List"**, **"Release My Locks"**, and
**"Release My and Others' Locks"** — for emergency situations (unsaved data will be
lost when a lock is released).