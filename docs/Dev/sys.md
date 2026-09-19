# System Doors and Utilities

A store keeps dozens of reference directories in its database: the settings registry, product and order options, customer fields, currencies, suppliers, languages, the application's operations, web modules, employees and the rights granted to them. A module reads all of this constantly and almost never changes it.

The `Sys*` methods come in two kinds. **Doors** read these directories, each one its own source with **one and the same query** — sections 3 to 8. **Utilities** do the system work every module would otherwise write for itself: they walk down a door's answer, look at a table's schema, rebuild a tree, count and sweep dependencies — sections 9 to 13. Two utilities stand among the doors of their own sections: writing a setting and checking a sign-in.

**Why a door, when the query fits in three lines.** Currencies really can be fetched with an ordinary `SELECT` — but writing it yourself means starting *a query text of your own*. The static cache is addressed by text: a different text is a second entry, its own copy of the same data and its own trip to the database on a miss. And it is enough to move a space or a line break for the text to become different — and then there are three entries.

The door gives what a query cannot: **every module of the store lands in one and the same cache entry**. Whoever asked for the currencies first paid for the query on behalf of all the others — for the module next door, for the next page, for the whole lifetime of the entry. That is why a system table is read only through its door, and if there is no door, it is worth adding one rather than going around it.

```php
$param_set = MELBIS()->SysParamValues();
$param = array_column($param_set, null, 'skey');

$ban_id = $param['BAN']['id'];
```

## 1. How the Doors Are Built

**A door returns a list of complete rows.** The answer has no key, and there is no selector argument either: the key is chosen by whoever needs it, in a single line.

```php
$lang_set = MELBIS()->SysLanguages();
$lang = array_column($lang_set, null, 'skey');
$ua = $lang['UA'] ?? [];
```

**A row is self-sufficient.** It holds every column of the table, `id`, `skey` and `pos` included. That is why the answer is a list and not a map: a map's key is a column taken out of the row, and the moment the row is fetched by iteration or by `array_column`, the name is lost and there is nowhere to get it from.

**The order of the rows is a contract.** The list arrives in the application's order: `pos` for flat directories, `absindex` for trees, and `id` as the second key — so that equal `pos` values do not give a different answer from request to request. Employees have no order column of their own at all; there the order is the order of appearance. Nested lists are ordered by the same rule, each by its own column.

**A door returns the family whole.** Where a directory has subordinate tables, they arrive as a nested list under a key of their own, and so on all the way down: a supplier group has `provider`, a supplier has `stock`; an option has `value`; a tax area has `rule`, and a rule has `rate`. The name of the key is the name of what lies in it.

**There is no flat door per level, and there will not be one.** A directory is read by **one cacheable query**; a second one, picking from the same tables in another way, is a second cache entry holding the same data, and the two will start to diverge the moment the table changes. A query of one's own is justified only where iterating in PHP costs more than a trip to the database, and the directories are far too small for that.

The level you need is fetched by `SysPickBranch` — in a single line, to any depth:

```php
$group_set = MELBIS()->SysProviderGroups();

$provider = MELBIS()->SysPickBranch($group_set, 'provider', 'id');
$mine = $provider[$provider_id] ?? [];
```

The row loses nothing in the process: a supplier holds `group_id` inside it, a warehouse holds `provider_id` — the way up is known from the row itself. In detail — section 9.

**A derived field comes on top of the columns, not instead of them.** A door may add to a row something the table does not hold, if that spares every store one and the same calculation: this is how a currency got its `rate` and an employee their `group`. The real columns stay where they were.

**A row without a symbolic name is in the list as well.** The door does not pick out "only those with a `skey`": the set is maintained by the store owner, an unnamed row there is an ordinary thing, and the data refers to it by `id`. A currency without a `skey` stands in a product's `price_curr_id`, and without it in the list the price cannot be worked out. Whoever needs only the named ones filters them out themselves.

**Three doors answer with a map, and that is a decision rather than a lag.** `SysKeyValues` returns "key → value" pairs, where the row is degenerate into the pair itself; `SysEntityValues` — the projection "what is set on the entity", not the rows of a table; `SysTableSkey` — addressing by name, the very thing it exists for. These three have a slice argument.

**The utilities each answer in their own way.** A family level, a schema, the number of a new node, a count of dependencies, a report on a sweep — these are answers of another kind, and every utility states its own shape on the spot: sections 9 through 13.

