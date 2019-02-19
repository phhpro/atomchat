# PHP Atomchat

**PHP Atomchat** is a **free PHP chat script** for low-volume and individual websites.

## Features
- Works OOTB
- Completely anonymous
- No registration or passwords ever
- Emoji auto-conversion
- File uploads
- Themeable
- Multi-lingual
- No database required

### Logging

The only logging applies to the chat history. The script can create either daily or endless logs. The setting is in `$log_mode` in `conf.php`, where you can also change the default maximum size. The log will auto-reset after reaching the maximimum size limit.

### Emojis

As of February 2019 only Firefox appears to have suitable Unicode support. The script therefore only covers a very basic set of emojis, which ought to be available on most devices and platforms. Edit `emo.txt` to add or modify.

### Uploads

Image types `gif, jpeg, jpg, png` are converted to Base64 strings to minimise server requests and avoid screen flicker. Only Base64 types will produce a thumbnail icon. Any other types are printed as normal text links. The logo image; if used; must be either of these types.

Depending your server's configuration, you may need to edit your CSP to add an exception for the `base` handler. If you can see the thumbnails, you're all golden. Else, either edit `.htaccess` in your document root; or provide one in the script's folder.

Refer to the *UPLOADS* section in `conf.php` to add or remove file types. There is also an option to auto-delete old files when using daily logs.

### Themes

The provided CSS themes are probably not the most fashionable. They are kept simple and primarily intended to serve as guidance.

### Languages

The script comes with a few demo translations. Just make a copy of `en.php` in the `lang` folder and rename it accordingly, e.g. `sv.php` for Swedish. You are very welcome to submit your new language file to the script's repository.

### Delay

The default post delay and refresh rate are 2 seconds. See below: Issues.

## Limitations

If Javascript is disabled or not supported, or when using a text-mode browser, the page needs to be manually refreshed to execute a selected action or to view any new posts. 

## Issues

Decreasing the recommended minimum of 2000 for the polling rate in `var rate = 2000` in `chat.js` may freeze the browser.

Using the `body` ID instead of `push` fixes distorted rendering but obviously makes any user input impossible. This applies only when viewed without or generic styles. Rename `.__debug.css` to `__debug.css` for testing.
