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


//** Script folder and title
$fold       = "atomchat";
$page       = "PHP Atomchat";

//** META tags description and keywords
$meta_des   = "PHP Atomchat Demo";
$meta_key   = "PHP Atomchat Demo";

//** Default language and theme
$lang_def   = "en";
$css_def    = "grey";

/**
 * Logo image, width, height, and text
 *
 * Set $logo_i = "" to skip image, $logo_t = 0 to skip text.
 *
 * Image type must be either one of gif, jpeg, jpg, or png!
 */
$logo_i     = "logo.png";
$logo_w     = 32;
$logo_h     = 32;
$logo_t     = 1;

//** Allow users to change theme, auto-convert emojis
$css        = 1;
$emo        = 1;

/**
 * Maximum characters to post and date format.
 * Value of "$char" must match "char" in chat.js.
 */
$char       = 1024;
$date       = gmdate('Y-m-d H:m:s');

/**
 * Log mode and maximum size
 *
 * Mode 1 creates endless log, 0 creates daily logs.
 * Log will auto-reset after size limit is reached.
 */
$log_mode   = 1;
$log_size   = 10240000;

/*
 ***********************************************************************
 *                                                             UPLOADS *
 ***********************************************************************
 */

//** Enable uploads -- use with caution
$up         = 1;

//** Uploads folder and maximum filesize -- default are 4 MiB
$up_fold    = "upload";
$up_max     = 4096000;

//** Thumbnail width and height -- auto-trimmed if source is larger
$up_tnw     = 64;
$up_tnh     = 64;

/**
 * Image, Base64 -- DO NOT EDIT !!!
 *
 * These will be converted to Base64 strings to minimise server
 * requests and avoid flicker. Only Base64 types will get thumbnails.
 */
$up_is_b64  = array(
    "gif",
    "jpeg",
    "jpg",
    "png"
);

/**
 * Image, other
 *
 * Add any other image types here. These will NOT get thumbnails.
 */
$up_is_img  = array(
    "bmp",
    "ico"
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

//** Document
$up_is_doc  = array(
    "doc",
    "docx",
    "odt",
    "pdf",
    "txt"
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
