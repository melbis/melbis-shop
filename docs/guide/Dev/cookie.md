# Cookies

Cookies are the second layer of data, alongside sessions, that is individual for each visitor.
Unlike sessions, they survive browser closure and do not require server-side
storage, so they are used to store long-lived small pieces of data: the selected city, storefront
version, or advertising campaign tag.

## Writing, Reading, Deleting

```php
// Write for 30 days
MELBIS()->CookieSet('town', $town, 30);

// Read (returns null if the cookie does not exist)
$town = MELBIS()->CookieGet('town');

// Read with a default value
$town = MELBIS()->CookieGet('town', 'Kyiv');

// Delete
MELBIS()->CookieClear('town');
```

The second argument of `CookieGet` is what to return when the cookie is not there;
without it that is `null`. Only absence is substituted: a cookie written as an empty
string comes back as an empty string. If the value goes into a string function, it is
better to set a default — `null` there raises an error.

Full write signature:

```php
CookieSet($mName, $mValue, $mLifeDay = 0, $mPath = "/", $mDomain = '', $mSecure = true, $mHttpOnly = false)
```

The lifetime is specified **in days**; `0` means a session cookie that expires when the browser is closed.

`CookieSet` and `CookieClear` immediately update `$_COOKIE`, so the written
value is available within the same request without waiting for the next one.

## Security Attributes

Two attributes are enforced by the engine: `SameSite=Lax` — the cookie is not
sent on third-party cross-site requests, but survives the visitor returning via
a regular link, for example from a payment gateway. And `Secure` — `true` by default;
it can be disabled via the last mandatory argument for local development over http.

**`HttpOnly` — the last argument, disabled by default.** This flag hides the value
from `document.cookie`: a browser script cannot read or overwrite such a cookie,
although it is still sent to the server with every request and `CookieGet` sees it as
normal.

It should be enabled when the cookie contains a **secret** — an autologin token, an access
key, or anything that could be used to act on behalf of the visitor. In that case,
a malicious script injected into the page via XSS will not be able to read the value and
hijack the session:

```php
// Autologin: the value must not be accessible to scripts
MELBIS()->CookieSet('client_secret', $secret, 90, '/', '.'.$domain, true, true);
```

For ordinary settings — the selected city, storefront version, a collapsed banner —
the flag is unnecessary and only gets in the way: such values are often read and written by the client itself.

> **A cookie should have a single owner.** Overwriting an `HttpOnly` cookie from JavaScript
> is not possible: the browser silently ignores the assignment — no error, no warning,
> the value simply does not change. The reverse is permitted; the server can overwrite and clear
> any cookie. Therefore, decide in advance who manages a particular cookie: if it is written by a
> client-side script, calling the server-side `CookieSet` with the same name is not allowed —
> a single such call will break the client-side write for everyone who has already received the cookie with
> that flag.

## Cookies and Cache

Just like session values, cookie contents must not be output using a regular module variable
that has caching enabled: the very first value will be baked into the cache and served to
all other visitors. Personal data is passed to the markup only through
the global array (see "Global Array").