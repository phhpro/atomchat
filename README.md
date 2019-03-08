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

### Config

The script generates a default config when first run or whenever the config file is missing. Manually editing the file is possible, but not recommended. All relevant settings can be configured from the super user screen and are usually effective immediately. Simply refresh the page if in doubt.

### Super User

The super user login gives you an extra button to open the super user screen from where you can configure all relevant settings or just reset the log on the fly. May come in handy when using auto-reset and the current log is approaching its limit. Just post a message to inform everyone else to hold pending uploads to prevent things getting dumped. Default login name is `atomchat`.

### Logging

Logging applies to the chat history. The script can create either daily logs or one continously growing endless log. Additional options exist to set a maximum size and auto-reset. Downloaded logs maintain all formatting; no styles though; and can be viewed offline with any HTML capable application. The default are unmetered daily logs.

### Emojis

The definition file `emo.dat` only covers a very basic set to avoid broken symbols on different devices and platforms.

### Uploads

Image types `gif, jpeg, jpg, png` are converted to Base64 strings to minimise server requests. Only these will render auto-scaled thumbnails. Any other types produce normal text links. You may need to edit your CSP to add an exception for the `base` handler if you don't see the thumbnails.

### Themes

The included CSS themes are probably not the most fashionable. They are kept simple and primarily aim to provide guidance.

### Languages

The script attempts to auto-detect the user's language preference and checks if a translation exists. If so, the interface will use that language. Else, the default value is applied. Users can change language settings at any time. Refer to `TRANSLATE.md` if you want to add a new translation.

### Limitations And Issues

- Mobile usability requires at least a 5 inch screen.

- If JavaScript is not available, or when using a text-mode browser, the page needs manual refreshing to execute the selected action or to view any new posts. 

- Setting `$rate` below 2000 may freeze the browser.
