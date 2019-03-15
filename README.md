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

The script generates a default configuration in `config.php` when first run or in case the file has been accidentally deleted. All relevant settings can be configured from the superuser screen and are usually effective immediately. Simply refresh the page if in doubt. You can also enable a timeout option to automatically logout inactive users after a given time. The default is to leave session handling to PHP settings.

Please visit the superuser screen before making the script public and change at least the prefix and suffix. The rest of the default settings should be fairly save to leave for the time being until you have a better understanding of how things work. Default login is `atomchat`.

Note: The script will *not* check your input. If you enter text into a field expecting numbers, the script will accept that, but likely break after reading the new configuration. In case you somehow managed to break the setup, your best option is to delete `config.php` and restart from scratch.

### Logging

Logging applies to the chatlog. The script creates unmetered daily logs, which are; together with any uploaded files; auto deleted after 24 hours. You can change the value on the superuser screen. Downloaded logs maintain all formatting; no styles though; and can be viewed offline with any HTML capable application.

### Emojis

When enabled, this feature automatically converts registered text tokens to graphical Unicode emojis. Hence, you'll get an image, which in fact is entirely text itself and thus saves bandwidth and doesn't cost any extra server requests. However, Unicode support varies greatly across devices and platforms. 

Google Chrome for desktops is particulary prone to fail. Interestingly though, the mobile version seems to have gotten all the Unicode support the desktop version is lacking. Therefore, the definition file `emo.dat` only covers a minimal set. In addition to direct textual input, the script also provides a hover menu to point-and-click insert emojis at the current cursor position. This feature requires JavaScript.

### Uploads

Image types `gif, jpeg, jpg, png` are managed internally and converted to Base64 strings to minimise server requests. Only these will render auto-scaled thumbnails. Any other types produce normal text links. You may need to edit your CSP to add an exception for the `base` handler if you don't see the thumbnails.

### Themes

The script provides a small collection of pre-made themes. These are kept simple and primarily aim to serve as guidance to *'roll your own'*. Themes focus on colours. The actual core styles are managed in `default.css`. You are advised not to touch this file unless you know what you're doing. Multi themes are enabled per default to allow users to change themes as they see fit. Disable this option if you prefer a fixed theme.

### Languages

The script attempts to auto-detect the browser's language preference and checks if a translation exists. If so, the interface will use that language. Else, the  configured default language is applied. Users can change language settings at any time.

As an example, consider a native speaker of Japanese on vacation in Italy, and the browser was configured to prefer Hindi. The script would talk Hindi. Open the settings screen and select Japanese from the list. The script talks Japanese instantly. Refer to `TRANSLATE.md` if your language is missing.

### Limitations And Issues

**PHP Atomchat** uses a very crude AJAX call to perform a pseudo push. While this works OK for the target audience running the occasinal P2P session, it means the script is contiously polling every `$rate` seconds, creating plenty of overhead on the server and bandwidth on the user end; depending the log size.

Sockets are clearly the better approach, but require running a daemon on the server; which may be well beyond the usual hobbyist user; or outright impossible on shared hosting. The primary intention of this script is to keep it as simple as possible with next to zero dependencies and minimal user efforts; and while it says PHP 5 in the header, it actually runs on obsolete PHP 4x machines all the same.

Where JavaScript is not available, or when using a text-mode browser, the page needs manual refreshing to execute the selected action or to update the log. In this case neither character counter nor emoji hover menu will have any effect. You can still input emojis by typing the assigned text token as illustrated on the settings screen. Text is auto cut after reaching the maximum set in 
`$char`.

The only possible caveat lies in setting a *bad* value for `$rate`. The default are 12 seconds and works just fine. Be careful if you intend to lower the value. The smaller the value, the more bandwidth and user data. In addition, there's a good chance it will freeze the browser; or even hang the whole system!

That all said, happy Atomchatting.
