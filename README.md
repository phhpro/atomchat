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

The script generates a default config when first run or in case the config file has been accidentally deleted. All relevant settings can be configured from the super user screen and are usually effective immediately. Simply refresh the page if in doubt.

Please visit the super user screen before making the script public and change at least the prefix and suffix. The rest of the default settings should be fairly save to leave for the time being until you have a better understanding of how things work.

### Super User

The super user login gives you an extra button to open a special screen from where you can configure all relevant settings or reset the log on the fly. May come in handy when using auto-reset and the current log is approaching its limit. Just post a message to inform everyone else to hold pending uploads to prevent things getting dumped. Default login is `atomchat`.

### Logging

Logging applies to the chatlog. The script can create either daily logs or one continously growing endless log. Options exist to set maximum size and auto-reset. Downloaded logs maintain all formatting; no styles though; and can be viewed offline with any HTML capable application. The default are unmetered daily logs.

### Emojis

When enabled, this feature automatically converts registered text tokens to Unicode emojis. Hence, you'll get an image, which in fact is entirely text itself and thus saves bandwidth and doesn't cost any extra server requests. However, since rendering varies greatly across devices and platforms, the definitions file `emo.dat` currently only covers a very basic set to avoid broken symbols. In addition, the script also provides an auto-select hover menu to insert emojis into the text. (Requires JavaScript)

### Uploads

Image types `gif, jpeg, jpg, png` are managed internally and converted to Base64 strings to minimise server requests. Only these will render auto-scaled thumbnails. Any other types produce normal text links. You may need to edit your CSP to add an exception for the `base` handler if you don't see the thumbnails.

### Themes

The script provides a small collection of pre-made themes. These are kept simple and primarily aim to serve as guidance to write your own. Themes focus on colours, whereas the actual core styles are managed in `default.css`. You are advised not to touch this file unless you know what you're doing.

Per default the script has multi themes enabled to allow users to change themes as they see fit. Simply disable this option if you prefer a fixed theme, e.g. to match your brand's colours.

### Languages

The script attempts to auto-detect the browser's language preference and checks if a translation exists. If so, the interface will use that language. Else, whatever you have configured as default is applied. Users can change language settings at any time.

Considering an edge case where you'd be a native speaker of Japanese, on vacation in Italy, and for some obscure reason the browser was configured to prefer Turkish, your inital screenful would be  Turkish. Click the question mark and select Japanese from the list. Tada, we're talking Japanese. Refer to `TRANSLATE.md` if your language is missing.

### Limitations And Issues

- If JavaScript is not available, or when using a text-mode browser, the page needs manual refreshing to execute the selected action or to view any new posts. Also, neither the character counter, nor the emoji autp-select hover menu will have any effect.

- Setting `$rate` below 2000 may freeze the browser.
