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

Image types `gif, jpeg, jpg, png` are converted to Base64 strings to minimise server requests, and only these will get auto-scaled thumbnails. Any other types are printed as normal text links.

You may need to edit your CSP to add an exception for the `base` handler if you don't see the thumbnails. Refer to the *UPLOADS* section in `conf.php` regarding file types. There's also an option to auto-delete old files when using daily logs.

### Themes

The included CSS themes are probably not the most fashionable. They are kept simple and primarily aim to provide guidance.

### Languages

The script attempts to auto-detect the user's language preference and checks if a translation exists. If so, the interface will use that language. Else, the default set in `$lang_def` is applied.

Translations can be added by making a copy of `en.php` in the `lang` folder and renaming it, e.g. `sv.php` for Swedish, and then translating the strings. You are welcome to submit your translation to the script's repository.

## Limitations

If JavaScript is disabled or not supported, or when using a text-mode browser, the page needs to be manually refreshed to execute the selected action or to view any new posts. 

## Issues

- Decreasing the recommended minimum of the refresh rate in `$rate` may freeze the browser.

- Distorted rendering when viewing without or generic styles.

- The pseudo `push()` function in `chat.js` is not fit to handle large numbers of simultaneous users.
