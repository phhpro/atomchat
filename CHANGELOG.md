# Changelog

Project: [PHP Atomchat](https://github.com/phhpro/atomchat)

## [Unreleased]
### Changed
- Bell works on sender but doesn't get pushed to receiver.
- Indicator of own posts while active

## [20190311] - 2019-03-11
### Added
- Timeout feature added to superuser screen.

### Changed
- Changed font sizes in `default.css` from percentage to keyword, fixed some mobile issues and cleared redundant settings.
- Merged credits into main script.
- Moved hover menu from below textarea to top and adjusted visuals. Selecting emojis no longer hides existing text.
- Plenty of internal house-keeping renaming variables.
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
