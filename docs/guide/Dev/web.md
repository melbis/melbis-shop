# Web Modules

Web modules are a special integration mechanism between the Melbis server platform and its Windows client, Melbis Shop. The core idea: a full-featured Chromium-based browser is embedded inside the application, capable of opening website pages directly within the application's windows and communicating with them in both directions — passing the manager's current working context to the server and receiving results back. A developer creates a standard Melbis module, and the application opens it inside its own window.

This allows extending the application's functionality without modifying its code: analytics, reports, management tools — all of this can be implemented in PHP and connected through settings.

---

## Embedded Web Modules

Embedded modules work directly inside the application's working windows — "Products / Prices", "Business / Customers", "Business / Orders". To open an embedded web module in any of these windows, press `Ctrl+W` — a panel with an embedded browser will appear.

The key feature: when a manager navigates through records (selects a product, opens an order, switches to a customer), the application **automatically** sends a POST request to the module with the current window's context. The module always knows which object the manager is currently working with and can display relevant information — without any additional actions on their part.

There can be multiple such modules for each section — they are switched using tabs. Access rights can be configured for each module: which user groups can see it.

**Examples of embedded modules for the "Products / Prices" section:**

- Viewing extended product information
- Product sales statistics
- Product inventory tracking
- Price change history

**Examples for the "Business / Orders" and "Business / Customers" sections:**

- Customer purchase history
- Sending SMS / E-mail
- Complaint management tools

### Configuration

Embedded modules are configured in the application: **"Development → Modules and Options", the "Embedded Modules" tab**. Select a group ("Products", "Customer", "Orders") and add a module with the following parameters:

- **Name** — displayed in the module list and on the tab in the panel.
- **Module URL** — the module's URL, e.g. `http://my-shop.com/?mod=melbis_web_sample`.
- **Key** — the module's symbolic code (e.g., `melbis_web_sample`). Used for access rights verification.
- **User identification required** — if this flag is enabled, the application additionally passes the `login` and `pass_code` (MD5 hash of the password) fields in POST. This allows the module to authenticate the user and verify their permissions.

### Parameters Passed by the Application

With each request to an embedded module, the application sends a POST request with the current window's context. Below is the full set of parameters for the "Orders" section:

```php
// $mVars in the module:
[
    'get'  => ['mod' => 'melbis_web_sample'],
    'post' => [
        'order_id'                => '1',
        'order_version_id'        => '2',
        'order_version_client_id' => '1',
        'order_version_user_id'   => '1',
        'order_client_field_id'   => '3',
        'order_store_id'          => '2',
        'order_option_id'         => '3',
        'login'                   => 'admin',
        'pass_code'               => '21232f297a57a5a743894a0e4a801fc3',
    ]
]
```

In the "Products" section, the `store_id` of the selected product is passed. In the "Customers" section — the client identifier. The password is passed as an MD5 hash.

---

## The melbis_inc_auth Library

For web modules, the standard demo store distribution includes the `melbis_inc_auth` library. It handles all the boilerplate code: user authentication via `login` + `pass_code`, storing the authentication state in a PHP session, checking access rights to the module, and routing POST calls between module functions.

**The central function — `MELBIS_INC_AUTH_Router($module, $mVars)`** — performs the following steps in order:

1. Checks whether a logout has been requested (`logout` in POST).
2. Authenticates the user: via `login` + `pass_code` from POST, or from a previously saved session.
3. Verifies the user's right to access the given module (based on the permissions table in the database).
4. Writes global variables via `GlobalAssign('page', [...])`, available in all templates: authentication status (`auth`), `user_id`, and `mod` — the URL of the current module. The `{PAGE:MOD}` variable is used in templates as the base URL for AJAX requests.
5. Routes the call: if the `func` parameter is passed in POST — calls the function `MODULE_NAME_{func}($userId, $mVars)`; the word in `func` is the function's Pascal tail (`GetCataloge`). Without `func`, `MODULE_NAME_Default($userId, $mVars)` is called.

Thanks to this, the entire main module function reduces to a single line:

```php
function MELBIS_WEB_SAMPLE($mVars)
{
    return MELBIS_INC_AUTH_Router(MELBIS()->UnitName(), $mVars);
}
```

