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

The definition file `emo.txt` only covers a basic set to avoid broken symbols on different devices and platforms. Chrome in particular has very poor support.

### Uploads

Image types `gif, jpeg, jpg, png` are converted to Base64 strings to minimise server requests. Only Base64 types will get a thumbnail icon. Any other types are printed as normal text links.

If you don't see the thumbnails you may need to edit your CSP to add an exception for the `base` handler. Refer to the *UPLOADS* section in `conf.php` to add or remove file types. There is also an option to auto-delete old files when using daily logs.

### Themes

The provided CSS themes are probably not the most fashionable. They are kept simple and primarily serve as guidance.

### Languages

The script comes with a few demo translations. Just make a copy of `en.php` in the `lang` folder and rename it, e.g. `sv.php` for Swedish. You are welcome to submit your translation to the script's repository.

### Delay

The default post delay and refresh rate are 2 seconds. See below: Issues.

## Limitations

If Javascript is disabled or not supported, or when using a text-mode browser, the page needs to be manually refreshed to execute a selected action or to view any new posts. 

## Issues

Decreasing the recommended minimum of 2000 for the polling rate in `var rate = 2000` in `chat.js` may freeze the browser.

Using the `body` ID instead of `push` fixes distorted rendering but obviously makes any user input impossible. This applies only when viewed without or generic styles.
