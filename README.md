# PHP Atomchat

**PHP Atomchat** is a **free PHP chat script** for low-volume and individual websites.

## Features
- Works OOTB and is completely anonymous
- No registration or passwords ever
- Emoji auto conversion and auto select
- Fully themeable and multilingual
- Responsive, cross browser, cross platform
- File uploads, literally zero dependencies
- One click text select for easy copy/paste
- Absolutely no database required

### News

As of v20190322 AJAX has been replaced with SSE. The AJAX option is still available but disabled.

While in theory quite possible, the script could actually run in hybrid mode, using both options at the same time. However, unless you really hate your server and just want to kill it as fast as possible, you are discouraged from attempting any such.

### Configuration

The script generates a default configuration in `config.php` when first run or in case the file has been accidentally deleted. All relevant settings can be configured from the superuser screen and are usually effective immediately. Simply refresh the page if in doubt. You can also enable a timeout option to automatically logout inactive users. The default is to leave session handling to PHP settings.

Please visit the superuser screen before making the script public and change at least the prefix and suffix. The rest of the default settings should be fairly save to keep for the time being. Default login as superuser is `atomchat`.

Also please note that whatever you enter will be accepted *AS IS*. In other words, if you put text into a field expecting numbers, the script will accept that but likely break after reading the new configuration. In any such case your best option is to delete `config.php` and start over.

### Access And *Security*

An optional access token can be enabled to restrict access, e.g. to keep bots out. You should change the default token value when using the feature. The setting is disabled per default to simplify testing, but is strongly recommended to be enabled in public environments. Just make sure to let your users know the proper token before they start hitting your inbox. Providing the complete URL would be the easiest, like `example.com/atomchat/?chat`

***The script is neither meant nor prepared for use in secure environments!***

### Logging

Logging applies to the chatlog. The script creates unmetered daily logs, which are; together with any uploaded files; auto deleted after 24 hours. The exact behaviour can be changed on the superuser screen. Downloaded logs maintain basic formatting; no styles or JavaScript though; and can be viewed offline with any HTML capable application.

***The script does not record any user data, like IP, date, browser, etc.***

### Emoji Auto Conversion

When enabled, this feature attempts to automatically convert registered text tokens into graphical Unicode emojis. Hence, you'll get an image, which in fact is entirely text. However, Unicode support varies greatly across devices and platforms. 

Google Chrome for desktops is particulary prone to fail. Interestingly though, the mobile version seems to have gotten all the Unicode support the desktop version is lacking; or maybe fonts on Android are just better prepared. Therefore, the definition file `emo.dat` covers only a minimal set. YMMV.

In addition to direct text input, the script also provides a point-and-click hover menu to insert emojis at the current cursor position and one click text select for easy copy/paste operations. Simply click the message's timestamp to auto select the corresponding text. These features require JavaScript.

### File Uploads

Image types `gif, jpeg, jpg, png` are managed internally and converted to Base64 strings to be available in offline logs. Only these will render auto scaled thumbnails. Any other types produce standard text links. You may need to edit your CSP to add an exception for the `base` handler if you don't see the thumbnails.

### Multi Themes

The script provides a small collection of pre-made themes. Themes just provide colours, while core styles are managed in `default.css`. You are advised not to touch this file unless you know what you're doing. Multi themes are enabled per default to allow users to change themes as they see fit. You can disable this option if you prefer a fixed theme.

### Multi Lingual

The script attempts to auto detect the browser's language preferences and checks if a language file exists. If so, the interface will use that language. Else, the  configured default language is applied. Users can change language settings at any time.

As an example, consider a native speaker of Japanese on a terminal configured to prefer Italian. Since an Italian language file exists, the script would initally talk Italian. However, since there also exists a Japanese language file, opening the settings screen and selecting Japanese from the list will make the script talk Japanese instantly. Refer to `TRANSLATE.md` if your language is missing.

### Limitations And Issues

Where JavaScript is not available, or when using a text mode browser, the page needs manual refreshing to execute the selected action or to update the log. In this case neither character counter, nor emoji hover menu, nor auto text select will have any effect. You can still input emojis by typing the assigned text token as illustrated on the settings screen. Text input is auto cut after reaching the character limit.

Setting a non-Western default as the page language META may produce strange effects. In mild cases just black and white emojis, in more extreme scenarios effectively placing the hover menu out of reach and hence making it completely unusable. Hard-linking a Western locale fixes the issue. Translations are not affected.

### Support

Please open an issue to request assistance; and be patient. I tend to follow-up on any feedback in due time and am open to suggestions; as long as they are meaningful.

Do *not* open an issue to request help setting up your server, or asking how to create a multi dimensional associative array or any other esoteric foo bar stuff.

That all said, happy Atomchatting.
