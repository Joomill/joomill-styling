# Changelog

All notable changes to the Extension are documented in this file.

## TODO
- Addition: help buttons now link to the Joomill documentation page
- Addition: Support Plugin lazy loading for PHP >= 8.4: Added a possibility to load plugin class on demand (lazy loading) when the event dispatched. For servers with PHP version >= 8.4.
- Check other updates in the past: https://github.com/joomla/Manual/tree/main/updates
- Check bc for Joomla 7 release: https://github.com/joomla/Manual/blob/main/updates/64-70/removed-backward-incompatibility.md
- Check joomla installer script volgens: C:\Obsidian\Joomill-Vault\Joomill\30-snippets\joomla-installer-script.md

## 2.1.1 - 11-10-2026
- Fix: the canonical no longer removes the self-referencing hreflang alternate on multilingual sites
- Fix: no second canonical is added when a component (e.g. HikaShop) already set one; conflicting canonicals made search engines ignore both
- Fix: paginated pages keep their `start`/`limitstart` parameter in the canonical instead of pointing to page 1
- Change: PHP and language file headers updated to the Joomill docblock standard; no functional changes
- Change: full code style pass against the Joomla CMS phpcs ruleset (PSR-12): phpcbf auto-fixes for indentation, line endings, brace placement and whitespace, plus phpcs annotations for deliberate exceptions (`_JEXEC` guards, legacy global class names, legacy API naming); no functional changes
