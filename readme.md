# Melbis Shop — self-hosted e-commerce with an AI agent on staff

**Melbis Shop** is a self-hosted e-commerce platform: your server, your domain, your data. A native **Windows back-office** and a **PHP storefront** share one business logic, and the package ships an **MCP server**, so an AI agent — Claude Code, Goose, Cherry Studio, anything that speaks MCP — works in your store as an employee: under a staff login, with that person's permissions, every action signed by their name.

Built since 2002 for stores with real teams: several suppliers and price lists, hundreds to hundreds of thousands of products, staff who must not see each other's data.

## Who it is for

A seller who has outgrown a marketplace and wants an own channel next to it — with a team of two to five people, suppliers' price lists and hundreds of SKUs. Not a page builder for a single product.

## What the AI agent does here

- **56 MCP tools** in the box: project files (modules, templates, statics, images — with version history), the database (pools of steps with locks and cache marks), catalogue trees, product files, storefront pages as a visitor sees them, and the platform documentation itself.
- **26 store tools with 244 commands** come with the demo store: products and prices, descriptions, catalogue and attributes, suppliers, orders, scheduler tasks, users, image profiles, batch import of goods and files.
- **Permissions per command.** Tools are data, not code: the owner grants each command to a person or a group. An employee with no database access still runs the agent — inside their own rights.
- **A real run, with the log and screenshots:** one message in plain words → the first working version of a store — currencies, suppliers, a catalogue with 12 products, three roles with separate rights, a business-process roadmap — in **24 min 45 s and 21.2k tokens**. Half an hour more: a redesign from a free third-party template. → https://melbis.com/en/doc/install/ai_work/

## What makes the platform different

- **One business logic** for the storefront and the back-office: an order is calculated by the same code in the Windows app and on the site. Nothing to synchronise, nothing to diverge.
- **Permissions down to the operation, the hour of the day and the server load**, plus four independent access planes on every catalogue section — descriptions, prices, placement, browse. Content managers never see purchase prices.
- **Batch back-office.** Staff load a slice of the catalogue into a local database, edit it like a spreadsheet — bulk assignments, formulas per column — and push the changes in one packet. The storefront runs almost read-only.
- **Five-level cache** whose dependencies are collected automatically from the queries a module runs. Trick cache serves an older copy under a load spike; Smart cache refreshes ahead of expiry in a quiet moment.
- **Versioned orders**, supplier warehouses and stock, tax rules on a territory tree, discount groups, customer groups with automatic loyalty rules, printed documents on FastReport.
- **A development environment inside the back-office**: modules, templates and statics edited on the server with version history, SQL consoles, per-module cache settings, AI help in the editor with any OpenAI-compatible model and your own key.
- **Storefront on your stack**: the built-in PHP template engine, or Laravel on the same order logic — [melbis/melbis-shop-laravel](https://github.com/melbis/melbis-shop-laravel).
- Interface in 8 languages; storefront content translated by hand or automatically.

## How it runs

- **Server:** an Ubuntu VPS with Docker — nginx 1.26, PHP 8.3 on Apache, MySQL 8.4, Certbot for SSL. `setup.sh <domain> <version>` installs everything on a clean machine; the Windows client runs it for you over SSH (Development → Server).
- **Client:** a native Windows application with a local Firebird database per store.
- **AI agent:** any MCP-capable application on the same Windows machine. The MCP server is part of the client package; no separate licence.

Seven steps with screenshots, from renting a server to the first task for the agent: https://melbis.com/en/doc/install/start/

## Licensing

Free to install and run in demo mode. A licence is bound to a staff login, not to a computer, and starts at **1 € per day**; monthly and yearly licences, full functionality in every plan: https://melbis.com/en/price/

## Links

- Website: https://melbis.com/
- Documentation — installation and the AI agent: https://melbis.com/en/doc/install/start/
- Reference of the Windows client: https://melbis.com/help/en/
- Releases: https://github.com/melbis/melbis-shop/releases
- Docker Hub: https://hub.docker.com/r/melbis/melbis-shop
- Packagist: https://packagist.org/packages/melbis/melbis-shop
- System requirements: https://github.com/melbis/melbis-shop/wiki/System-requirements
- Discussions: https://github.com/melbis/melbis-shop/discussions · Telegram: https://t.me/melbis_shop · YouTube: https://www.youtube.com/@Melbis-Shop
- Live example: https://astroscope.com.ua/ — a high-load optics store run on Melbis Shop
