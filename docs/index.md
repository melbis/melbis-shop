## 📖 Melbis Shop

* [Introduction](main.md)
* [Tasks instead of interfaces](paradigm.md)

### 1. User Guide

**1.1 Getting Started**

* [Program overview and main menu](User/overview.md)
* [Basic operating principles](User/basics.md)
* [Connecting to a store](User/connection.md)
* [AI Assistant](User/ai-assistant.md)

**1.2 Business**

* [Dispatcher and locks](User/dispatcher.md)
* [Scheduler](User/planner.md)
* [Currencies](User/currencies.md)
* [Discounts](User/discounts.md)
* [Taxes](User/taxes.md)
* [Suppliers](User/suppliers.md)
* [Orders](User/orders.md)
* [Customers](User/customers.md)

**1.3 Products**

* [Brands](User/brands.md)
* [Browser](User/browse.md)
* [Descriptions](User/descriptions.md)
* [Location](User/location.md)
* [Prices](User/prices.md)
* [Price lists](User/price-lists.md)
* [Recovery](User/recovery.md)
* [Reviews](User/reviews.md)

**1.4 Structure**

* [Catalog](User/catalog.md)
* [Attributes](User/attributes.md)
* [Parameters](User/parameters.md)
* [Filters](User/filters.md)
* [Promo blocks](User/promo-blocks.md)
* [Multilanguage](User/multilanguage.md)
* [Settings](User/settings.md)

**1.5 Development**

* [Settings registry](User/registry.md)
* [Order options](User/order-options.md)
* [Customer options](User/customer-options.md)
* [Report editor](User/report-editor.md)
* [Modules and options](User/module-options.md)
* [AI Tools](User/ai-tools.md)
* [Server](User/server.md)
* [Installation](User/installation.md)
* [Workbench](Dev/ide.md)
* [Web console](User/web-console.md)

**1.6 System**

