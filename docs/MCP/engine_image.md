# Images

The `engine_image_*` section works with the files that make up the templates' look — images, fonts, icons — and with their folders. The files attached to the store's rows — a product photo, a certificate — are another matter: they are handled by the "Element Files" section.

## Where They Live

```
templates/<group>/images/…
```

Not only graphics lie here, but fonts and icons as well: the tools of the section work with any file of this folder. The folders inside it are created, renamed and deleted by `engine_image_dir_*`. `images/` itself is the root: it can neither be renamed nor deleted.

In the markup a file is named from the template group with the `{PATH}` tag: `<img src="{PATH}/images/logo.png">` — see "[System Constants](../Dev/tpl_const.md)".

## A File, Not Text

An image does not go through the correspondence as text: it is downloaded and uploaded as a file.

**`engine_image_load`** downloads a file onto this computer: by default into `mcp\melbis\` of the store folder, along the same path as on the server. The tool never reads the local copy instead of the server. png, jpeg, gif and webp come in the answer as an image as well, svg as text.

The agent application may shrink the image before handing it to the model: Claude Code brings it down to 2000×2000 pixels and compresses it. The file on the disk stays an exact copy of the server's one.

**`engine_image_add`** uploads a file onto the server: there is no separate save, and an upload along the same path again replaces the file. That is how a file gets into `images/` from this computer; inside the store the move of a static file by `engine_static_rename` leads there too.

## Replacement and Deletion Are Irreversible

Image files have no versions: a replaced or a deleted file cannot be brought back. That is why an upload onto an occupied path without `overwrite=true` refuses.

A rename and a move do not fix the links to the file: the markup and the styles go on naming it by the former path.

## The Cache

Images are given out by the web server directly, the engine's cache does not touch them, and there is no need to clear it after a replacement. The visitor's browser may go on showing the former image with the same name for a while — that is its own cache.

## Bundles

A file of the statics can be moved into the images folder too. If it was part of a bundle, the bundle's description moves with it, so a rename, a move and a deletion here answer with the `bundled` field as well — see "[Statics](engine_static.md)".
