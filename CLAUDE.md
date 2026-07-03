# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

This is Ricky Kennedy's personal website, served as a static site from GitHub Pages at `rickykennedy.github.io`. It is plain hand-written HTML + Bootstrap/jQuery — there is **no build step, package manager, test suite, or CI pipeline** (`ci.txt` is just a notes file with reference links, not a config). Edits to `.html` files go live when pushed to `master`.

Because there is no CI and no staging environment, **this repo's only safety net is manual discipline before pushing.** Treat every push to `master` as a production deploy.

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

`send_email.php` sends to the hardcoded admin address `rickyicezz@gmail.com`.

## ⚠️ Known risks — flag if touched, don't silently "fix" or ignore

- **`test.php` is a live `phpinfo()` dump.** If this is deployed anywhere reachable, it leaks server config, paths, and possibly environment details publicly. It should be deleted outright, not just excluded from deploys. Flag this proactively if you notice it, even if not asked about it.
- **`send_email.php` hardcodes the recipient and has no visible rate-limiting or spam protection.** Don't copy this pattern into new forms without calling out the gap.
- **The Socket.io backend runs on a free `.tk` domain** (`rickykennedy.tk`), outside this repo's control. Free TLDs of this kind can be reclaimed or expire without notice. If `chat.html`, `polling.html`, or `counter.html` appear broken, check DNS/domain status before assuming the frontend code is at fault.

## Dead files — do not edit or use as reference

Root contains numerous stale variants: `*.bk` / `*.html.bk`, `index_oldv.html`, `old_index.html`, `indexv2.html`, `new_index.html`, `index_simple.html`, `resumev1.html`, `portfolio.bk.html`, `index.html.default`, etc.

These are treated as **candidates for future deletion, not ambiguous alternatives.** When asked to change "the homepage" or "the resume," always edit the canonical linked file (`index.html`, `resume.html`, ...) — never a `new_`/`old_`/`v1`/`.bk` variant. Confirm which file is actually linked from `index.html` before editing. Don't delete these unprompted, but don't treat a request as applying to them either.

**When a request is ambiguous** about which file it targets (e.g. "update the footer everywhere"), default to the canonical linked pages only, and say explicitly which files were changed and which stale variants were left untouched.

## Working in this repo — important cautions

- Because nav/footer markup is copy-pasted per page, a shared change must be applied to every canonical page individually — verify all of them, not just the one you started with.
- CDN/asset paths are relative (`public/...`); keep them relative so they resolve on GitHub Pages.

## Before pushing to master

Since there is no CI, tests, or staging, and pushes go live immediately:

- Open every changed page locally and click through the nav links
- Check the browser console for JS errors (the jQuery plugin stack is brittle to script load order)
- Confirm relative asset paths still resolve (`public/...`, never absolute paths)
- If the change touched nav/footer or other duplicated markup, confirm it was applied to **all** canonical pages, not just the one edited
- Confirm no edits leaked into dead/backup files instead of the canonical ones