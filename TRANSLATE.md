# Translation Guide

Copy `en.php` and save as `xx.php` -- where `xx` is the language ID you are translating into, e.g. `sv.php` for Swedish. Visit the [IANA language subtag registry](https://www.iana.org/assignments/language-subtag-registry) if you don't know the language ID. Always use the macro language, e.g. the sub tag for Mesopotamian Arabic is `acm`, but we just say `ar` to indicate it's Arabic.

Open the file in your favourite editor and update the `@language` tag. Don't touch any other `@tags` and keep the file structure exactly AS IS. Translate the strings and save as UTF-8 (without BOM) with Unix line endings. Submit a request to the repository if you want to share your new translation.

Be prepared to maintain your translation and keep it in sync with future updates. If you're a native speaker of any of the existing languages and spot an error, please report your suggestion, thank you.