**The fields are named after the table's columns.** What is written in `install.sql` and in the table description is what is in the row: `kind_key`, `tindex`, `is_blocked`, `modify_sum`. There is no second vocabulary to learn, and a `grep` by the column name finds both the queries and the modules.

**Addressing is twofold, and the boundary follows the meaning.** A symbolic name addresses what the code refers to: an operation's command, a module's `skey`, a setting's key. An identifier addresses what a person created in the store: an employee, a brand, a section, an option value. Such rows may have no symbolic name at all, and that is normal.

**Neither the doors nor the utilities take `$mLine`.** Their query is their own, and the module's line has nothing to do with it. In refusals and in the log, the method's name stands in place of the line number.

## 2. What Is Cached and What Is Read Live

There is one rule: **a door reads through the static cache, a utility caches nothing.** Directories, employees, rights — all of these are doors, and all of them go through the cache.

Freshness does not suffer from it. A static entry's address is assembled from the table's change mark, and that mark is moved by every write that goes through the engine — both a PHP module and the native client, which writes through the same exchange scripts. Once the table has changed, the entry has a different address, and the very next request assembles it anew. A day in an entry's lifetime is not a delay before a change shows up; it is the term for an entry nobody has cancelled.

The mark stays put only for a write that goes past the engine: a raw query from an SQL tool, an external script, an edit made by hand in the database. But such an edit shows up neither in the rights, nor in the prices, nor in the products — that is a property of going around, not of particular tables.

For the most part the utilities do not go to the database at all: both `SysPick*` walk the ready array a door has already brought, while the tree doors and `SysSelfKeyModify` write. But three of them read, and each without the static cache — for a reason of its own.

**`SysUserLoginCheck`** — the verb in the name says this is a check and not a list. The password is not put into shared memory: the static cache keeps a query's parameters inside the entry itself, and the password would lie there in the open. A sign-in always goes to the database.

**`SysTableColumns`** — its query text holds not a single table of the store, which means there is no mark by which the entry could be cancelled. The schema is remembered for the duration of the request: within one request the columns do not change.

**`SysDependCount`** counts against the live database: the query is assembled for the call, and the answer has to be exact as of now — it is what the decision to delete rests on.

A door keeps nothing in the request's memory: every call goes to the static cache anew, and any write moves the address there. So a change made earlier on the same page is visible to everyone who asks after it.

### 2.1. The Door and the Module Cache

The engine finds a module's dependency on a table itself — from the query the door executed. There is no need to mark it in the Workbench, and nowhere to do so: from the accounting point of view, a door's query is no different from a query written by hand in the module. How the list is assembled and when it grows is covered in the "Caching" section.

**A page assembled according to rights, though, is no fit for the module cache.** The door will answer with fresh data, but a page that got into the cache will go to someone who does not hold those rights. Modules that ask about rights work under a particular employee, and there is no point caching them.

## 3. The Settings Registry

**`SysSelfKeyValues()`** — the store's user settings, the very ones `DefineSelfConst` turns into constants. A list of rows: the code, the value, the prefix, the caption with its description and input mask, and `key_id` — the registry branch the right is granted on.

```php
$self_set = MELBIS()->SysSelfKeyValues();
$self = array_column($self_set, null, 'code');

$phone = $self['SHOP_PHONE']['value_txt'] ?? '';
```

The prefix lies in the setting itself: it groups settings for display, while they are addressed by code. `DefineSelfVars` can lay a whole group out into the global array — the case of the prefix makes no difference to it.

**`SysSelfKeyModify($mUserId, $mCode, $mValue, $mTries = false, $mPause = false)`** — the section's utility, the one that writes. It checks that the setting exists and that the person is allowed to edit it, takes the table under a lock, writes, and releases the lock. It sits out an occupied table the way `SqlTableLock` does: the attempts and the pause in milliseconds go through to it, and `BUSY` arrives only after them. It answers with a word:

| Answer | What happened |
|---|---|
| `ACCEPT` | written |
| `ABSENT` | there is no such code |
| `DENIED` | no right |
| `BUSY` | the application is holding the table, retry later |

A word rather than `true`/`false`, because "not allowed" and "busy right now" are different news: the first is final, the second asks you to try again.

> Constants already defined by `DefineSelfConst` in this request will keep their
> old value: PHP cannot redefine a constant. After writing, read through
> `SysSelfKeyValues`.

