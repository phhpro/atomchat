# Translation Guide

Copy `en.php` and save as `xx.php` -- where `xx` is the language ID you are translating into, e.g. `sv.php` for Swedish. Visit the [IANA language subtag registry](https://www.iana.org/assignments/language-subtag-registry) if you don't know the language ID. Always use the macro language. For example, the sub tag for Mesopotamian Arabic is `acm`, but we just say `ar` to indicate it's Arabic.

Open the new file in a text editor and update the `@language` tag with your name and mail. Don't change any other `@tags` and keep the structure exactly AS IS. Translate the strings and save the file as UTF-8 (without BOM) and Unix line endings. Submit a request to the repository.

If you're a native speaker of any of the existing languages and spot an error, please report your suggestion, thank you.
