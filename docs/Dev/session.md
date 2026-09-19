# Sessions and CSRF

The Melbis storefront operates in an almost "read-only" mode and is aggressively cached,
so everything related to a specific visitor — the cart, authentication,
the selected language — lives in the session and cookies. This is the only data layer
that is unique to each visitor.

This section covers the server side: the visitor's session and form protection with its token.
Cookies are described in the neighboring "Cookies" section, and what is known about the visitor
from request headers is covered in the "Visitor" section.

## 1. Creating a Session

A session is created once, in the root script, before calling `Run()`:

```php
// index.php
require 'units/melbis.php';

MELBIS()->DefineSession('MELBIS_SHOP');

MELBIS()->Run($entry_point, $entry_param);
```

Full signature:

```php
DefineSession($mName, $mDomain = '', $mLifeTime = 0, $mPath = '/', $mSecure = true)
```

- `$mName` — the session cookie name.
- `$mDomain` — the domain; empty means the current one.
- `$mLifeTime` — lifetime in seconds; `0` means until the browser is closed.
- `$mPath` — the path for which the cookie is valid.
- `$mSecure` — transmit the cookie over HTTPS only.

In addition to actually starting the session, the method does two things: it generates a CSRF token
if one does not yet exist, and enables debug mode if a secret code was passed in the URL
(see "Debugger").

If a session has not been created, its methods cannot be called — accessing them
without `DefineSession` will result in an error.

## 2. Session Values

```php
// Write
MELBIS()->SessionSetValue('client_id', $client_id);

// Read
$client_id = MELBIS()->SessionGetValue('client_id');

// Read with a default value
$goods = MELBIS()->SessionGetValue('basket_goods', []);

// Delete a single value
MELBIS()->SessionRemoveValue('client_id');

// Destroy the entire session
MELBIS()->SessionRemove();
```

`SessionGetValue` returns `null` if the value does not exist — this is convenient to distinguish from
an empty string, but requires care when checking:

```php
$value = MELBIS()->SessionGetValue('basket_id');
$exists = !is_null($value);
```

The second argument states at once what "there is no value" means — and the check
becomes unnecessary. It is evaluated every time, even when the key is in place, so an
expensive substitution — creating an order, going to the database — is still written
through `??`: the right-hand side there is evaluated only on `null`.

```php
$order = MELBIS()->SessionGetValue('order') ?? LOGIC\OrderCreate();
```

`SessionRemove` clears the data, destroys the session, and clears the cookie — this is a full
account logout, not the deletion of a single key.

The current session identifier is returned by `SessionGetId()`. It is also available in any
template via the system tag `{PHP_SESS_ID}`.

## 3. Suspending a Session

PHP keeps the session file locked for the entire duration of the script's execution. Until the request
completes, parallel requests from the same visitor — AJAX calls, deferred block loading — will
wait in a queue, even if they only need the session for reading.

`SessionSuspend()` closes the session for writing and releases the lock; `SessionResume()`
opens it again:

```php
// Data from the session has already been read, now comes a long-running operation
MELBIS()->SessionSuspend();

$answer = MELBIS_INC_CURL_Post($url, $data);   // external API, takes several seconds

// The session is needed again — write the result
MELBIS()->SessionResume();
MELBIS()->SessionSetValue('last_answer', $answer);
```

This is the only way to avoid making the visitor's parallel requests wait for a
slow module. The rule is simple: between `Suspend` and `Resume` the session is unavailable —
neither for reading nor for writing — so everything needed from it must be read beforehand.

## 4. Form Protection: CSRF Token

A CSRF attack is a request sent to a site from a third-party resource on behalf of a
logged-in visitor: a foreign page sends a `POST` to your handler, and the
browser dutifully attaches the session cookies to it. There is only one defense against this —
verifying that the form was actually served by your storefront. This is done using a
token tied to the visitor's session.

The token is created automatically by `DefineSession`. It is inserted into a template via the
system tag `{CSRF_TOKEN}` — there is no need to pass it from a module, the tag is available
in any template:

```html
<form method="post" action="/order/">
    <input type="hidden" name="csrf" value="{CSRF_TOKEN}">
    ...
</form>
```

The handler module verifies the submitted value:

```php
$token = $mVars['post']['csrf'] ?? '';
if ( !MELBIS()->SessionCsrfCheck($token) )
{
    // The request did not come from our form
    return MELBIS()->TplFinal($tpl, 'error');
}
```

`SessionCsrfCheck` returns `true` only on an exact match with the token from
the session. The comparison is performed in a timing-attack-resistant manner, so guessing
the token character by character is pointless. If the session has not been created, the method returns `false` —
the check fails rather than throwing an error.

When a form is submitted not by a browser but by a script — AJAX, JSON, deferred loading —
the tag may not be present in the markup at all. In that case, the token is retrieved from PHP and placed
into the module's response directly:

```php
$token = MELBIS()->SessionCsrfToken();
```

It is then verified using the same `SessionCsrfCheck` — the receiving side does not care
which form the value came from.

It makes sense to add the check to every module that modifies something via `POST`:
order placement, contact form submission, cart updates. It is not needed on regular
pages that only read data.

## 5. Session and Cache

A module's output is cached together with its local data. Therefore, values
from the session **must not** be output as regular module variables — the very first
visitor will bake their name into the cache, and all other visitors will see it.

Personal data is passed only through the global array: it is
expanded in a separate pass that is never cached. This way the site header
can be cached in its entirety, while the greeting will still be personalized.

A ready-made example is in the "Global Array" section → "Visitor Data: Session and Cache".