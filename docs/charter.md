# Store Charter

The charter is how the store works, written down in the store itself: processes,
employees' duties, rules for orders, suppliers, the warehouse and products, the
technical setup. Employees read it in the Program, and the AI assistant reads it
before any work.

## Specification First, Action Second

Software development has a "specification first" approach (Spec-Driven
Development): before writing code, people write down what the result should be,
and the code is built from what is written, not from a verbal agreement. Melbis
carries this approach over to the business. The owner says how they want it — in
their own words. The assistant turns what was said into a charter document, points
out what is unclear and asks. Only on the approved document does it act and build
tools:

```
conversation → charter document → tools and data
```

This way what is said in passing becomes rules that everyone works by alike: an
employee today, a new employee a year later, the assistant in any session.
Inconsistencies and misunderstandings show up while still in the text — before
they turn into errors in the data.

## How It Is Built

* A document is a product of the "Document" type in the "Charter" section;
  sections are topics, subsections are details.
* Who reads and who edits is set by the section access rights in the
  "[Catalog](User/catalog.md)": the general part is open to everyone, the details
  only to those they concern.
* A document's status: a draft is still being written, a ready one is approved by
  the owner, and work goes by it.
* What is not yet confirmed the assistant writes in italics right where it
  belongs, and the owner answers what is marked.
* Technical names — fields, values, settings, other documents — are hidden in
  tooltips: a person reads plain text, the assistant sees the exact reference.
* Significant changes go into the "What's New" document, and the assistant shows
  them to an employee once a day.

## Where It Starts

A new store gets a sample charter: how to use the charter, how to lay out
documents, the "What's New" log and a draft of the catalog's structure. The
assistant's note for everyone sends it to the charter from the first session. The
sample is written in English: before the first edit the assistant translates it
into the language you speak with it. The layout rules are yours: they lie in the
charter itself and change like any other document. In a store without a charter
the assistant will offer to set one up once agreements pile up.

## The Charter and the Assistant's Memory

Memory is about how the assistant works with a particular person: manner, habits,
current affairs. The charter is about how the business works: it is shared by
everyone and outlives any employee and any session. A decision about how something
is done in the store the assistant offers to write into the charter, not into its
own memory.

## Where to Go Next

- "[Tasks Instead of Interfaces](paradigm.md)" — why a store is run by tasks rather
  than by an interface.
- "[AI Assistant](User/ai-assistant.md)" — connection, memory, and how to work
  with it.