**`SysKeyValues($mKeyCode = '')`** — the registry of keys: sections such as `STORE_STATUS_KEY` and the "key → value" pairs inside them. This is what a product's status, a brand's type and a web option's kind are signed with.

```php
$status_set = MELBIS()->SysKeyValues('STORE_STATUS_KEY');
$name = $status_set[$store['status_key']] ?? '';
```

## 4. Options and Their Values

Six doors of one shape. Every entity has its own pair of tables — "option" and "option values" — and they all answer the same way: **a list of options, each with its own list of values** under the `value` key. There is no key on either level.

| Door | What it describes |
|---|---|
| `SysEntityKeyValues($mEntity)` | additional options of an entity: see the list of names below |
| `SysWebKeyValues()` | web options |
| `SysOrderOptionValues()` | order options: status, payment, delivery |
| `SysOrderStoreOptionValues()` | options of a product inside an order |
| `SysParamValues()` | product parameters |
| `SysFieldValues()` | customer registration fields |

**The name of an entity is the name of its tables.** `<entity>_key` holds the options, `<entity>_key_value` their values, `<entity>_key_set` — what has been set on whom. There is no separate door per entity: a triple of tables is created after this pattern, and both doors start to understand it.

`$mEntity` in the first one is one of twelve:

| | | | |
|---|---|---|---|
| `advert` | `brand` | `info` | `param` |
| `param_value` | `provider` | `provider_stock` | `tax_area` |
| `topic` | `topic_filter` | `user` | `user_group` |

An unknown name stops execution with the list of accepted ones.

The pairs `param` and `param_value` in this list are different: a product parameter has options of its own, and each of its **values** has its own. `provider` and `provider_stock`, `user` and `user_group` differ in the same way.

```php
$param_set = MELBIS()->SysParamValues();
$param = array_column($param_set, null, 'skey');

foreach ( $param['COLOR']['value'] as $value )
{
    echo $value['id'].' '.$value['name'];
}
```

**A value's row is complete.** It holds `id`, and `skey`, and `pos`, and a sum of its own — so a value taken out of the list remains itself. The key is put by whoever needs it, on any level:

```php
$value = array_column($param['COLOR']['value'], null, 'skey');
$red_id = $value['RED']['id'] ?? 0;
```

Keying the values by `skey` is possible only where you know your own data: the set is maintained by the store owner, a value without a symbolic name is an ordinary thing, and all the unnamed ones will collapse into a single cell. By `id` that never happens.

**The folders of an option tree are in the list too.** They have `folder` raised — that is what tells them apart from the options.

**Sums arrive raw.** The values of order options and of parameters have a sum and a currency of their own (`modify_sum` or `set_sum` plus `sum_curr_id`). The door does not convert to the storefront's currency — the rate is taken from `SysCurrencies`.

**`SysEntityValues($mEntity, $mId = 0)`** — what is actually set on an entity, rather than the list of what is allowed:

```php
$option = MELBIS()->SysEntityValues('provider', $provider_id);
$min_buy = $option['MIN_BUY'] ?? '';
```

The answer is the `skey` of the chosen value, or, if the value has no symbolic name, its title; for an option with free input — the text itself. If several rows are entered against one option, the one entered last answers.

**The key of the answer is the option's `skey`, so an option without one does not get into the answer at all.** This is the single place where a symbolic name is required: there is nothing here to address an option by `id` with, and merging all the unnamed ones into one cell would mean returning a random one in place of them all. If the door stays silent on an entity where values are entered, the first thing to look at is the `skey` of the options themselves in `SysEntityKeyValues`.

### 4.1. Order Blocks and Limits

The rules by which the owner restricts the choice of order options. Three tables, three doors, all of them ordered by `id` — the rules have no `pos` column.

| Door | What it returns |
|---|---|
| `SysOrderOptionBlocks()` | incompatible values of order options: `value1_id`, `value2_id`, `message` |
| `SysOrderStoreOptionBlocks()` | the same for the options of a product inside an order |
| `SysOrderOptionLimits()` | forbidden transitions of an option's value: `option_id`, `was_value_id`, `set_value_id`, `message`, and under the `right` key — who the rule concerns |

The check is an intersection of sets in PHP, not a condition in a query. Putting the chosen values into the query text would mean a cache entry for every combination a customer is able to pick; the door reads the whole table, and there is one entry:

