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

The only logging applies to the chat history. The script can create either daily or endless logs. The setting is in `$log_mode` in `config.php`, where you can also change the default maximum size. The log will auto-reset after reaching the maximimum size limit.

### Emojis

As of February 2019 only Firefox appears to have suitable Unicode support. The script therefore only covers a very basic set of emojis, which ought to be available on most devices and platforms.

### Uploads

Image types `gif, jpeg, jpg, png` are converted to Base64 strings to minimise server requests and avoid screen flicker. Only Base64 types will produce a thumbnail icon. Any other types are printed as normal text links. The logo image; if used; must be either of these types.

Depending your server's configuration, you may need to edit your CSP to add an exception for the `base` handler. If you can see the thumbnails, you're all golden. Else, either edit `.htaccess` in your document root; or provide one in the script's folder; or the server's main configuration file. Just don't break it.

### Themes

The provided CSS themes are probably not the most fashionable. They are kept simple and primarily intended to serve as guidance.

### Languages

The script comes with a few demo translations. Just make a copy of `en.php` in the `lang` folder and rename it accordingly, e.g. `sv.php` for Swedish. You are very welcome to submit your new language file to the script's repository.

### Delay

The default post delay and refresh rate are 2 seconds. You may want to increase the delay, or possibly even randomise it, if you're expecting nasty bots or users attempting to flood the page; though this may only be relevant when using an endless log. Edit `var rate = nnnn` in `chat.js` to change. See below: Issues.

## Limitations

If you have Javascript disabled or your device doesn't support it, or if you are using a text-mode browser, you may have to manually refresh the page to execute the selected form action or to view any new posts. A common key combination is CTRL-R. 

## Issues

A polling rate of 1 second (`var rate = nnnn` in `chat.js`) appears to trigger a massive memory leak in Firefox 65; even with a minimal log. Testing in Qupzilla 1.8.9 and Chrome 72 didn't reproduce the issue.

Applying the trigger to the `body` ID instead of `push` fixes distorted rendering but effectively prevents input. This only happens when viewed either without or generic styles. Rename `.__debug.css` to `__debug.css` for testing. It's using the exact same rules, just without declaring fonts or colours. Switch to any other theme and the issue is gone.

Feedback welcome, thank you.

