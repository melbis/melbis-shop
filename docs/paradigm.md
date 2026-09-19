# Tasks Instead of Interfaces

Built into Melbis Shop is not only a set of programs but also a way of running a business: the owner states a task in words, the AI assistant carries it out with the store's tools, and if the tool it needs does not exist, it builds one. This page describes the approach itself. How to connect the assistant and grant it rights — "[AI Assistant](User/ai-assistant.md)".

## Two Approaches to Running a Store

**The familiar approach is a set of screens.** The platform gives you a product list, an order card, filters, bulk action buttons. Everything the business can do has been thought up in advance by the platform vendor. When the needed screen is missing, people look for an add-on module, wait for a new version, order custom development, or do the work by hand, row by row. The business adapts itself to the interface, and the knowledge of how to do things right lives in the employees' heads.

**The Melbis approach is the task.** What matters is not the screen but the result you need. The owner says what needs to be achieved. The assistant works out by itself which data and files this touches, lays out a plan, and once it is agreed, does the work and reports back. If there is no ready-made tool for the task, the assistant writes one — and every recurring task gets a small tool of its own, made by the rules of this particular store.

How the same tasks look under the two approaches:

| Task | Through screens | Through a task |
|---|---|---|
| Raise a brand's prices by 7%, except for products on promotion | a filter, a selection, a bulk change — if the platform can do that; the exceptions are removed by hand | one sentence; the assistant selects the products by the rule, shows the list of changes and, once they are approved, changes the prices with the "Prices" tool |
| Add 200 products from a supplier's price list, with photos | an import using the platform's template: match the columns, upload, sort out the errors, then the photos one by one | the assistant reads the price list in whatever form it arrived, creates the products with "Product Import", lays out the photos with "File Import"; the products are born hidden — the batch is looked over first and opened to the customer afterwards |
| Find the products with no weight that throw off the delivery calculation | a report, if there is one; otherwise an export to Excel and a manual search | an answer as a list within a minute, and an offer to make a checking tool for the logistician |
| Mark from a phone that an order has been shipped, and enter the waybill number | open the admin panel in the phone's browser, find the order, change the status, enter the number | write one sentence to the assistant: "Order 1532 shipped, waybill 20450012345678" — it carries it out with the "Orders" tool, and the order is saved as a new version |
| Add a "Bestsellers" block to the home page | look for a theme or a plugin with such a block | the assistant writes a module and a template, shows the result, and after a check by eye it goes onto the storefront |

## What Is Missing Gets Built

A new capability of the store is born from a task and goes through four steps.

1. **The task comes up for the first time.** The assistant does it itself: with
   database queries, a script on the computer, or a module of its own on the
   storefront. One-off work stays one-off — it needs no tool.
2. **The task repeats.** The assistant notices the repetition and offers to turn the
   work into an AI tool — a store module with commands described in the owner's
   words: what each one does, which fields it accepts, what it checks before writing.
3. **The owner accepts the tool.** They register it in "Development → AI Components"
   and grant the commands — each one separately, to the employees who need it.
4. **The task is done by anyone who has been granted the command.** An employee tells
   their assistant what is needed, from a computer or from a phone. They need no
   rights to the database or to the project files: the command itself knows what to
   change and how.

Here is how this looks, with product descriptions as the example.

- **Week one.** The owner writes: "Write descriptions for the forty new sneakers:
  short, about the material and the fit, no exclamation marks." The assistant reads
  a few of the store's existing descriptions, writes the new ones, shows three for
  review, and after an "ok" saves the rest. It offers to save the style rule in the
  store's memory — the owner agrees.
- **Week three.** A new shipment has arrived, and the task is the same. The assistant
  proposes a "Descriptions" tool with two commands: one prepares drafts for a chosen
  catalog section, the other saves the approved ones.
- **From then on.** The content manager has been granted both commands and has no
  rights to the database. They write to their assistant: "Descriptions for the new
  Nike shipment." The style rules are the same as the owner's — they are in the
  store's memory and in the tool itself.

Any work follows the same path: reconciling stock with a supplier, closing orders by the store's rules, checking product cards before a section is opened, preparing advertising copy. The ready-made tools that ship with Melbis Shop — "Prices", "Product Import", "File Import", "Profiles", "Scheduler" — are built the same way and serve as samples for your own.

## A Small Tool Beats a Big Interface

People are used to thinking that the richer the interface, the more the business can do. When you work through the assistant, the opposite is true: a dozen narrow tools made for your own tasks beat one universal screen.

- **A narrow tool knows your rules.** Which fields are required, which statuses are
  allowed, what to check before writing — all of this is written into the command. A
  universal screen has none of these rules; a person keeps them in their head.