```php
$chosen = array_flip($value_ids);

foreach ( MELBIS()->SysOrderOptionBlocks() as $block )
{
    if ( !isset($chosen[$block['value1_id']]) ) continue;
    if ( !isset($chosen[$block['value2_id']]) ) continue;

    // The first rule by id is the one shown, as the order of the list promises
    $message = $block['message'];
    break;
}
```

**A limit's `right` is not a right but an addressee.** The rows of `order_option_limit_right` say *who* the ban applies to: `user_id` — an employee, `group_id` — a group; an empty one arrives as zero. This is not the family of rights (section 8): there a door answers "is it allowed", while here it returns a rule with a text of its own, and the store's owner falls under it like everyone else if they have been named. Whether the rule concerns a person the module decides itself — by `group` from `SysUsers`:

```php
$user_set = MELBIS()->SysUsers();
$person = array_column($user_set, null, 'id');
$group = $person[$user_id]['group'] ?? [];

foreach ( MELBIS()->SysOrderOptionLimits() as $limit )
{
    if ( $limit['was_value_id'] !== $was_id ) continue;
    if ( $limit['set_value_id'] !== $set_id ) continue;

    foreach ( $limit['right'] as $right )
    {
        $mine = ( $right['user_id'] === $user_id || in_array($right['group_id'], $group, true) );
        if ( !$mine ) continue;

        $message = $limit['message'];
        break 2;
    }
}
```

A rule with no addressee is in the list — with an empty `right` — and concerns nobody.

## 5. The Store's Reference Directories

| Door | What it returns |
|---|---|
| `SysCurrencies()` | currencies with `multiplex`, `division` and the rate supplier, as a list |
| `SysClientGroups()` | customer groups with their price column and discount |
| `SysLanguages()` | the store's languages, `is_origin` marks the source one |
| `SysProviderGroups()` | supplier groups, each with `provider` inside, and `stock` inside a supplier |
| `SysDiscGroups()` | discount groups, each with `rate` — the rules of the discount |
| `SysTaxGroups()` | tax groups, each with `rate` — the rates |
| `SysTaxAreaRules()` | tax areas, each with `rule`, and `rate` inside a rule |
| `SysTableSkey($mTableName)` | a `skey → id` map for any reference table |

The last four doors return the family whole, down to the lowest level — why that is so, and how to take the level you need, is in section 1.

**A currency's `rate` is a derived column.** The database does not hold it: it is computed from `multiplex` and `division` by the rule every store would otherwise rewrite — together with the guard against a zero `multiplex`. A sum is multiplied by it and comes out in the base currency; there is no dividing anywhere, ever. The key for a currency is almost always `id`: that is what the data refers to it by (`price_curr_id`, `sum_curr_id`, `pprice_curr_id`).

```php
$currency_set = MELBIS()->SysCurrencies();
$currency = array_column($currency_set, null, 'id');

$rate = $currency[$store['price_curr_id']]['rate'] ?? 1;
$price = round($sum * $rate, 2);
```

The original `multiplex` and `division` stay in the row alongside. If a currency in the store has a zero rate entered, `rate` will come out zero as well — that is visible, and it is the store's data rather than the engine's guess.

**For tables with no door of their own** there is `SysTableSkey($mTableName)`. A condition in a query is written by identifier, while the developer knows the symbolic name: `id` differs in every store, `skey` is the same everywhere.

```php
$advert = MELBIS()->SysTableSkey('advert');

$param_ban = [
    'advert_id' => $advert['NEWYEAR']
    ];
$store = MELBIS()->SqlSelect(__LINE__, $command, $param_ban);
```

The table name is written **without the prefix**, as everything is in this section: the door substitutes the nickname itself. An unknown table stops execution. Rows with an empty `skey` do not get into the map — it is addressed by exactly that. The query is read through the static cache, so a repeat call never goes to the database.

The doors carry the `id` in every row, so for their tables this method is not needed — it is for the rare directories the doors have not reached.

## 6. The Application's Registries

**`SysOperations()`** — the application's operations, the ones rights are granted on. Besides the name and the command they carry a time window (`allow_from`, `allow_to`) and load thresholds. The list holds the tree's folders as well — they are marked with `folder` and have no command.

**`SysWebInKeys()`** and **`SysWebOutKeys()`** — embedded and external web modules: address, place, order and the `auth` flag. A zero `auth` means the module requires no authentication.

