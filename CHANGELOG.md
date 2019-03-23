# Changelog

Project: [PHP Atomchat](https://github.com/phhpro/atomchat)

## [Unreleased]
### Changed
- Bell works on sender but doesn't get pushed to receiver.
- Superuser screen doesn't allow selecting emojis from hover menu.

## [20190322] - 2019-03-22
### Changed
- Replaced AJAX with SSE. Polling option still available but disabled.
- Fix: Access token condition was broken on login screen.
- Fix: Missing hover state on box ID.

### Added
- New theme 'Poetry'.

## [20190321] - 2019-03-21
### Changed
- Moved copy/paste trigger from symbol to timestamp.

### Added
- Optional access token to restrict access, primarily aiming at bots.

## [20190315] - 2019-03-15
### Changed
- Fix: Hard-linked Western page language meta to prevent non-Western scripts from breaking things.
- Further increased refresh rate default from 12 to 15 seconds.

### Added
- One-click text-select for easy copy/paste operations.

## [20190315] - 2019-03-15
### Changed
- Fix: Language auto detect was missing condition.
- Moved uploads and credits to sub screens.
- Reverted buttons back to below textarea due to space gained by exporting uploads.
- Emoji hover menu now dynamically expands up to top of screen or maximum height, whichever comes first.
- Raised `$rate` default to 12 to ease bandwidth and user data.
- Merged log and upload folders into new tmp folder.

### Removed
- Log options: endless, max size, auto reset
- Dropped translations EL, IW, PL, TR

## [20190312] - 2019-03-12
### Added
- Timeout feature now available on superuser screen.
- Styles for hover state when selecting emojis from hover menu.

### Changed
- Fix: Form handler no longer accepts null values when updating config.
- Fix: Mobile scaling factor no longer cuts off.
- Fix: Form handler now keeps text intact after error, e.g. text + image when image is too big or invalid.
- Changed font sizes in `default.css` from percentage to keyword.
- Merged credits into main script.
- Moved hover menu above textarea and adjusted visuals. Selecting emojis no longer hides existing text.
- Moved themes from `css` to `themes`.
- Moved `conf.php` to `config.php`.

## [20190309] - 2019-03-09
### Added
- Emoji auto-selection hover menu. (Requires JS)
- New Theme: Lemon

### Changed
- Exported core styles to `default.css` to simplify editing. Themes now focus on providing their colours.

## [20190308] - 2019-03-08
### Added
- Super user screen to configure script without having to manually edit.
- Experimental: Timeout handler. Default: 15 minutes between posts. *Requires manual change of `$out` in `conf.php'*.

### Changed
- Language files and themes updated to match new features.
- Fix: Double-render when viewed without styles.
- Moved navigation mark-up above chatlog to keep focus when viewed without styles.

## [20190303] - 2019-03-03
### Added
- Include CHANGELOG.md
- Translation TR (Turkish)

### Changed
- Update credits section and merged into main script
- Clean-up emoji handler

## [20190302] - 2019-03-02
### Changed
- Merged home screen into main script
- Fix cookie handler
- Navigation buttons from below to left and right of textarea to save height

### Removed
- External home screen

### Added
- Translations IW (Hebrew), HI (Hindi)