- **It is easy to check.** One command, one action. An error comes back in words:
  "order 1532 has already been shipped" instead of a silently corrupted row.
- **It works from a phone.** A sentence instead of hunting for the right menu item on
  a small screen.
- **It is cheap.** A tool for a single task takes one conversation to write, not a new
  release of the platform.
- **It can be granted precisely.** An employee gets exactly one command, not access to
  the whole admin panel.

For example, the logistician has to check every morning that yesterday's orders have the phone number and the address filled in. In a typical admin panel, that means a date filter and looking through every card. Here, it is an "Order Check" tool with one command: the logistician's assistant calls it and answers with a list of three orders whose address is empty. If tomorrow the payment method has to be checked as well, the command is extended — nobody waits for a new version of the program.

## What Keeps Things in Order

Hundreds of small tools are dangerous if nobody keeps an eye on them. In Melbis, order is built into the approach itself.

- **The assistant works under a person's login**, with their rights and in their name.
  Everything it has done is signed by that person. There is no separate
  "all-powerful AI" in the store.
- **Commands are granted one at a time.** A command that has not been granted refuses
  and names the place where it is granted.
- **The work has rules.** The assistant lays out a plan before changing anything, asks
  separately for consent to change data, and does not touch a table that an employee
  currently has locked.
- **Decisions live in the store.** Rules and agreements are kept in the store's
  memory, and the established ones in the store charter. The next assistant and the
  next employee do not start from scratch.
- **An error is a refusal, not broken data.** The command answers what is wrong and
  what to do next.
- **Changes can be traced and rolled back.** Project files keep versions if this is
  enabled in the Workbench, the store's tools are saved to a backup as a single
  archive, and new products are born hidden.

## How It Works in Practice

**The base is a computer with the program.** The Melbis Shop program and an agent application are installed on a Windows computer (the application has its own Windows version requirements). The program handles the sign-in to the store: every day it obtains the license and passes the person's login to the assistant. That is why it has to be running — an ordinary laptop that is never switched off, serving as a home server, is enough.

**Access from anywhere.** If the agent application supports remote control — Claude Code, for example — the session runs on that computer, and the person writes to it from a phone or from anywhere else. Meanwhile the scripts, files, and tools stay next to the assistant: only words travel from the phone.

**The program is for the start and for heavy work.** Installing the store, employee rights, granting commands, batch work with hundreds of thousands of rows — all in the program. Day-to-day management goes through the assistant.

What an ordinary day might look like:

- **in the morning** the owner asks from a phone what happened with orders overnight —
  the assistant answers with a summary and names two orders where the customer has
  not chosen a delivery method;
- **during the day** the content manager, through their assistant, enters a new
  shipment from a price list and lays out the photos — the products are hidden for now;
- **in the evening** the owner looks over the new batch on the storefront, asks for
  three descriptions to be corrected, and opens the section.

## Roles

- **The owner** speaks in results: "by Friday, all the new products with descriptions
  and photos." They decide which tasks become tools and whom to grant commands to.
- **The developer** turns what repeats into tools and looks after their quality: how
  an AI tool is built is described in the "[AI Tools](Dev/agent_tool.md)" section of
  the developer guide.
- **An employee** works with the granted commands through their own assistant: the
  content manager writes descriptions, the logistician handles statuses and waybills,
  the salesperson changes prices. No programming is needed.
- **Without an assistant**, an employee works in the program as usual: both paths lead
  into the same database.

## What Stays in Human Hands

- **Decision and consent.** The assistant proposes, the person approves.
- **How the storefront looks.** The assistant sees the page markup but not the
  picture: a person does the looking.
- **The store's server.** The assistant has no keys to the server and never will: the
  owner sets it up, and the assistant suggests what to do and where.
- **Review before publishing.** The assistant's edits are read like a colleague's
  edits — before the customer sees them.

## The Singularity of the Business Process

When the tool for a task is built in the same conversation in which the task was set, the distance between "we need it" and "it works" shrinks to a single conversation. The business stops waiting for the platform: every recurring process gets its own tool, and the store's set of capabilities matches the set of tasks of this particular business. The platform grows at the speed of the business's own tasks — this is what we call the singularity of the business process.

## Where to Go Next

- "[AI Assistant](User/ai-assistant.md)" — connection, rights, and how to work with it.
- "[AI Tools](User/ai-tools.md)" — how the owner sets up tools and grants commands.
- "[AI Tools](Dev/agent_tool.md)" in the developer guide — how a tool is written.
- "[MCP Server Reference](MCP/intro.md)" — everything the assistant can do, operation by
  operation.