* [Stores](User/connection.md#stores)
* [Connection](User/connection.md#connect)
* [Users and access rights](User/users.md)
* [Locks](User/dispatcher.md#locks)
* [Profiles](User/overview.md#profiles)
* [Activity log](User/journal.md)

**1.7 Tools**

* [File management](User/files.md)
* [HTML editor](User/html-editor.md)
* [Web modules](User/web-modules.md)
* [Reports](User/reports.md)

### 2. Developer Guide

**2.1 Getting Started**

* [Workbench](Dev/ide.md)
* [AI Help](Dev/ai_help.md)

**2.2 Project structure**

* [Storefront architecture](Dev/arch.md)
* [Configuration](Dev/config.md)
* [Naming conventions](Dev/rules.md)

**2.3 Scripts**

* [melbis.php](Dev/php.md)
* [Root scripts](Dev/root.md)
* [Module scripts](Dev/unit.md)
* [Library modules](Dev/inc.md)
* [Web modules](Dev/web.md)
* [AI Tools](Dev/agent_tool.md)

**2.4 Template engine**

* [Template groups](Dev/tpl_group.md)
* [Template engine methods](Dev/tpl_api.md)
* [Template syntax](Dev/tpl_syntax.md)
* [Modifiers](Dev/tpl_mod.md)
* [System constants](Dev/tpl_const.md)
* [Global array](Dev/tpl_global.md)
* [Static bundle](Dev/tpl_bundle.md)

**2.5 Data**

* [Working with the database](Dev/sql.md)
* [System doors and utilities](Dev/sys.md)
* [Date and time](Dev/datetime.md)
* [Visitor](Dev/visitor.md)
* [Sessions and CSRF](Dev/session.md)
* [Cookies](Dev/cookie.md)

**2.6 Performance**

* [Caching](Dev/cache.md)
* [Lazy loading](Dev/lazy.md)
* [Debugger](Dev/debug.md)

**2.7 Operations**

* [Task scheduler](Dev/cron.md)
* [Logs, errors and metrics](Dev/log.md)
* [Utility methods](Dev/util.md)

**2.8 Integrations**

* [Laravel integration](Dev/integr_laravel.md)

### 3. MCP Server Reference

**3.1 Getting Started**

* [Introduction and terms](MCP/intro.md)
* [Sign-in, rights, license](MCP/access.md)
* [Answers and refusals](MCP/answers.md)

**3.2 Session**

* [Concept](MCP/session.md)
* [session_init](MCP/session_init.md)
* [session_connect](MCP/session_connect.md)
* [session_rules_accept](MCP/session_rules_accept.md)
* [session_clear](MCP/session_clear.md)

**3.3 Engine**

* [Concept](MCP/engine.md)
* General
    * [engine_search](MCP/engine_search.md)
    * [engine_whole_load](MCP/engine_whole_load.md)
* Map
    * [engine_map_tree](MCP/engine_map_tree.md)
    * [engine_map_units](MCP/engine_map_units.md)
* [PHP files](MCP/engine_php.md)
    * [engine_php_load](MCP/engine_php_load.md)
    * [engine_php_save](MCP/engine_php_save.md)
    * [engine_php_add](MCP/engine_php_add.md)
    * [engine_php_rename](MCP/engine_php_rename.md)
    * [engine_php_remove](MCP/engine_php_remove.md)
* [Templates](MCP/engine_html.md)
    * [engine_html_load](MCP/engine_html_load.md)
    * [engine_html_save](MCP/engine_html_save.md)
    * [engine_html_add](MCP/engine_html_add.md)
    * [engine_html_rename](MCP/engine_html_rename.md)
    * [engine_html_remove](MCP/engine_html_remove.md)
* [Template groups](MCP/engine_template.md)
    * [engine_template_add](MCP/engine_template_add.md)
    * [engine_template_rename](MCP/engine_template_rename.md)
    * [engine_template_remove](MCP/engine_template_remove.md)
* [Statics](MCP/engine_static.md)
    * [engine_static_load](MCP/engine_static_load.md)
    * [engine_static_save](MCP/engine_static_save.md)
    * [engine_static_add](MCP/engine_static_add.md)
    * [engine_static_rename](MCP/engine_static_rename.md)
    * [engine_static_remove](MCP/engine_static_remove.md)
    * [engine_static_dir_add](MCP/engine_static_dir_add.md)
    * [engine_static_dir_rename](MCP/engine_static_dir_rename.md)
    * [engine_static_dir_remove](MCP/engine_static_dir_remove.md)
* [Images](MCP/engine_image.md)
    * [engine_image_load](MCP/engine_image_load.md)
    * [engine_image_add](MCP/engine_image_add.md)
    * [engine_image_rename](MCP/engine_image_rename.md)
    * [engine_image_remove](MCP/engine_image_remove.md)
    * [engine_image_dir_add](MCP/engine_image_dir_add.md)
    * [engine_image_dir_rename](MCP/engine_image_dir_rename.md)
    * [engine_image_dir_remove](MCP/engine_image_dir_remove.md)
* [Versions](MCP/engine_history.md)
    * [engine_history_list](MCP/engine_history_list.md)
    * [engine_history_content](MCP/engine_history_content.md)
* [Configuration and cache](MCP/engine_dev.md)
    * [engine_dev_config](MCP/engine_dev_config.md)
    * [engine_dev_cache_clear](MCP/engine_dev_cache_clear.md)
* [Database](MCP/engine_db.md)
    * [engine_db_tables](MCP/engine_db_tables.md)
    * [engine_db_select](MCP/engine_db_select.md)
    * [engine_db_execute](MCP/engine_db_execute.md)
    * [engine_db_locks](MCP/engine_db_locks.md)
    * [engine_db_unlocks](MCP/engine_db_unlocks.md)
* [Element files](MCP/engine_files.md)
    * [engine_files_add](MCP/engine_files_add.md)
    * [engine_files_load](MCP/engine_files_load.md)
    * [engine_files_remove](MCP/engine_files_remove.md)

**3.4 Memory**

* [Concept](MCP/memory.md)
* [memory_list](MCP/memory_list.md)
* [memory_load](MCP/memory_load.md)
* [memory_save](MCP/memory_save.md)
* [memory_remove](MCP/memory_remove.md)

**3.5 AI Tools**

* [Concept](MCP/tool.md)
* [tool_list](MCP/tool_list.md)
* [tool_run](MCP/tool_run.md)
* [tool_export](MCP/tool_export.md)

**3.6 Storefront**

* [Concept](MCP/shop.md)
* [shop_page](MCP/shop_page.md)
* [shop_run](MCP/shop_run.md)
* [shop_download](MCP/shop_download.md)
