# File Management

The "File Management" window opens by clicking the **"Associated Files"** button, which is
available in the product card, attributes, catalog sections, promo blocks, and other
objects. Any set of files (not only images) can be attached to any object.
Files are stored and catalogued on the server automatically — there is no need to manage
directories manually.

The window contains four tabs: **"File List"**, **"Edit as Image"**,
**"Edit as Document"**, and **"Additional Information"**.

## File List

The list columns are configured by the "Table Designer". Key fields:

* **Status** — the state of the file on your computer: **"Not downloaded"**,
  **"Downloaded"**, **"New/modified"** (files are downloaded from the server as
  needed and only once);
* **Added**, **File path and name** (address on the server), **File name**,
  **Size**;
* **Group** — the file's purpose; the list of groups can be extended in the "Settings Registry"
  (the basic ones are "Source file" and "File from description").

Functions: **"Add file"**, **"Delete file"** (the file is retained on the server until a final cleanup), moving, **"Download files from server"**, **"Save file to local disk"**, copying and pasting images via the clipboard
(transparency survives the paste if the source program passes it on),
showing/hiding description files and the preview window, as well as **"Perform batch file processing"** — batch conversion of images to the required format using a profile.

Product files, after being uploaded to the server, may become "read-only"
(they are edited simultaneously by many employees) — to modify such a file, delete it
and add a new one. For all other objects, full access to files is available.

## Edit as Image

A built-in image editor: the source image is shown at the top, the result at the bottom,
and on the right is a transformation panel with tabs: **"Area"** (area type
"Rectangle" or "Polygon", smoothing with the right mouse button),
**"Information"** (the bounds of the area and its proportion), **"Mask"** (protective
watermark), **"Sharpness"**, **"Intensity"**, **"Colors"**, **"Rotate/Mirror"**,
**"Size"**, **"Background"**, and **"Format and Compression"**. The resulting image can be
saved to the current file or to a new one.

PNG and WebP open together with their transparency, and the transparent places are shown
in the background color. A mask with transparency is laid over with its own transparency
taken into account.

### Size

The size of the resulting image is a link **"Quality" × "Ratio" = "Width" ×
"Height"**:

* **"Quality"** — how many megapixels the resulting image has;
* **"Ratio"** — the proportion of the sides: from the list (1:1, 4:3, 16:9 and others) or
  your own, for example 3:2 or 1.5;
* changing the quality or the ratio recalculates the width and the height; typing a width
  fits the height to the ratio, and typing a height changes the ratio.

The checkboxes:

* **"Proportionally"** — the resulting image repeats the proportion of the area; without
  the flag the area is fitted into the given width and height, and the free space is
  filled with the background;
* **"Do not enlarge"** — an area smaller than the given size is not stretched and keeps
  its own size;
* **"Original size"** — the resulting image stays at the size of the area, and the
  other size settings are closed. The flag is on by default; a chosen profile sets its
  own value.

The editor does not build a resulting image larger than 50 megapixels — the message
"The image is too large" appears instead.

### Background

* **"Background color"** — it fills the transparent places, the free margin and the cut-out
  background;
* **"Padding"** — the picture is shrunk inside the canvas by twice the padding along its
  long side, and proportionally along the short one; the size of the resulting image
  does not change;
* the slider in the top row removes a light background, such as the white background of
  a product photo: the lower the value, the more light tones go into the background
  (255 — no cut). The number to the left of the slider softens the edge of the cut, 0 —
  a hard edge;
* **"Transparent"** — the background is not filled but stays transparent. It works for
  PNG and WebP; JPEG has no transparency.

### Format and Compression

**"File Type"** — JPEG, PNG or WebP. The compression level applies to JPEG and WebP.

### Profiles

Frequently repeated processing can conveniently be saved as a **profile** and applied to a group
of files with a single click. A profile keeps the settings of the tabs: size, background,
mask, rotation, effects and format. If a ratio is set, the profile stores the exact
width and height. If the ratio is "original", the profile stores the quality only:
every picture keeps its own proportion, and only the number of pixels changes. The
profile's **"Original key"** flag
does not change the group the file is saved to: the resulting file goes into the group
chosen in the editor — by default, the group of the source file. Profiles, masks, and
other editor settings are configured in "Settings Registry" → "Basic Settings" (see
"[Settings Registry](registry.md)").

The AI assistant can maintain profiles as well — with its "Profiles" tool, in plain
words instead of XML: size, compression, margins, background, rotation, an overlay mask by
its name. It also applies a profile to an already uploaded file and puts the derived
image into the right group. The crop, and whatever the assistant does not draw — the
background removal itself — it names honestly in its answer; that stays with the image
editor.

## Edit as Document

A simple text editor for quick viewing and editing of a file (the
**"Save new file version"** button).

## Additional Information

A large text field attached to each file. By default, it stores information
about the source image; it can also be used for other purposes in collaboration with
a developer.