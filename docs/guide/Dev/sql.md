# Working with the Database

All module communication with the DB goes through `MELBIS()->Sql*` methods. There are no direct calls to the DBMS driver from module code, nor should there be: the parser methods, in addition to the query itself, collect statistics for the debugger, check table prefixes, and mark modified tables for the caching system.

## 1. General Rules

Three things repeat in every call.

**The first argument is always `__LINE__`.** This is not a formality: the debugger uses the line number to show which specific module query was executed, how long it took, and with what parameters — and in case of a SQL error, where to look for it.

**The table name is written using `{DBNICK}`.** The engine substitutes the actual database prefix. If you write the prefix manually, the parser will stop with a "Wrong prefix in table name" error — and the check is intentional: it is by this very record that the engine learns which tables a module depends on, and when to drop its cache. Were it to let such a query through, the dependency would go unfound and the cache would stop being refreshed.

**Values are passed as parameters, not by substitution into the string:**

```php
$command = "SELECT id, name
              FROM {DBNICK}_store
             WHERE topic_id = :TOPIC_ID
               AND price <= :PRICE_MAX
            ";
$param = [
    'topic_id'  => $topic_id,
    'price_max' => $price_max
    ];
$store = MELBIS()->SqlSelect(__LINE__, $command, $param);
```

Placeholders in the query text are written in uppercase with a colon — `:TOPIC_ID`. The engine converts parameter array keys to uppercase automatically, so in PHP you can write them however you like. The query is sent as a prepared statement: the value reaches the database separately from the query text, so a quote or semicolon inside a value will not break anything.

**An array cannot be passed as a parameter** — the driver only binds scalars. A list for `IN (...)` is assembled in PHP, with mandatory type casting:

```php
$brand_ids = array_map('intval', $brand_set);
$brand_line = implode(',', $brand_ids);
$command = "SELECT id FROM {DBNICK}_store WHERE brand_id IN ($brand_line)";
```

The type cast here is not decoration: it is the only thing standing between a list from a user request and the SQL text.

## 2. Fetching Data

The methods differ only in the form of the result — the query text and parameters work the same way for all of them.

| Method | Returns |
|---|---|
| `SqlSelect($mLine, $mCommand, $mParams)` | array of all records |
| `SqlSelectFlat($mLine, $mCommand, $mParams)` | a single record as a flat array (empty array if nothing was found) |
| `SqlSelectValue($mLine, $mCommand, $mDefault, $mParams)` | column value(s) of a single record; the form and fallback value are set by `$mDefault` (see 2.1) |
| `SqlSelectLimit($mLine, $mCommand, $mOffset, $mLimit, $mParams)` | a page of records and the total row count |
| `SqlSelectPage($mLine, $mCommand, $mOrder, $mOffset, $mLimit, $mParams)` | the same, but filtering rows on the server side |
| `SqlSelectEnum($mLine, $mCommand, $mKey, $mValue, $mParams)` | all records where field `$mKey` equals `$mValue` |
| `SqlSelectEnumFlat($mLine, $mCommand, $mKey, $mValue, $mParams)` | a single such record |
| `SqlSelectEnumValue($mLine, $mCommand, $mDefault, $mKey, $mValue, $mParams)` | column value(s) of such a record (like `SqlSelectValue`) |
| `SqlSelectStatic` / `SqlSelectStaticFlat` / `SqlSelectStaticValue` | same as `SqlSelect`/`SqlSelectFlat`/`SqlSelectValue`, but with in-memory result caching (see "Caching") |

### 2.1. A Value Instead of a Record

`SqlSelectValue` is a wrapper around `SqlSelectFlat` for queries where you need not a record but a value: a counter, `MAX(...)`, one or two fields. The method takes the first row and returns **its column values**, not a hash with field names.

The form of the result and the behavior on an empty result set are defined by `$mDefault` — a required argument immediately after the query.

**Scalar `$mDefault`** → returns the value of the first column; if no row exists or the column value is `NULL`, returns `$mDefault` itself:

```php
$param_count = [
    'clann' => $clann
    ];
$command = "SELECT COUNT(*) FROM {DBNICK}_store WHERE clann = :clann";
$total = MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_count);
```

> `NULL` from the database means "no value", and it also falls back to `$mDefault`. This is the main use case: `SUM`, `MAX`, `MIN`, `AVG` on an empty result set return one row with a `NULL` value (not zero rows), so a default of `0` is required for them — otherwise `NULL` will be returned instead of a number.

**Array `$mDefault`** → its length defines the number of columns. A numeric array (not a hash) is returned — it can be unpacked with `list()`. Extra columns are discarded; missing columns and `NULL` columns are taken from `$mDefault` by position; on an empty result set the entire `$mDefault` is returned:

