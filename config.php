<?php
/**
 * PHP Version 5 and above
 *
 * User configuration -- integer values of 0 = NO, 1 = YES
 *
 * @category  PHP_Chat
 * @package   PHP_Atomchat
 * @author    P H Claus <phhpro@gmail.com>
 * @copyright 2015 - 2019 P H Claus
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @version   GIT: Latest
 * @link      https://github.com/phhpro/atomchat
 */


//** Script folder and page title
$fold       = "atomchat";
$page       = "PHP Atomchat";

//** Page META tags description and keywords
$meta_des   = "PHP Atomchat Demo";
$meta_key   = "PHP Atomchat Demo";

//** Default language and theme
$lang_def   = "en";
$css_def    = "grey";

/**
 * Logo image, width, height, and text
 * Set $logo_i = ""; to skip image, $logo_t = 0; to skip text 
 */
$logo_i     = "logo.png";
$logo_w     = 32;
$logo_h     = 32;
$logo_t     = 1;

//** Allow users to change theme, auto-convert emojis
$css        = 1;
$emo        = 1;

//** Maximum characters to post and date format
$char       = 1024;
$date       = date('r');

/**
 * Endless log
 *
 * The default setting is 0 to create a fresh log every day.
 * Set $log_less = 1; if you'd rather like an endless log.
 * Note that this might cause considerable lag depending
 * the size of the log!
 */
$log_less = 0;

//** Enable uploads -- use with caution
$up         = 1;

//** Uploads folder and maximum filesize -- default are 4 MiB
$up_fold    = "upload";
$up_max     = 4096000;

//** Thumbnail width and height -- auto-trimmed if source is larger
$up_tnw     = 64;
$up_tnh     = 64;

//** Document
$up_is_doc  = array(
    "doc",
    "docx",
    "odt",
    "pdf",
    "txt"
);

//** Image
$up_is_img  = array(
    "bmp",
    "gif",
    "ico",
    "jpeg",
    "jpg",
    "png"
);

//** Sound
$up_is_snd  = array(
    "m4a",
    "mid",
    "mp3",
    "oga",
    "ogg",
    "wav"
);

//** Video
$up_is_vid  = array(
    "avi",
    "m4v",
    "mp4",
    "mpeg",
    "mpg",
    "ogg",
    "ogv",
    "qt"
);

//** Archive
$up_is_arc  = array(
    "7z",
    "bz2",
    "gz",
    "rar",
    "tgz",
    "xz",
    "z",
    "zip"
);
