# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

`System - Joomill Styling` is a Joomla 5/6 system plugin (element `joomill`, group `system`) that applies Joomill's own branding to the Joomla administrator and adds a few SEO/meta conveniences to the frontend. It is an internal Joomill tool, not a public extension. There is no build step, dependency manager, or test suite; the repo is the source that ships as-is inside `Joomill-Styling.zip`. The installer enforces a minimum of Joomla 5.0 / PHP 8.1.

## Architecture

Modern Joomla 6 plugin using namespaced classes, the DI-container bootstrap and typed events (no legacy `joomill.php` entry file):

- `services/provider.php` is the entry point Joomla loads. It registers the plugin **lazily** (`$container->lazy(Joomill::class, ...)`, Joomla 6.1+ pattern) and calls `setApplication(Factory::getApplication())` so `$this->getApplication()` works inside the class.
- `src/Extension/Joomill.php` (`Joomill\Plugin\System\Joomill\Extension\Joomill`) is a `final` class extending `CMSPlugin` and implementing `SubscriberInterface`; it registers handlers via `getSubscribedEvents()` and type-hints the J6 event classes. Always use `$this->getApplication()`, never a `$this->app` property. The namespace root maps to `src/` via the `<namespace>` tag in `joomill.xml`.
- `script.php` (`plgSystemJoomillInstallerScript`) extends `\Joomla\CMS\Installer\InstallerScript`, declares `minimumJoomla`/`minimumPhp` (checked in `preflight`), and auto-enables the plugin in `postflight` by updating `#__extensions`.

Event handlers in `Joomill.php`, each guarded by an `isClient()` check:
- `onBeforeCompileHead` (`BeforeCompileHeadEvent`): on **admin**, injects remote brand CSS via the WebAssetManager (plus `style-dev.css` on `joomill.dev` hosts) and returns; on **site**, sets `noindex, nofollow` on `joomill.dev`, applies the `themecolor` param as the `theme-color` meta, overrides the homepage `<title>` with the `homepagetitle` param (falling back to sitename), and adds a canonical URL. The document is taken from `$event->getDocument()`. This is the correct hook for head/meta work — it was previously (wrongly) in `onContentPrepare`, which fires per content item and not on content-less pages.
- `onAfterRender` (`AfterRenderEvent`, admin only): string-replaces Atum login/brand logos and Joomla help/"forgot password" links in the page body with Joomill equivalents.

Plugin params (`themecolor`, `homepagetitle`) are defined in `joomill.xml` and read via `$this->params->get(...)`.

## Manifests (two different files — do not confuse)

- `joomill.xml` — the install/update manifest packaged with the extension. **This is the authoritative version number.** Currently `2.0.0`.
- `manifest.xml` — the update-server feed hosted at the GitHub raw URL; Joomla reads it to detect new versions. It lists its own `<version>` + download URL and `<targetplatform>`, and must be bumped (and pushed to `master`) for clients to see an update. Keep its `<version>` in sync with `joomill.xml` on every release (they have drifted before).

## Releasing / versioning

When bumping the version: update `<version>` in `joomill.xml`, update `manifest.xml` (`<version>` + the `downloadurl` zip path/tag), regenerate `Joomill-Styling.zip` from the repo contents, and tag/commit. Commit messages in history use the form `vX.Y.Z`.

## Conventions

- UI strings (name, description, field labels) live in `language/en-GB` and `language/nl-NL` (`plg_system_joomill.ini` for params, `.sys.ini` for the install screen). The name string `PLG_SYSTEM_JOOMILL` is identical in every language; only the rest is translated. The find/replace literals in `onAfterRender` are deliberately hardcoded HTML matched against Joomla's rendered admin output, not translatable copy.
- Per the global Joomill standard, any `.xml` manifest you touch must be rebuilt to the fixed manifest standard in `30-snippets/joomla-extension-manifest.md` (element order, values, section comments). This is the **agency** brand (`Jeroen Moolenschot | Joomill`, `joomill.nl`, copyright `(C) <year> Joomill`), not the `Joomill Extensions` product brand.