```php
$param_id = [
    'id' => $id
    ];
$command = "SELECT name, price FROM {DBNICK}_store WHERE id = :id";
list($name, $price) = MELBIS()->SqlSelectValue(__LINE__, $command, ['', 0], $param_id);
```

`SqlSelectEnumValue` does the same on top of `SqlSelectEnumFlat` (see 2.2), and `SqlSelectStaticValue` is the caching variant (see "Caching").

### 2.2. Enum Fetches: One Query Instead of a Hundred

`SqlSelectEnum` and `SqlSelectEnumFlat` solve a classic problem: a page displays forty product cards, each is a separate module, and each needs its own record from `store`. The straightforward solution produces forty queries.

These methods execute the query **once**, distribute the entire result by the value of field `$mKey`, and keep it in memory until the end of the request. The second and subsequent calls with the same query text and the same parameters do not go to the database — they retrieve the ready record from the distributed array:

```php
// Inside a card module: the query is written for all products at once,
// and the module retrieves only its own record from the result
$ids = MELBIS()->EnumGet('kStore', $id);
$command = "SELECT id, name, price
              FROM {DBNICK}_store
             WHERE id IN ( $ids )
            ";
$store = MELBIS()->SqlSelectEnumFlat(__LINE__, $command, 'id', $id);
```

The list of identifiers for such a query is prepared by the parent module via `EnumSet`, and `EnumGet` retrieves it — this pair is described in the "Caching" section.

The memory key is computed from the query text and parameters, so the query text must be **identical** across all calls. A dynamically assembled condition that changes from call to call will disable this optimization: each variant will become a separate query.

### 2.3. Paginated Output

Both methods return an array with keys `total` (total rows), `offset`, `limit`, and `rows` (records for the page).

```php
$goods = MELBIS()->SqlSelectLimit(__LINE__, $command, $offset, $limit, $param);

MELBIS()->TplAssign($tpl, [
    'GOODS' => $goods['rows'],
    'TOTAL' => $goods['total']
    ]);
```

**`SqlSelectLimit`** does not modify the query: the server returns the full result set, and the method extracts the needed slice on the PHP side. Works on any version of MySQL. With a result set of a hundred rows the difference is imperceptible; with several thousand wide records this means megabytes transferred over the network and expanded into PHP arrays just to display nine cards on screen.

**`SqlSelectPage`** wraps the query, adds `LIMIT`, and retrieves the total row count using the window function `COUNT(*) OVER()`. The server returns exactly one page. Requires MySQL 8.0+ or MariaDB 10.2+.

The sort order is passed as a separate parameter — an array of "field → direction":

```php
$order = [
    'status_pos'    => 'asc',
    'price'         => 'desc',
    's.id'          => 'asc'
    ];
$goods = MELBIS()->SqlSelectPage(__LINE__, $command, $order, $offset, $limit, $param);
```

Three requirements for the query that the engine does not verify:

- **There must be no `ORDER BY` inside the query.** Any left inside will be silently ignored: the row order of a derived table is not guaranteed by the standard, and the server is free to discard it.
- **All sort fields must be present in the query's field list.** Sorting is applied on the outside, where only the selected columns are visible. A forgotten field will cause a SQL error on the very first run.
- **Field names must not be duplicated** (`SELECT s.id, p.id ...`) — a derived table will not accept this.

The direction is validated against a list (`asc` / `desc`) — nothing else will pass. However, **the field name is only validated by character set**, and that set is intentionally broad: it allows parentheses and colons so that expressions like `RAND(:TOWN_HASH)` work. This means a function call or subquery can be smuggled through a field name — quotes and commas are filtered out, but `(SELECT ...)` consists entirely of allowed characters.

This leads to the rule: **the sort field name is set by the developer, not the visitor.** A `?order=price` value coming from the URL should be parsed through a `switch` statement and mapped to your own column name — never pass a user-supplied string directly as a key.

### 2.4. Manual Result Iteration

Rarely needed — when the result is processed as a stream and expanding it into a full array is undesirable. `SqlQuery` returns a result pointer; four methods then operate on it:

```php
$query = MELBIS()->SqlQuery(__LINE__, $command, $param);
$rows = MELBIS()->SqlNumRows($query);

MELBIS()->SqlDataSeek($query, 100);
for ( $i = 1; $i <= 20; $i++ )
{
    $hash = MELBIS()->SqlFetchHash($query);
}
```

- `SqlNumRows($query)` — number of rows in the result.
- `SqlFetchHash($query)` — the next record as an associative array.
- `SqlFetchRow($query)` — the same, but with numeric indices.
- `SqlDataSeek($query, $pos)` — move the pointer to a position.

### 2.5. A Map of Symbolic Names

