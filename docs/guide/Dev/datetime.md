# Date and Time

The storefront server and its visitors live in different time zones, and the database is in a third one. So you don't have to keep this in mind in every module, the platform provides a single method that always operates in the project's time zone.

## DateTime

**`DateTime($mHow = 'now', $mFormat = "Y-m-d H:i:s")`** returns a formatted date **in the project's time zone**, as defined in the configuration (`MELBIS_TIME_ZONE`), not in the server's time zone. The default format is the one in which dates are stored in the database, so the method is equally convenient for writing to a table and for displaying to a visitor.

```php
// Current time in database format
$now = MELBIS()->DateTime();

// Parsing a string with a custom format
$year = MELBIS()->DateTime($store['create_time'], 'Y');

// Relative expressions are interpreted as in strtotime
$deadline = MELBIS()->DateTime('now + 3 day');

// A number is treated as a UNIX timestamp
$stamp = MELBIS()->DateTime(1767225600, 'd.m.Y');
```

The first argument accepts three types of values: a date string from the database, a string with a relative expression (`'now + 3 day'`, `'last monday'`), and a number — which is treated as a UNIX timestamp. The second argument is a standard PHP format string.

Use `DateTime` instead of `date()`: on a server running in UTC, `date()` will show the visitor a time they don't expect to see, and will write a date to the database that is out of sync with the rest.

## Dates in Templates

In markup, there is no need to format dates via PHP — the `date` and `idate` modifiers are available for this purpose (the latter substitutes the month name in the storefront's current language). From the module, the date is passed as-is, in the form it is stored in the database:

```php
MELBIS()->TplAssign($tpl, 'ORDER', $order);
```

```html
Order from {ORDER:CREATE_TIME|idate:d MMMM yyyy}
```

The `idate` modifier uses the ICU format standard rather than PHP's — see the "Modifiers" section for details.