```php
$menu = MELBIS()->SysWebInKeys();

foreach ( $menu as $module )
{
    $skey = $module['skey'];
    if ( $skey === '' ) continue;
    if ( !MELBIS()->SysWebInRight($user_id, $skey) ) continue;

    echo '<a href="'.$module['url'].'">'.$module['name'].'</a>';
}
```

## 7. People

**`SysUsers()`** — the store's employees. Besides their own `group_id` and `add_group_id` columns each one carries a derived field `group` — both groups in one list, the empty one thrown out.

**`SysUserGroups()`** — the groups, and inside each one, under the `users` key, the **identifiers** of its people rather than their rows: people have a door of their own, and the group names them instead of copying them. It unfolds in two lines:

```php
$user_set = MELBIS()->SysUsers();
$user = array_column($user_set, null, 'id');

foreach ( $group['users'] as $user_id )
{
    $person = $user[$user_id];
}
```

A group nobody has been moved into yet is in the list — simply with an empty `users`.

**`SysUserLoginCheck($mLogin, $mPassCode)`** — signing in: it returns the employee's identifier or zero. Zero means all of it at once — no such login, the password did not match, the person is blocked — and that is right: from the outside these cases must not be told apart.

The verb in the name is not for decoration: this is a utility rather than a door — it reads without the cache, and it is the only one in the whole section that takes a secret. No door reads the password, it is not in the list of `SysUsers` columns, and the check itself never gets into shared memory.

## 8. Rights

Ten doors, one per family of rights. The first argument everywhere is the employee being asked about.

| Door | What is being asked |
|---|---|
| `SysOperRight($mUserId, $mCommand = '')` | an operation of the application |
| `SysWebInRight($mUserId, $mSkey = '')` | an embedded web module |
| `SysWebOutRight($mUserId, $mSkey = '')` | an external web module |
| `SysWebKeyRight($mUserId, $mSkey = '', $mFor = 'read')` | a web option: `read`, `write`, `remove` |
| `SysTopicRight($mUserId, $mTopicId = 0, $mFor = 'frame')` | a section: `frame`, `price`, `ctrl`, `browse` |
| `SysInfoRight($mUserId, $mInfoId = 0, $mFor = 'info')` | an info item: `info`, `value` |
| `SysOrderRight($mUserId, $mValueId = 0)` | an order option value: which orders are visible |
| `SysOrderOptionRight($mUserId, $mOptionId = 0)` | an order option: may it be edited in an order |
| `SysOrderStoreOptionRight($mUserId, $mOptionId = 0)` | an option of a product inside an order: the same |
| `SysSelfKeyRight($mUserId, $mCode = '')` | a store setting |

The three order doors are three different questions about the same options. `SysOrderRight` asks about a **value**: an order is visible to whoever has been granted the values of its options, the status among them. The other two ask about an **option**: is the employee entitled to set a value for it in an order themselves. Blocks and limits (section 4.1) are not rights but rules: they restrict the choice, not the access.

**The answer comes in three shapes**, and this is worth reading carefully:

```php
$allow = MELBIS()->SysWebKeyRight($user_id, 'FIN_ACCOUNT', 'read');   // true | false
$allow = MELBIS()->SysWebKeyRight($user_id, '', 'read');              // true | array
```

With a named object the door answers "yes" or "no". Without an object it returns the set of what is allowed: a map of `name => id`. And if the person is allowed **everything** — they are the owner or hold a super-right — `true` comes instead of the map.

Hence the rule: check for `=== true`, not for "truthiness". A non-empty array is truthy as well.

```php
$allow = MELBIS()->SysTopicRight($user_id, 0, 'price');

if ( $allow !== true )
{
    $id_line = implode(',', $allow);
    $command .= " AND t.id IN ( $id_line )";
}
```

**Why `true` and not the full list.** A door without an object exists not for display but for a filter — to narrow a selection down to what is allowed. And "everything is allowed" here is not a list every row happened to fall into, but a different fact: **there is nothing to narrow**. Were the door to assemble the full set for the owner, the query would carry `AND t.id IN ( every section )` — extra work, and on top of that a snapshot that will go stale within this very request.

So `true` carries more than a map, not less, and the `!== true` in the example above is not a guard against an inconvenient type but a fork by substance: to filter or not.

Hence also what is not worth doing: iterating over this answer as a ready-made list for display. For the owner there will be nothing to iterate. When a list is exactly what is needed — assemble it from the directory (`SysWebKeyValues`, `SysOperations` and the rest) and leave in it what is allowed.

