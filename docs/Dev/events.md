# Event Tracking

An event is an entry in the store's log saying that something has happened:
prices were saved, an order came in, the morning of a working day began. Events
are written by the engine and by modules, and read by the application's
Dispatcher, by modules and by the [AI Dispatcher](../User/ai-dispatcher.md). How
events look to an employee is covered in "[Events](../User/dispatcher.md#events)"
in the user guide; this page covers how they are built and how to set up your own.

## The Log {#journal}

The log is kept by the store's server, one for all workstations. A log row:

| Field | What it holds |
|---|---|
| `id` | the event number, grows with every entry |
| `user_id` | the author — the user who wrote the event; empty — the store itself. For an operation event, the one who performed it |
| `princ_id` | the principal — the user with whose rights the AI Dispatcher performs the AI tool commands it has not been granted; empty — no principal |
| `key_name` | the key — the event code, for example `kNewOrder` |
| `state` | the state — a word the store agrees on; `ALONE` by default |
| `data` | the event data in JSON |
| `for_users`, `for_groups` | who it is addressed to: user and group numbers separated by commas; empty — everyone |
| `date_time` | the time of the entry by the store's clock |

A module reads the log through the doors of the "[Event Log](sys.md#events)",
an agent through a store AI tool built on the same doors.

The application's Dispatcher receives the number, the key and the time of an
event and the [agent launch parameters](#agent). The rest of the data is read by
whoever handles the event — by its number:

```php
$event = MELBIS()->SysUserEventLoad($event_id);
$order_id = $event['data']['order_id'] ?? 0;
```

## Operation Events {#operations}

Every operation in the "Operation Catalog" has an "Event key" and a "State"
(the "[Users and Access Rights](../User/users.md)" window). An empty key means
no event is written; an empty state means the event is written with `ALONE`.

An event is written once per employee action, after the operation has been
performed:

| Action | When the event is written |
|---|---|
| a command to the server | after it succeeds |
| saving a window with a lock | when the save has gone through to the end |
| saving a window without a lock, merging in "Descriptions", importing in "Price Lists" | once, after all the portions |
| opening a window | by the data-fetching operation, on the first portion |
| data the application receives in portions | on the first portion |
| an AI assistant tool | by the operation of its right — for example, "AI Assistant → Tools → Execute"; a refusal writes no event |

The event records who performed the operation as its author, in `user_id`, and
the data says what was performed:

```json
{"command": "UPDATE_PRICE_STORE"}
```

`command` is the operation's "Command for the server". Operation events are
addressed to everyone.

A key on the "Business → Dispatcher → Notification check" operation gives
nothing: it is waiting, not an employee action.

## Module Events {#modules}

A module writes an event through the `SysUserEventAdd` door. It is placed where
the fact itself happens — in the shared logic that every path goes through.

That is how `kNewOrder` of the demo store is built. It is written by
`LOGIC_ORDER_EDIT\Run` — the function that writes every version of an order:
the cart on the storefront, the application's "Orders" window and the AI tools
all pass through it. A new order differs from a new version of an old one in
that the order row does not exist yet:

```php
$order = MELBIS()->SqlSelectFlat(__LINE__, $command, $param);
$order_new = !isset($order['id']);
if ( $order_new )
{
    // The order is born here
}

// ...

// Tell the store about a new order, wherever it was placed
if ( $order_new )
{
    // The dispatcher handles a new order with this model
    $agent_event = [
        'model' => 'sonnet'
        ];
    $data_event = [
        'order_id'     => $order['id'],
        'MELBIS_AGENT' => $agent_event
        ];
    MELBIS()->SysUserEventAdd(null, null, 'kNewOrder', 'ALONE', $data_event);
}
```

The event is written after all the order's records, so whoever handles it will
find the order complete.

**The key** is named like the keys of the "Settings Registry": `k` and a word
with a capital letter — `kNewOrder`, `kPricesSaved`.

**The data** is numbers, not copies of rows: an order, a product, a customer.
The handler reads for itself what they are at the moment. The `MELBIS_AGENT`
key of the data is special — it holds the [agent launch parameters](#agent): the
demo store sets the model in it.

**The recipients** are lists of user and group numbers; empty — everyone.

**The author** is the number of the user the event comes from; `null` — the store
itself.

**The principal** is a user's number, if the event assigns the AI Dispatcher work
with that user's rights. Only an event addressed to the dispatcher by number
or by group passes rights on. The door signature and an example are in the
"[Event Log](sys.md#events)".

A scheduled task writes events too. A module that the "[Task
Scheduler](cron.md)" calls once a day marks the morning of a working day:

```php
namespace MELBIS_CRON_MORNING;

function Main($mVars)
{
    header('Content-Type: text/plain; charset=utf-8');
    MELBIS()->CronLocalOnly();

    // The morning of a working day, for whoever makes the summary
    $day = MELBIS()->DateTime('now', 'Y-m-d');
    $data_event = [
        'day' => $day
        ];
    $event_id = MELBIS()->SysUserEventAdd(null, null, 'kMorning', 'ALONE', $data_event);
    echo "Event $event_id\r\n";

    return '';
}
```

## Delivery to the Application {#delivery}

The Dispatcher of each application keeps one long request open to the server.
The server waits up to 25 seconds and answers as soon as the event log or the
chat has changed; in silence — when the time runs out, and the application asks
the next one right away. A module event wakes the request just as an operation
event does.

The Dispatcher receives events addressed to everyone, to this user or to
their group.

The application remembers the number of the last event it has seen and asks for
the events after it. Right after start-up it puts its mark on the last event of
the log and launches nothing for it, so events that happened while the
application was closed cause no notifications.

**Subscription.** For the Dispatcher to respond to an event, its key must be in
the "Settings Registry":

* the "Basic Settings" tab, the "Dispatcher" → "Event list" section;
* "Key" is the event key, "Value" is its name in the notification list.

Each workstation sets its own:

* the check mark in "Notification Activation";
* the sound;
* the command.

The command is launched minimized, in the store's folder, and receives the event
in environment variables:

| Variable | What it holds |
|---|---|
| `MELBIS_EVENT` | the event key |
| `MELBIS_ID` | the event number; `0` for the "Test" button |
| `MELBIS_TIME` | the event time |
| `MELBIS_AGENT_<KEY>` | the [agent launch parameters](#agent), one variable per key |

The ready agent commands — `claude.bat`, `codex.bat`, `opencode.bat` — take only
the model and the effort from the variables. The agent's MCP server receives the
event number from the program together with the login, and the agent sees the
event itself in the answer to signing in to the store. Number `0` is a run from
the "Test" button: there is no event, this is a test run.

## Agent Launch Parameters {#agent}

An event can say how to launch the agent: the module puts a `MELBIS_AGENT` object
into the data. The program hands each of its keys to the command as an
environment variable `MELBIS_AGENT_<KEY>`, the key in capital letters: `model`
comes as `MELBIS_AGENT_MODEL`.

```json
{"order_id": 25, "MELBIS_AGENT": {"model": "sonnet", "effort": "medium"}}
```

* **The key** — Latin letters, digits and `_`, up to 32 characters: it becomes
  the variable's name. **The value** — a string or a number, it goes as is; a
  nested object is not passed on.
* **The ready commands** take two keys:

  | Key | `claude.bat` | `codex.bat` | `opencode.bat` |
  |---|---|---|---|
  | `model` | the model, without the key — `sonnet` | the model, without the key — the one set in Codex itself | the model, without the key — `deepseek/deepseek-v4-flash` |
  | `effort` | the effort, without the key — `high` | the effort, without the key — `high` | — |

  The other keys are for your own commands: such a command reads the variables
  itself.
* **Each application has its own model names**, so the module writes the name
  for the application the dispatcher runs on.
* **The variables of the previous run** the program deletes before each new one:
  an event without `MELBIS_AGENT` launches the agent with the default values.
* **Which values are allowed is decided by the owner** in the code of whoever
  writes the events — modules and AI tools. The program discards nothing, and
  the ready commands put the value in such a way that cmd does not run it as a
  command. A double quote in the value will still break the argument of
  `codex.bat` and `opencode.bat`.

## Handling by a Module {#handling}

Events are handled without the Dispatcher as well — by a scheduled module that
looks for events of its key. So that two handlers do not take the same event, it
is taken by a state change conditioned on the previous state: of two
simultaneous attempts, only one gets `ACCEPT`. Once the work is done, the taken
event is marked as done. To start reading from a certain time, use
`SysUserEventLast` with `$mBeforeTime`: the number of the last event before that
time is the cursor for `SysUserEventList`.

The log grows with every event, and it is cleaned up by whoever does the
handling — for example, a scheduled task deletes done events older than a week
once a day. The cleanup does not disturb the Dispatcher's mark.

The door signatures, the answer words (`ACCEPT`, `ABSENT`, `TAKEN`), the
handling loop and the cleanup with examples are in the "[Event Log](sys.md#events)".

## Event Cascade {#cascade}

Not every event is worth launching the AI Dispatcher: a conversation with the
agent costs money and takes tens of seconds, and most events do fine without it.
So a filter is put between the event and the agent — a scheduled module. It
handles the frequent event by rules, cheaply and quickly, and writes its own,
special one only when the agent cannot be done without. The AI Dispatcher is
subscribed to the special event, not to the frequent one.

For example, of all the new orders the agent needs only the large ones:

```
kNewOrder ── module once a minute ──> kOrderAttention ──> AI Dispatcher
             sum below the threshold: marked and forgotten
```

The module takes the `kNewOrder` events nobody has handled yet, checks the sum
and for a large order writes `kOrderAttention` with the order number and the
reason:

```php
namespace MELBIS_CRON_ORDER_WATCH;

// The sum from which an order wants the AI dispatcher
const SUM_ATTENTION = 1000;

function Main($mVars)
{
    header('Content-Type: text/plain; charset=utf-8');
    MELBIS()->CronLocalOnly();

    // New orders nobody has looked at yet
    $event_set = MELBIS()->SysUserEventList(0, 'kNewOrder', 'ALONE');
    foreach ( $event_set as $event )
    {
        // Two runs never take the same event
        $take = MELBIS()->SysUserEventState($event['id'], 'LAUNCH', 'ALONE');
        if ( $take != 'ACCEPT' ) continue;

        // Only an order that needs a look goes further
        $order_id = $event['data']['order_id'] ?? 0;
        $order_sum = OrderSum($order_id);
        if ( $order_sum >= SUM_ATTENTION )
        {
            // A big order is worth the strongest model
            $agent_event = [
                'model' => 'opus'
                ];
            $data_event = [
                'order_id'     => $order_id,
                'reason'       => 'sum',
                'MELBIS_AGENT' => $agent_event
                ];
            MELBIS()->SysUserEventAdd(null, null, 'kOrderAttention', 'ALONE', $data_event);
        }

        MELBIS()->SysUserEventState($event['id'], 'DONE');
        echo "Order $order_id: $order_sum\r\n";
    }

    return '';
}

/**
 * OrderSum
 **/
function OrderSum($mOrderId)
{
    $command = "SELECT order_version.total_sum
                  FROM {DBNICK}_orders orders
                  JOIN {DBNICK}_orders_version order_version
                    ON order_version.id = orders.version_id
                 WHERE orders.id = :ORDER_ID
               ";
    $param_order = [
        'order_id' => $mOrderId
        ];
    $order = MELBIS()->SqlSelectFlat(__LINE__, $command, $param_order);

    return (float) ( $order['total_sum'] ?? 0 );
}
```

The task is declared in `cron.php` once a minute:

```php
MELBIS()->CronAdd('* * * * *', 'http://localhost/?mod=melbis_cron_order_watch', 60, 'order_watch');
```

The special event carries the reason in its data, and one agent note on
`kOrderAttention` says what to do for each reason. A new rule is a new reason in
the module and a line in the note, not a new subscription.

What the cascade gives:

* **The agent is launched only when it is needed.** The rule "sum above the
  threshold" is thousands of times cheaper than a conversation with the model.
* **The rule can be checked.** The threshold and the conditions are visible in
  the code, and they can be debugged in the browser like any task module.
* **The model fits the task.** The special event names the model itself in the
  [launch parameters](#agent): a large order is handled by a strong model, the
  routine by a simpler one.
* **The frequent event does not go anywhere.** The state does not hide it from
  the Dispatchers: employees subscribed to "New order" get a notification.

The cascade's delay is the schedule step: when run once a minute, the event
reaches the agent within a minute. An event has a single state, so it is handled
by states by one module; the others read this event without changing its state.

## How to Set Up Your Own Event {#recipes}

**On an application operation.**

1. Set the "Event key" on the operation in the "Operation Catalog".
2. Add the key to the "Event list" of the "Settings Registry".
3. Check the event in the Dispatcher of the workstations that should respond.

**In a module.**

1. Put `SysUserEventAdd` where the fact happens — in the shared logic, not in
   one of its doors.
2. Add the key to the "Event list".

**On a schedule.**

1. The task module writes the event.
2. A line in `cron.php` sets when to call it.

**For the AI Dispatcher.**

1. Set up the event in one of the ways above.
2. Give the agent a note on this key and subscribe its Dispatcher — see
   "[AI Dispatcher](../User/ai-dispatcher.md)".
3. If the event is frequent, put a [cascade](#cascade) between it and the agent.
4. If the work must go with a user's rights, the module writes their number
   into the event as the principal — "[Assignments with a User's
   Rights](../User/ai-dispatcher.md#princ)".

## What to Watch Out For {#pitfalls}

* **An operation event comes to everyone.** It says that the operation has been
  performed, not that it concerns you. An event for one user or group is
  written by a module that names the recipients.
* **An operation event does not say what has changed** — only who performed
  which operation. What exactly, the handler finds out for itself, for example
  by the modification time of the rows.
* **The AI Dispatcher is not subscribed to its own operations.** If an operation
  it performs writes an event it is subscribed to, it wakes itself up endlessly.
* **Each event is a separate command launch.** Several events in a row launch
  several commands, and they run at the same time.
* **The principal is passed only by an addressed event.** For an event for everyone
  the principal does not work, even if `princ_id` is written: otherwise its rights
  would go to everyone whose Dispatcher has a command.
