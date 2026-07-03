# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

This is Ricky Kennedy's personal website, served as a static site from GitHub Pages at `rickykennedy.github.io`. It is plain hand-written HTML + Bootstrap/jQuery — there is **no build step, package manager, test suite, or CI pipeline** (`ci.txt` is just a notes file with reference links, not a config). Edits to `.html` files go live when pushed to `master`.

## Serving locally

There is no dev server configured. Preview any page by opening the file directly or with a static server, e.g.:

```
python3 -m http.server 8000   # then visit http://localhost:8000/index.html
```

Note: the dynamic features (contact form, chat, polling) will not function locally — they depend on external backends (see below).

## Architecture

Each page is a **self-contained HTML file** at the repo root that inlines its own `<head>` (CSS links), page-specific `<script>` blocks, and a hand-duplicated nav bar + footer. There is no templating or shared layout — changing navigation or shared styling means editing each page. Shared assets live in `public/`:

- `public/css/` — Bootstrap, jQuery UI, Font Awesome, plus site-specific `style.css` / `mystyle.css`
- `public/js/` — jQuery, Bootstrap, jQuery UI, `jquery.countdown`
- `public/fonts/`, `public/images/`, `public/files/RickyKenedy.pdf`
- `img/` — content images used across pages

The canonical, linked site is: `index.html` → `about.html`, `portfolio.html`, `resume.html`, `contact.html`. These are the pages reachable from the nav bar and footer.

### Dynamic features (external backends)

A few pages talk to services **not hosted in this repo**:

- **`contact.html` / `enquiry.html`** — POST via jQuery AJAX to `send_email.php` (also in repo). This PHP mailer only works on a PHP-capable host, not GitHub Pages.
- **`chat.html`, `polling.html`, `counter.html`** — connect to a Socket.io server at `https://www.rickykennedy.tk:3000`. That Node server lives outside this repo (the `/node` dir is gitignored). Commented-out lines in these files show older endpoints (e.g. `128.199.159.114`) — the active one is `rickykennedy.tk:3000`.

`send_email.php` sends to the hardcoded admin address `rickyicezz@gmail.com`. `test.php` is a leftover `phpinfo()` dump and should not be deployed.

## Working in this repo — important cautions

- **Many files are dead duplicates/backups.** Root contains numerous stale variants: `*.bk` / `*.html.bk`, `index_oldv.html`, `old_index.html`, `indexv2.html`, `new_index.html`, `index_simple.html`, `resumev1.html`, `portfolio.bk.html`, `index.html.default`, etc. When asked to change "the homepage" or "the resume," edit the **live linked file** (`index.html`, `resume.html`, ...), not a `new_`/`old_`/`v1`/`.bk` variant. Confirm which file is actually linked from `index.html` before editing.
- Because nav/footer markup is copy-pasted per page, a shared change must be applied to every canonical page individually.
- CDN/asset paths are relative (`public/...`); keep them relative so they resolve on GitHub Pages.
