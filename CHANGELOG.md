# Changelog

Project: [PHP Atomchat](https://github.com/phhpro/atomchat)

## [Unreleased]
### Changed
- Bell works on sender but doesn't get pushed to receiver.
- *Make user posts editable???*
- *Add moderator feature to edit/delete posts???*

## [20190308] - 2019-03-08
### Added
- Super user screen to configure script without having to manually edit.
- Timeout handler. Experimental. Default: 15 minutes between posts. *Requires manual change of `$out` in `conf.php'*.

### Changed
- Language files and themes updated to match new features.
- Fixed double-render when viewed without styles.
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