A condition in a query is written by identifier, while the developer knows the symbolic name: `id` differs in every store, `skey` is the same everywhere. The `skey → id` map for any reference table is returned by `SysTableSkey('advert')` — a method from another family, covered in the "System Doors and Utilities" section. The same place explains why it is not needed for currencies, suppliers, languages, customer groups, and product parameters: those have doors of their own, and the `id` lies in every row.

## 3. Writing Data

Three methods cover almost all cases. All of them return the number of affected rows and **mark the table as modified** for the caching system themselves.

```php
// Insert
$fields = [
    'id'        => MELBIS()->SqlGenId('store_param'),
    'store_id'  => $store_id,
    'param_id'  => $param_id,
    'value_dec' => $value
    ];
MELBIS()->SqlInsert(__LINE__, '{DBNICK}_store_param', $fields);

// Update: the last argument is the condition field (or array of fields)
$fields = [
    'id'        => $store_id,
    'price'     => $price,
    'status_key'=> 'kExist'
    ];
MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_store', $fields, 'id');

// Delete
MELBIS()->SqlDelete(__LINE__, '{DBNICK}_store_param', 'store_id', $store_id);
```

For `SqlUpdate`, the condition fields are listed in the same `$fields` array: the engine distributes the array itself — whatever is named in `$mKeyField` goes into `WHERE`, the rest goes into `SET`. The condition can be composite: `MELBIS()->SqlUpdate(__LINE__, $table, $fields, ['store_id', 'param_id'])`.

`SqlDelete` accepts either a "field, value" pair or a full array of conditions: `MELBIS()->SqlDelete(__LINE__, $table, ['store_id' => $id, 'param_id' => $param])`.

**Identifiers for new records are issued by `SqlGenId`**, not `AUTO_INCREMENT`:

```php
$store_id = MELBIS()->SqlGenId('store');
```

The table name is passed **without a prefix** — as it is recorded in the `{DBNICK}_generator` table. The method atomically increments the counter and returns the new value. This scheme is used because identifiers must be synchronized between the server and the native client working with the same database.

Helper methods:

- `SqlLastInsertId()` — the last identifier issued by the DBMS itself. With an argument — `SqlLastInsertId($id)` — it does the opposite and sets it, the way `LAST_INSERT_ID(expr)` does in MySQL: the next call without an argument returns that value.
- `SqlAffectedRows()` — how many rows the last query affected.

### 3.1. Batch Writing

Import, recalculation, transfer — work where there is not one record but a thousand. A loop of `SqlGenId` and `SqlInsert` costs three queries per row there; the block methods do the same in a handful of queries.

**`SqlGenIdBlock($mTableName, $mCount)`** claims a range of identifiers and returns them as a ready-made array:

```php
$ids = MELBIS()->SqlGenIdBlock('store_param', count($values));
```

The counter moves in a single query regardless of the block size, so a thousand identifiers cost the same as one. An empty request (`$mCount` less than one) returns an empty array and never goes to the database — there is no need to check the set size before the call. The table name, as with `SqlGenId`, is written without the prefix; if there is no such row in `{DBNICK}_generator`, execution stops with an error.

**`SqlInsertBlock($mLine, $mTableName, $mRows)`** inserts an array of records in one query:

```php
$rows = [];
foreach ( $values as $param_id => $value )
{
    $rows[] = [
        'id'        => array_shift($ids),
        'store_id'  => $store_id,
        'param_id'  => $param_id,
        'value_dec' => $value
        ];
}
$count = MELBIS()->SqlInsertBlock(__LINE__, '{DBNICK}_store_param', $rows);
```

What you need to know about the set of rows:

- **The columns are set by the first row.** The rest are obliged to repeat the same
  set of keys — the order does not matter. An extra or a forgotten key stops
  execution with the row number given: the method will not silently shift the values
  into the neighbouring column.
- **The whole set is checked before the first query.** A malformed row in the middle
  means nothing was written at all.
- **An empty set** returns zero and never goes to the database.
- The total number of inserted rows is returned; the table is marked as changed once,
  at the end.

A long set is split by the engine itself — a prepared query holds a limited number of values, and a network packet a limited volume. The parts go as separate queries, so a block on its own promises no "all or nothing": if the write has to be indivisible, wrap the call in a transaction (see 5) or take the tables with a lock (see 6).

## 4. Writing with a Raw Query and Cache Invalidation

If data is written not through `SqlInsert`/`SqlUpdate`/`SqlDelete` but with a custom query via `SqlQuery`, the engine does not know which tables were modified — and the cache of modules reading those tables will remain stale. You must mark the tables manually:

```php
$command = "UPDATE {DBNICK}_store
               SET rating = rating + 1
             WHERE id = :ID
            ";
MELBIS()->SqlQuery(__LINE__, $command, ['id' => $id]);
MELBIS()->SqlTableChange(__LINE__, '{DBNICK}_store');
```

