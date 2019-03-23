# Translation Guide

## Prepare

Copy `en.php` and save as `xx.php` -- where `xx` is the language ID you are translating into, e.g. `sv.php` for Swedish. Visit the [IANA language subtag registry](https://www.iana.org/assignments/language-subtag-registry) if you don't know the language ID. Always use the macro language, e.g. the sub tag for Mesopotamian Arabic is `acm`, but we just say `ar` to indicate it's Arabic.

## Translate

Open the file in your favourite *text* editor and update the `@language` tag. Don't touch any other `@tags` and keep the file structure exactly *AS IS*. Translate the strings and save as UTF-8 (without BOM) with Unix line endings.

## Share

Submit a request to the repository if you want to share and include your new translation. However, you should be prepared to maintain your translation and keep in sync with future updates before posting a request. Abandoned language files will be purged.

## Help

If you're a native speaker of any of the existing language files and spot any typos or errors, please report your suggestions, thank you.