`MELBIS()->UnitName()` returns the name of the current module — it is passed to the router for rights verification and building the names of child functions.

---

A module with a namespace is written the same way, only the main function is called `Main` and the library is called by its short name (see "Modular Scripts"):

```php
namespace MELBIS_WEB_SAMPLE;

use MELBIS_INC_AUTH as AUTH;

function Main($mVars)
{
    return AUTH\Router(MELBIS()->UnitName(), $mVars);
}
```

Routing does not change along with it: the function name arrives as data, from POST, and `UnitFunc` assembles it — it checks both forms of the name, so one and the same router serves both flat modules and namespaced ones.

## Module Structure: the _Default Function

The `_Default` function is the entry point when the module is first opened. It receives `$mUserId` (or `null` if authentication failed) and the full `$mVars` array.

```php
function MELBIS_WEB_SAMPLE_Default($mUserId, $mVars)
{
    $tpl = MELBIS()->TplCreate();

    if ( $mUserId > 0 )
    {
        // Prepare the user's permissions cache for working with web_key
        MELBIS_INC_AUTH_WebKeyPrepare($mUserId);

        // Pass input variables to the template
        MELBIS()->TplAssign($tpl, 'VARS', var_export($mVars, true));

        // Pass order data for the reverse data transfer mechanism
        MELBIS()->TplAssign($tpl, 'ORDER', $mVars['post']['order'] ?? '{}');

        MELBIS()->TplParse($tpl, 'SCRIPTS', 'scripts');
        MELBIS()->TplParse($tpl, 'CONTENT', 'page');
    }
    else
    {
        // User is not authenticated — show the login form
        MELBIS()->TplParse($tpl, 'CONTENT', 'auth');
    }

    MELBIS()->GlobalAppend('page', ['title' => 'Sample Web module']);

    return MELBIS()->TplFinal($tpl, 'main');
}
```

The `auth.htm` template contains a call to the `melbis_web_auth` module — it renders the login form. After the form is submitted, `MELBIS_INC_AUTH_Router` processes the POST again, authenticates the user, and this time returns the main `page.htm` template.

---

## AJAX Requests from the Template

For interactive actions within the module, JavaScript passes the `func` parameter in POST. The router will find and call the corresponding function — provided the user is authenticated and has access to the module.

In the template, the `{PAGE:MOD}` variable contains the ready-made URL of the current module (`/?mod=melbis_web_sample`) — it is convenient to use as the endpoint for all AJAX requests:

```javascript
$('#melbis_table_cataloge').bootstrapTable({
    url: '{PAGE:MOD}',
    method: 'post',
    queryParams: function(params) {
        params.func = 'GetCataloge';  // the router will call MELBIS_WEB_SAMPLE_GetCataloge
        return params;
    },
    pagination: true,
    sidePagination: 'server',
    pageSize: 20,
    sortName: 'absindex',
    sortOrder: 'asc'
});
```

On the PHP side, the function receives control only if the user is authenticated, executes the query, and returns JSON:

```php
function MELBIS_WEB_SAMPLE_GetCataloge($mUserId, $mVars)
{
    $limit  = (int) $mVars['post']['limit'];
    $offset = (int) $mVars['post']['offset'];
    $sort   = preg_replace('/[^a-z_]/', '', $mVars['post']['sort']);
    $sort  .= ($mVars['post']['order'] == 'asc') ? ' ASC' : ' DESC';

    $command = "SELECT t.id, t.name, t.tlevel, COUNT(ts.id) AS amount
                  FROM {DBNICK}_topic t
             LEFT JOIN {DBNICK}_topic_store ts ON t.id = ts.topic_id
              GROUP BY t.id
              ORDER BY $sort";

    $data = MELBIS()->SqlSelectLimit(__LINE__, $command, $offset, $limit);

    return json_encode($data);
}
```

The same principle applies to any other module functions — searching, filtering, saving data to the database, sending notifications, and so on.

---

## Reverse Data Transfer to the Application

The most unique capability of embedded web modules is the ability to pass data **from the browser back to the Windows application** without additional HTTP requests.