`SqlTableChange($mLine, $mTables, $mCheckAffect = true)` accepts a table name or an array of names. By default, the mark is set only if the last query actually changed something — if no rows were affected, there is no need to invalidate the cache. Pass `false` as the third argument to mark the tables unconditionally.

Returns a timestamp, or `false` if there were no changes.

This is perhaps the easiest method in the entire API to overlook: a forgotten call does not break anything immediately — the storefront simply continues to display stale data until the cache expires on its own.

## 5. Transactions

```php
MELBIS()->SqlBegin(__LINE__);
try
{
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_orders', $order);
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_order_goods', $goods);

    MELBIS()->SqlCommit(__LINE__);
}
catch ( \Throwable $e )
{
    MELBIS()->SqlRollback(__LINE__);
}
```

- `SqlBegin($mLine)` — start a transaction. Nested transactions are not supported: attempting to start a second one while the first is active stops parsing with an error.
- `SqlCommit($mLine)` — commit.
- `SqlRollback($mLine)` — roll back.
- `SqlTransactionActive()` — whether a transaction is currently in progress. Useful in a library function that does not know whether it was called inside a transaction or not.

Note that tables must use a transaction-capable engine (InnoDB) — on MyISAM the calls will execute without errors, but no rollback will occur.

## 6. Table Locking

`SqlTableLock` and `SqlTableUnlock` are **not** `LOCK TABLES` at the DBMS level, but cooperative locking through the service table `{DBNICK}_oper_block`. It is needed for coordination with the native client: while the manager is performing a batch operation on products, a background server task must not touch the same tables.

```php
if ( !MELBIS()->SqlTableLock(__LINE__, ['{DBNICK}_store', '{DBNICK}_store_param']) )
{
    // Tables are already occupied by another operation — exit
    return '';
}

// ... lengthy batch work ...

MELBIS()->SqlTableUnlock(__LINE__, ['{DBNICK}_store', '{DBNICK}_store_param']);
```

`SqlTableLock` returns `false` if the tables could not be taken at all; in that case nothing is locked and the work should be deferred. The locking is **cooperative**: it does not physically prohibit anything, and only works insofar as all participants check it. Forgetting `SqlTableUnlock` is not an option — the tables will remain marked as occupied until manually cleared.

**The method waits out an occupied table by itself.** The full signature is `SqlTableLock($mLine, $mTables, $mUserId = 0, $mTries = 10, $mPause = 500)`: the number of attempts and the pause between them in milliseconds. With the defaults, `false` arrives after ten attempts over five seconds — the method sits out somebody else's short piece of work rather than carrying the refusal through to a person. Whoever needs the former instant answer passes a single attempt:

```php
$taken = MELBIS()->SqlTableLock(__LINE__, '{DBNICK}_store', $user_id, 1);
```

The waiting is cheap: those waiting check the occupancy with an ordinary read, and only the one who sees the way clear takes the common `oper_block` latch — where it checks once more, because the way could have been seized between the two glances. A seized attempt burns just like an occupied one.

### 6.1. The Owner of a Lock

Both methods have a third argument — `$mUserId`, the employee the table is taken **for**. It goes into the `user_id` column of the `oper_block` row, and the lock is released by it as well.

```php
$taken = MELBIS()->SqlTableLock(__LINE__, '{DBNICK}_store', $user_id);

// ... work ...

$gone = MELBIS()->SqlTableUnlock(__LINE__, '{DBNICK}_store', $user_id);
```

By default there is a zero there — "held by a storefront module", as it always was, so code written earlier has not stopped working. Naming the employee is worth doing where the work goes on in their name: a stuck lock is then not anonymous, and it is visible whose request left it behind.

**Occupancy does not depend on the owner.** Nobody will take an occupied table, including whoever holds it: a repeated `SqlTableLock` with the same `$mUserId` answers `false` too. The owner decides not "who may take it" but "who may release it".

**`SqlTableUnlock` answers with the number of rows released.** A zero means there was nothing to release — most often because it was taken under one name and is being released under another, and the lock has stayed hanging:

```php
if ( MELBIS()->SqlTableUnlock(__LINE__, '{DBNICK}_store', $user_id) === 0 )
{
    // Taken under the wrong name: the table is still standing occupied
}
```

## 7. Table Maintenance

`SqlTableOptimize($mLine, $mTables = false)` executes `OPTIMIZE TABLE` and `ANALYZE TABLE`. Without the second argument, the method automatically iterates over all tables in the database that have unused space (`Data_free > 0`) and use the MyISAM or InnoDB engine:

```php
$result = MELBIS()->SqlTableOptimize(__LINE__);
```

This is a heavy, blocking operation — its place is in a nightly cron module, not in storefront code. Returns the DBMS report for each processed table.