**The doors observe three rules of the complex themselves:**

- **The store owner** (`id = 1`) gets "yes" to any question, without going to the database.
- **A super-right** outranks the granted ones: `PUT_OUTSIDE_RIGHT` opens all web
  modules and web options, `PUT_TOPIC_RIGHT` — all sections, `PUT_INFO_RIGHT` — all
  info items, `PUT_ORDER_OPTION` — all three order doors at once, `PUT_KEY_VALUE` —
  all settings.
- **A blocked employee holds nothing**, not even modules without authentication.
  A live session does not save them: the block takes effect from the next request.
  A deleted one all the more so.

The employee's identifier is required and must be a number greater than zero: a right is a system question, and it cannot be asked about nobody. Zero or a foreign string stops execution.

## 9. Taking a Door’s Answer Apart

Two `SysPick*` utilities work with what a door has already brought: one walks down the family, the other strips the nested part off it. Neither of them goes to the database.

**`SysPickBranch($mSet, $mPath, $mKey = '', $mDefault = [])`** — walk down the answer of a family door to the level you need and take it as a flat list or as a map.

```php
$group_set = MELBIS()->SysProviderGroups();

$provider = MELBIS()->SysPickBranch($group_set, 'provider', 'id');
$mine = $provider[$provider_id] ?? [];
```

The path is written through a slash — a step down by the key of nesting, to any depth:

```php
$stock = MELBIS()->SysPickBranch($group_set, 'provider/stock', 'id');
$rate = MELBIS()->SysPickBranch(MELBIS()->SysTaxAreaRules(), 'rule/rate', 'id');
```

A row met twice becomes one: an employee in two groups arrives once, because the map is keyed by `id`.

Without `$mKey` the answer is a list with no key. An unknown key in the path stops the walk with a list of what the row does hold, and returns `$mDefault` — an empty array by default, so `isset` by key works even after a miss.

**`SysPickFlat($mSet, $mKey = '', $mDefault = [])`** — the same rows, but without anything nested. The two utilities usually go in a pair: one takes the lower level, the other the upper one without it.

```php
$keys = MELBIS()->SysEntityKeyValues('provider');

$value_set = MELBIS()->SysPickBranch($keys, 'value');
$key_set = MELBIS()->SysPickFlat($keys);
```

`$mKey` works just as it does in its neighbour: empty — a list, given — a map.

```php
$key = MELBIS()->SysPickFlat($keys, 'skey');
$min = $key['MIN_BUY'] ?? [];
```

What is thrown out is **everything that is an array** — both a nested level and a derived field that is a list. An employee loses `group` along with `provider`, but `group_id` and `add_group_id` stay where they were, so no data is lost: only the convenient summary goes.

## 10. The Table Schema

**`SysTableColumns($mTableName)`** — a table's columns and their types, as a map of `column => type`. The name goes without the prefix, as everything in this section.

```php
$column = MELBIS()->SysTableColumns('topic');
$has_folder = isset($column['folder']);
```

The utility is needed where a column name arrives from the outside — from a store setting, from a module's parameters, from someone else's code. The schema is its own white list: what is not in it is not a column. Both of the following families stand on it — the tree and the dependencies check every name against it before taking it into a query.

## 11. The Tree

A tree in the database is three columns: `tindex` (the parent), `tlevel` (the level), `absindex` (the number in the traversal). That is how the catalog's sections are built, and info items, customer fields, the application's operations, web options and others. A table without these three columns is not a tree as far as the utilities are concerned, and they answer with a refusal.

One table may hold several trees — then they are told apart by a **scope**: a column and its value. That is how the alternative catalogs in `topic_alt` are built: all of them in one table, and which one a row belongs to is told by `kind_key` with a value from the `TOPIC_ALT_KIND_KEY` registry. The scope is checked against the schema and goes into the query as a parameter, not as text.

```php
$id = MELBIS()->SysTreeAdd('topic_alt', $parent_id, ['kind_key' => 'kTree']);
```

**The tree utilities only write.** There are no reading ones among them, and there is no point in adding any: a tree lies in an ordinary table, and an ordinary `SELECT` over it gives more — your columns, your filter, your order. A door would return someone else's set of fields, and for the rest you would have to go with a second query.

The order is set by the table itself: `absindex` is the number in the traversal from top to bottom, `tlevel` is the depth. `ORDER BY absindex` draws the tree as it is, `tlevel` gives the indent:

```php
$command = "SELECT id, name, tlevel, seo_psu
              FROM {DBNICK}_topic
             WHERE no_visible = 0
          ORDER BY absindex
           ";
$topic_set = MELBIS()->SqlSelectStatic(__LINE__, $command);
```

A branch comes from that same order: a node and everything below it lie by `absindex` in **one continuous stretch** — from the node's number up to the first following node whose `tlevel` is no deeper. So "a section with all its subsections" is selected by a condition in the query, without a traversal in PHP.

The utilities themselves change the structure — where a node stands, in what order and to whom it is visible:

| Utility | What it does | What it answers |
|---|---|---|
| `SysTreeAdd($mTable, $mParentId = 0, $mScope = [])` | creates a node under the parent | the `id` of the new node, or 0 |
| `SysTreeRightCopy($mTable, $mParentId, $mId)` | copies the parent's grants to the node | the number of rows copied |
| `SysTreeMove($mTable, $mId, $mParentId = 0, $mScope = [])` | moves a node together with its branch | `true` or `false` |
| `SysTreeShift($mTable, $mId, $mDown = false, $mScope = [])` | swaps a node with its neighbour | `true` or `false` |
| `SysTreeDelete($mTable, $mId, $mScope = [])` | removes a node together with its branch | the number of rows removed |

Three things about writing are worth knowing in advance.

**A row is born bare.** `SysTreeAdd` writes only the `id` and the scope's columns — the name, the type and everything else are filled in by the caller with their own `SqlUpdate`. The parent's rights the node does not inherit by itself either: until they have been copied, a new section or info item is seen by the owner alone. The second act is `SysTreeRightCopy`: it finds the table's twin `<table>_right` and writes the parent's grants over to the node. For a table without a twin there is nothing to copy, and the answer is zero — which is not an error.

**The lock is on the caller.** The utilities mark the table as changed themselves, so the cache of this same request will see the edit. But they do not take `SqlTableLock` — take it around your work as a whole, not around every call.

**A move into the node's own branch writes nothing** and answers `false`: a node cannot become a descendant of itself. Shifting a node that has no neighbour on that side answers the same way: there is nothing to swap with — not an error, but not a shift either.

## 12. The Order of a Flat List

Where there is no tree, the order is held by the `pos` column: reference directories, option values, rates, languages. That is what the doors sort by. A table with no `pos` these utilities do not count as a list at all, and they answer with a refusal.

One table may hold several lists — and then a **scope** tells them apart, the same one as in a tree: a column and its value. For tax rates that is `['group_id' => 3]`, for option values `['option_id' => 7]`.

| Utility | What it does | What it answers |
|---|---|---|
| `SysPosShift($mTable, $mId, $mDown = false, $mScope = [])` | swaps a row with its neighbour | `true` or `false` |
| `SysPosOrder($mTable, $mIdSet, $mScope = [])` | puts the named rows first, in the given order | the number of rows rewritten |

```php
$taken = MELBIS()->SqlTableLock(__LINE__, '{DBNICK}_tax_rate');
if ( $taken )
{
    $scope_rate = [
        'group_id' => $group_id
        ];
    MELBIS()->SysPosOrder('tax_rate', [$third_id, $first_id], $scope_rate);

    MELBIS()->SqlTableUnlock(__LINE__, '{DBNICK}_tax_rate');
}
```

Three things are worth knowing in advance — the same three as with a tree.

**`pos` runs consecutively, 1..N.** After any call no gaps are left in the numbering: the utility renumbers the whole list of the scope. Only the rows that **have moved** are written, the rest are not touched at all.

**The named ones go first, the rest keep their order behind them.** A full list and a partial one work the same way: name every row and you get exactly that order, name two and they stand first. A row that does not belong to this scope stops the walk, and it never comes to writing: half an order is worse than none.

**The lock is on the caller.** The utilities mark the table as changed themselves, but take `SqlTableLock` around your work as a whole.

Shifting a row that has no neighbour on that side answers `false` and writes nothing — as with a tree.

## 13. Dependencies

The database keeps no foreign keys, so what hangs on what the engine knows from a list written by hand. A module may extend that list for the duration of its request and ask who would be left hanging in the air if a row were removed.

**`SysDependAdd($mMain, $mTable, $mKey, $mKeyNullable = false)`** — to declare a relation: in table `$mTable` the column `$mKey` names a row of table `$mMain`. Both tables and the column are checked against the schema right away: a relation taken on trust would delete by a name that does not exist, and a sweep is not the place where that gets discovered.

