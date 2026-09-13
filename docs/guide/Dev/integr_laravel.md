# Laravel Integration

By default, the server core comes bundled with modules and templates for a classic storefront written on the native Melbis Shop engine. However, the system provides complete freedom in choosing frontend technologies. You can manage your store through the Melbis Shop Windows application while implementing the customer-facing storefront on any framework you prefer, such as **Laravel**.

**Important architectural requirement:** The client Windows application and the web storefront must use a unified mathematical engine for order calculations (discounts, taxes, options). Therefore, when using Laravel, you will need to configure a connection to the base logic modules of the Melbis Shop core.

To simplify this task, we have prepared a basic Laravel storefront template.  
🔗 **Repository:** [github.com/melbis/melbis-shop-laravel](https://github.com/melbis/melbis-shop-laravel)

---

## 1. Joint Installation

For both systems to work correctly, the store's server core is installed in the root directory of the site, and Laravel is installed in a subdirectory (or the appropriate routing is configured at the web server level).

Installing the core in the root:
```bash
composer create-project melbis/melbis-shop .
```

Installing Laravel in a directory (e.g., `/laravel`):
```bash
cd laravel
composer require laravel/laravel
```

---

## 2. Configuring the Bridge Between Laravel and Melbis (The Bridge)

### Step 1: Configuring Autoloading

For Laravel to gain access to the Melbis core classes, you need to update the autoloader. Open the `laravel/composer.json` file and add the Melbis namespace to the `psr-4` section, specifying the path to the core classes:

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Database\\Factories\\": "database/factories/",
        "Database\\Seeders\\": "database/seeders/",
        "Melbis\\MelbisShop\\": "../core/class/"
    }
}
```

After saving the file, regenerate the Composer autoload files:
```bash
composer dump-autoload
```

### Step 2: Connecting the Global Helper

The `Melbis.php` file contains a helper function `MELBIS()` that returns a singleton instance of `Parser`. Connect it via the `files` section in `composer.json`:

```json
"autoload": {
    "psr-4": { ... },
    "files": [
        "app/Services/Melbis.php"
    ]
}
```

After adding it, run the following again:
```bash
composer dump-autoload
```

### Step 3: Configuring the Environment

Copy the example environment file and generate the Laravel application key:
```bash
cp .env.example .env
php artisan key:generate
```

*Note:* Make sure the database connection parameters are configured correctly. The Melbis core dynamically reads its configuration directly from the root `config.json` file — this is handled by the `loadConstants()` method inside `MelbisLogic`.

---

## 3. Architecture Overview (Hybrid Approach)

This project uses a hybrid architecture that combines the classic procedural paradigm of direct Melbis Shop management with the strict MVC structure of Laravel.

### 1. The Bridge (`App\Services\MelbisLogic`)

This is the heart of the integration. The `MelbisLogic` service acts as a Bridge wrapper that:

- Initializes the database connection and the `Parser` object **once** via the Singleton pattern (static property `$parser`).
- Dynamically reads the `config.json` file and defines core constants via `loadConstants()`.
- Connects the procedural module `melbis_inc_logic` via `MELBIS()->UnitInc(...)`.
- Converts procedural core errors into standard Laravel exceptions via the static method `halt()`.
- Provides a `call()` method for invoking native Melbis functions (e.g., `$melbis->call('MELBIS_INC_LOGIC_OrderCalc', [$version])`).

The global helper function `MELBIS()` (from the `Melbis.php` file) provides convenient access to the already-initialized `Parser` from anywhere in the application:

```php
// Melbis.php
if ( !function_exists('MELBIS') ) {
    function MELBIS() {
        return \App\Services\MelbisLogic::getParser();
    }
}
```

> **Important:** `MELBIS()` throws an exception if called before a `MelbisLogic` instance has been created. Make sure `MelbisLogic` is initialized first — for example, via a Service Provider or Dependency Injection in a controller.

### 2. Thin Controllers

Laravel controllers (such as `CartController`) serve exclusively as routers. They intercept HTTP requests, receive `MelbisLogic` via Dependency Injection, manage Laravel sessions, and return JSON responses or views. They **do not contain** business logic or mathematical calculations.

### 3. Native Modules (Melbis Core)

All the heavy lifting — database queries, cart calculations, discounts, multi-threading, and product option processing — is performed inside the native Melbis directories `/units/` and `/core/`. This guarantees 100% mathematical consistency with the desktop Windows application.

### 4. Dumb Views

The frontend is built using the Laravel Blade templating engine and Bootstrap 5. Views receive pre-calculated data arrays from controllers and simply generate HTML, keeping the presentation layer completely isolated from the data processing layer.

---

## 4. Code Examples

### The Bridge Service (`MelbisLogic`)

```php
<?php