This is implemented via `console.log` with a special key. The application intercepts the browser's console output and, upon detecting the `MELBIS_ORDER_UPDATE` key, applies the received data to the order being edited.

The mechanism only works in the order editing window while the order has not yet been saved. In other sections (products, customers), the web module writes changes directly to the database via the standard SQL parser methods when needed.

A practical example: the module receives all the data of the open order from the application in JSON format, displays it in interactive tables, and allows the manager to edit individual fields directly in the browser.

```php
// PHP: retrieve the order data passed by the application and send it to the template
MELBIS()->TplAssign($tpl, 'ORDER', $mVars['post']['order'] ?? '{}');
```

```javascript
// Template: parse JSON and build tables
var melbis_order = {ORDER};

function melbis_init_back() {
    for (var table in melbis_order) {
        var data    = melbis_order[table];
        var columns = [];
        for (var c in data[0]) {
            columns.push({field: c, title: c});
        }
        $('.melbis_table_order[data-table="' + table + '"]').bootstrapTable({
            columns: columns,
            data:    data
        });
    }
}

// Click on a cell — edit dialog
$('.melbis_table_order').on('click-cell.bs.table', function(event, field, value, row) {
    var table = event.target.dataset.table;
    bootbox.prompt({
        title: 'Edit value of "' + field + '"',
        value: value,
        callback: function(result) {
            if (result != null) {
                var update = {};
                update[table] = [{id: row.id, [field]: result}];
                // Pass the change to the application via the console
                console.log('MELBIS_ORDER_UPDATE' + JSON.stringify(update));
            }
        }
    });
});
```

The application reads the console output, recognizes the `MELBIS_ORDER_UPDATE` key, and applies the changes to the order fields in its interface.

---

## External Web Modules

External modules differ from embedded ones in one fundamental way: they are not tied to a specific application window and do not receive the current record's context. Instead, when opened from the application, only the user's authentication parameters (`login`, `pass_code`) are passed.

Access to external modules is available through the **"Business / Web Modules"** section — this contains a browser with a catalog of all available modules in the left panel. In addition, any of these modules can be opened in a regular browser on any device — a tablet, smartphone, or a workstation without the application installed.

This makes external modules a universal tool for dashboards, analytical reports, and administrative panels — anything that does not require binding to a specific product or order. The screenshot above shows an example of a real external module with sales analytics, financial accounting, inventory analysis, and management tools built directly into the application.

Configuring external modules — **"Development → Modules and Options", the "External Web Modules" tab**: a name, URL, and symbolic key are set for each module, along with access rights by user groups.

---

## Authentication in External Modules

Since external modules can also be opened from a regular browser, authentication here works in two modes automatically:

**When opened from the application** — Melbis Shop passes `login` and `pass_code` in POST. `MELBIS_INC_AUTH_Router` authenticates the user without a form and immediately displays the content.

**When opened directly in a browser** — POST does not contain credentials, the router returns `$mUserId = null`, and the `_Default` function substitutes the authentication form template. After a successful login, the data is saved in the PHP session, and the form is no longer shown on subsequent requests.

The same module code works in both cases — the branching happens entirely automatically inside the `melbis_inc_auth` library. The developer only needs to correctly handle the `$mUserId == null` case in the `_Default` function.

---

## Summary Diagram

```
Windows Melbis Shop
    │
    │  POST: store_id / order_id / login / pass_code
    ▼
index.php → Run('melbis_web_sample')
    │
    ▼
MELBIS_WEB_SAMPLE($mVars)
    │
    ▼
MELBIS_INC_AUTH_Router(...)
    ├─ authentication + rights verification
    ├─ func absent          → MELBIS_WEB_SAMPLE_Default($userId, $mVars)      → HTML
    ├─ func = 'GetCataloge' → MELBIS_WEB_SAMPLE_GetCataloge($userId, $mVars)  → JSON
    └─ func = 'GetGoods'    → MELBIS_WEB_SAMPLE_GetGoods($userId, $mVars)     → JSON

HTML page in the application's browser
    │  (only for the order editing window)
    ▼
console.log('MELBIS_ORDER_UPDATE{"version":[...]}')
    │
    ▼
Windows Melbis Shop — applies changes to the order
```