`$mKeyNullable` says that the column **may stand empty**. Then an empty row is not an orphan: it hangs on nobody by its own will, and the sweep spares it. That is how grants are built — `topic_right.user_id` is empty when the right is granted to a group.

The relation lives until the end of the request. A relation the engine already holds — a built-in one or one declared earlier — is not added a second time: the answer is `false`, and the count does not change.

**`SysDepends($mTable)`** — what hangs on a table, as a list of rows `main`, `table`, `key`, `nullable`.

**`SysFileEntities()`** — the entities files are attached to: the tables that have a `files_<table>` link by `elem_id` in the map. The list is read from the same map, so a link the store declares through `SysDependAdd` gets into it by itself.

**`SysDependCount($mTable, $mIds)`** — how many rows would be left hanging if the named ones were removed. The answer is a map of `table => number`, only the non-zero ones. It must be asked **before** the deletion, while the rows are still in place:

```php
$count = MELBIS()->SysDependCount('topic', $topic_id);
if ( !empty($count) )
{
    // tell the person what exactly will go next
}
```

**`SysDependSweep($mTable, $mTries = false, $mPause = false)`** — to remove what is already hanging. It walks the whole chain: a dependent may have dependents of its own. It answers with a row **per relation**, not per table:

```php
[
    ['main' => 'user', 'table' => 'user_chat', 'key' => 'user_id',    'gone' => 12, 'busy' => false],
    ['main' => 'user', 'table' => 'user_chat', 'key' => 'to_user_id', 'gone' => 0,  'busy' => true],
    ]
```

One table is sometimes bound twice by different columns — like `user_chat`, where a person stands both as the sender and as the recipient. A row per relation was chosen for exactly that reason: otherwise one pass would wipe out the result of the other.

The latch is taken only where orphans have been found: the relation is first checked with a cheap count, and where there is nothing to sweep — which is the ordinary case — the row answers `gone 0` without a lock. The removal itself finds its orphans anew, already under the latch, so `gone` speaks of what actually went rather than of the count of the pre-check.

`busy` means that the table was held by somebody else and was skipped — having already sat out that somebody's work: the attempts and the pause in milliseconds go through to `SqlTableLock`, and with the defaults every occupied table of the chain is waited for up to five seconds. The sweep takes every table into work by itself, so call it **before** you take your own set into work, or after you release it.

### 13.1. Soft Relations

Not everything that refers depends: a product refers to a currency and to a brand, but must not die with them — the row stays, the reference is nulled. Such relations live by **a map of their own** (for a currency, the five price columns of a product; for a brand, `brand_id`), and the hard sweep does not see them at all. The doors are a mirror of the hard ones:

| Door | What it does |
|---|---|
| `SysRelates($mTable)` | the soft relations of a table, as a list: `main`, `table`, `key` |
| `SysRelateCount($mTable, $mIds)` | how many rows would be left without a reference if the named ones were removed — ask **before** the deletion |
| `SysRelateSweep($mTable, $mTries = false, $mPause = false)` | null the references to rows already deleted |

The pass is built like the hard one — a pre-check by count, a latch per table, the mark, `busy` after the sitting out — but with two differences in meaning. There is no chain: a nulled reference has orphaned nobody, and there is nowhere to go deeper. And there is no sparing variant: only non-empty references are nulled, an empty one is empty already. In the row of the report, `cleared` stands in place of `gone`:

```php
[
    ['main' => 'currency', 'table' => 'store', 'key' => 'price_curr_id', 'cleared' => 8, 'busy' => false],
    ]
```

Nulling is a matter of last resort, and nothing calls it by itself: the hard sweep does not touch these rows, and the decision to untie the products from a deleted currency is taken by whoever deleted the currency.

## 14. What Does Not Belong Here

The reference doors read **reference directories**, not the store's data. Products, orders, customers, catalog sections and the bindings to them are read with the ordinary `Sql*` methods: their volume grows along with the store, and a whole list would not pay off there.

The tree and dependency utilities are an exception by meaning: they work with the **structure** of any table, the catalog included. But they do not return rows: the tree writes, the dependencies count.

The boundary is visible in the example of options: the list of a parameter's allowed values is returned by `SysParamValues`, while which values are set on a particular product lies in `store_param` and is read with a query.
