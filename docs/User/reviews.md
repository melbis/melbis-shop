# Reviews

The "Reviews" section is for viewing and managing visitor reviews of products.

## Window Layout

On the left is a list of reviews; on the right are three text fields for the review: **"Liked"**,
**"Disliked"**, and **"Tips & Conclusions"** (by agreement with the developer,
you can use fewer than all three — for example, only the last one as a single
comment).

## Loading Reviews

There can be many reviews, so the required subset is loaded via the **"Advanced
Review Query"** with saving to a profile. The conditions are varied: product attributes,
user fields, and review parameters themselves — for example, by date (you can select
an automatic period template). The advanced query and profiles mechanism is described in
"[Basic Operating Principles](basics.md)".

A review contains: the product (ID, store code, name), user data
(name, e-mail, IP address), **Rating**, the number of upvotes and downvotes, and the date.

## Functions

* **"Add Review"** — specify the product for which the review is being created and fill in
  the fields directly in the table;
* **"Delete Review"** — for example, those that look like spam;
* **"Table Designer"** — configure columns.

Modified reviews are shown in bold; the **"Save"** button submits the changes to
the store.