# Melbis Shop

Melbis Shop is a commercial platform for online stores with CRM/ERP elements,
designed for large catalogs and high loads.

The platform consists of two halves running on a unified business logic:

- **Native Windows client** — an employee workstation and a development environment
  at the same time. Managers handle products, prices, orders, and customers in bulk —
  hundreds of suppliers, hundreds of thousands of products, thousands of attributes —
  at the speed of a desktop application, not a web interface. Granular permissions
  separate access between content managers, logistics staff, and sales personnel.
- **PHP engine** — serves the storefront. Between batch work sessions, data
  changes rarely, so the storefront operates in an almost read-only mode with
  multi-level caching — resulting in fast response times even under peak traffic.

Order calculation and checkout are performed by the same code both in the application
and on the website — discrepancies between the storefront and back office are
excluded by design.

## Key Features

- Batch data processing and deferred loading — minimal server load.
- Built-in image editor and HTML editor powered by the Chromium engine.
- Web modules: client extension and hybrid store management directly within its windows.
- Functional filters, query templates, and direct SQL for data retrieval.
- Multilingual support: manual and automatic translation of any store elements.
- Analytics and reports powered by FastReport.
- Workbench: code editor with autocomplete, SQL debugger, database
  structure reference, AI agents. The Melbis PHP controller is used by default;
  optionally, the storefront can be built on Laravel.
- A ready-made demo store built on Bootstrap 4 for a quick start.