# PHP Atomchat

**PHP Atomchat** is a **free PHP chat script** for low-volume and individual websites.

## Features
- Works OOTB and is completely anonymous
- No registration or passwords ever
- Emoji auto-conversion and auto-select
- Fully themeable and multi-lingual
- Responsive, cross browser, cross platform
- File uploads, next to zero dependencies
- No database required

### Config

The script generates a default configuration in `config.php` when first run or in case the file has been accidentally deleted. All relevant settings can be configured from the superuser screen and are usually effective immediately. Simply refresh the page if in doubt. You can also enable a timeout option to automatically logout inactive users after a given time. This feature is initially disabled to leave session handling to the PHP default.

Please visit the superuser screen before making the script public and change at least the prefix and suffix. The rest of the default settings should be fairly save to leave for the time being until you have a better understanding of how things work.

Note: The script will *not* check your input. If you enter text into a field expecting numbers, the script will accept that, but likely break after reading the new configuration. In case you somehow managed to invalidate the setup, your best option is to delete `config.php` and restart from scratch.

### Superuser

The superuser login gives you an extra button to open a special screen from where you can configure all relevant settings or reset the log on the fly. May come in handy when using auto-reset and the current log is approaching its limit. Just post a message to inform everyone else to hold pending uploads to prevent things getting dumped. Default login is `atomchat`.

### Logging

Logging applies to the chatlog. The script can create either daily logs or one continously growing endless log. Options exist to set maximum size and auto-reset. Downloaded logs maintain all formatting; no styles though; and can be viewed offline with any HTML capable application. The default are unmetered daily logs.

### Emojis

When enabled, this feature automatically converts registered text tokens to Unicode emojis. Hence, you'll get an image, which in fact is entirely text itself and thus saves bandwidth and doesn't cost any extra server requests. However, rendering and Unicode support varies greatly across devices and platforms. Google Chrome for desktops is particulary prone to fail. Interestingly though, the mobile version seems to have gotten all the Unicode support the desktop version is lacking. Therefore, the definitions file `emo.dat` only covers a minimal set. In addition to textual input, the script also provides a hover menu to insert emojis into the text at the current cursor position. (Requires JavaScript)

### Uploads

Image types `gif, jpeg, jpg, png` are managed internally and converted to Base64 strings to minimise server requests. Only these will render auto-scaled thumbnails. Any other types produce normal text links. You may need to edit your CSP to add an exception for the `base` handler if you don't see the thumbnails.

### Themes

The script provides a small collection of pre-made themes. These are kept simple and primarily aim to serve as guidance to write your own. Themes focus on colours, whereas the actual core styles are managed in `default.css`. You are advised not to touch this file unless you know what you're doing.

Per default the script has multi themes enabled to allow users to change themes as they see fit. Simply disable this option if you prefer a fixed theme, e.g. to match your brand's colours.

### Languages

The script attempts to auto-detect the browser's language preference and checks if a translation exists. If so, the interface will use that language. Else, whatever you have configured as default is applied. Users can change language settings at any time.

Considering an edge case where you'd be a native speaker of Japanese, on vacation in Italy, and for some obscure reason the browser was configured to prefer Turkish, your inital screenful would be Turkish. Click the question mark and select Japanese from the list. Tada, we're talking Japanese. Refer to `TRANSLATE.md` if your language is missing.

### Limitations And Issues

- If JavaScript is not available, or when using a text-mode browser, the page needs manual refreshing to execute the selected action or to view any new posts. Also, neither the character counter, nor the emoji autp-select hover menu will have any effect.

- Setting `$rate` below 2000 may freeze the browser.
