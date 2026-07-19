# Changelog

All notable changes to the Extension are documented in this file.

## 2.1.1 - Unreleased
- Improvement: PHP file headers updated to the standard Joomla docblock copyright format; code style only, no functional changes
- Improvement: full code style pass against the Joomla CMS phpcs ruleset (PSR-12): phpcbf auto-fixes for indentation, line endings, brace placement and whitespace, plus phpcs annotations for deliberate exceptions (`_JEXEC` guards, legacy global class names, legacy API naming). Code style only, no functional changes

## TODO
- Addition: help buttons now link to the Joomill documentation page
- Addition: Support Plugin lazy loading for PHP >= 8.4: Added a possibility to load plugin class on demand (lazy loading) when the event dispatched. For servers with PHP version >= 8.4.
- Check other updates in the past: https://github.com/joomla/Manual/tree/main/updates
- Check bc for Joomla 7 release: https://github.com/joomla/Manual/blob/main/updates/64-70/removed-backward-incompatibility.md
- Check joomla installer script volgens: C:\Obsidian\Joomill-Vault\Joomill\30-snippets\joomla-installer-script.md