namespace App\Services;

use Exception;
use Melbis\MelbisShop\MySql;
use Melbis\MelbisShop\Parser;

class MelbisLogic
{
    private static ?Parser $parser = null;

    public function __construct()
    {
        $this->loadConstants();
        $this->initializeMelbis();

        MELBIS()->UnitInc('melbis_inc_logic');
    }

    private function loadConstants(): void
    {
        $configPath = base_path('../config.json');
        if (file_exists($configPath)) {
            $config = json_decode(file_get_contents($configPath), true) ?? [];
            foreach ($config as $const => $value) {
                if ( !defined($const) ) {
                    define($const, $value);
                }
            }
        }
    }

    private function initializeMelbis(): void
    {
        if ( self::$parser !== null ) {
            return;
        }

        $error_halt = [self::class, 'halt'];

        $db = new MySql($error_halt);
        $db->Connect(__FILE__, __LINE__);

        self::$parser = new Parser($error_halt, $db);
    }

    public static function getParser(): Parser
    {
        if (self::$parser === null) {
            throw new Exception("Melbis Shop not ready!");
        }

        return self::$parser;
    }

    public function call($functionName, $params = [])
    {
        if ( !is_callable($functionName) ) {
            throw new Exception("Function core {$functionName} not found!");
        }

        return call_user_func_array($functionName, $params);
    }

    public static function halt($mType, $mFile, $mError, $mInfo = '')
    {
        $message = "Melbis Error [$mType] in $mFile: $mError";

        if (!empty($mInfo)) {
            $message .= " | Info: " . trim($mInfo);
        }

        throw new Exception($message);
    }
}
```

### Using Eloquent Models to Connect to the Melbis Shop Database

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $table = 'ms_store';
    public $timestamps = false;

    public function images()
    {
        return $this->hasMany(FilesStore::class, 'elem_id', 'id')
                    ->where('kind_key', 'kDefault')
                    ->orderBy('pos', 'asc');
    }

    public function topics()
    {
        return $this->belongsToMany(Topic::class, 'ms_topic_store', 'store_id', 'topic_id');
    }
}
```

### Cart Controller (`CartController`)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MelbisLogic;

class CartController extends Controller
{
    // Add a product to the cart
    public function add(Request $request, MelbisLogic $melbis)
    {
        $store_id = (int) $request->input('id');
        $version  = session('melbis_version');

        if (!isset($version)) {
            $version = $melbis->call('MELBIS_INC_LOGIC_OrderCreate');
        }

        $version = $melbis->call('MELBIS_INC_LOGIC_OrderGoodsAdd', [$version, $store_id]);
        $version = $melbis->call('MELBIS_INC_LOGIC_OrderCalc', [$version]);

        session(['melbis_version' => $version]);

        return response()->json(['result' => 'OK']);
    }

    // Get the list of products for the checkout window
    public function goods(Request $request)
    {
        $version = session('melbis_version');

        if (!$version || empty($version['store'])) {
            return view('store.partials.goods_empty')->render();
        }

        return view('store.partials.goods_list', [
            'items' => $version['store']
        ])->render();
    }

    // Remove a product from the cart
    public function remove(Request $request, MelbisLogic $melbis)
    {
        $store_id = (int) $request->input('id');
        $version  = session('melbis_version');

        if ($version) {
            $version = $melbis->call('MELBIS_INC_LOGIC_OrderGoodsRemove', [$version, $store_id]);
            $version = $melbis->call('MELBIS_INC_LOGIC_OrderCalc', [$version]);

            session(['melbis_version' => $version]);
        }

        return $this->goods($request);
    }

    // Place the order and save it to the database
    public function save(Request $request, MelbisLogic $melbis)
    {
        $version = session('melbis_version');

        if (!$version || empty($version['store'])) {
            return response()->json([
                'result'  => 'ERROR_EMPTY',
                'message' => 'No items found in your cart!'
            ]);
        }

        if (isset($version['result']['value']) && $version['result']['value'] !== 'OK') {
            return response()->json([
                'result'  => $version['result']['value'],
                'message' => $version['result']['message']
            ]);
        }

        $result = $melbis->call('MELBIS_INC_LOGIC_OrderEdit', [$version]);

        if ($result['value'] !== 'OK') {
            return response()->json([
                'result'  => $result['value'],
                'message' => $result['message']
            ]);
        }

        session()->forget('melbis_version');

        return response()->json(['result' => 'OK']);
    }
}
```