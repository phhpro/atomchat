<?php
/**
 * PHP Version 5 and above
 *
 * User configuration
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

//** META description and keywords
$meta_des   = "PHP Atomchat Demo";
$meta_key   = "PHP Atomchat Demo";

//** Default language and folder
$lang_def   = "en";
$lang_fold  = "lang";

//** Default theme and folder
$css_def    = "light";
$css_fold   = "css";

/**
 * Logo image, width, height, and text
 *
 * Set $logo_i = "" to skip image, $logo_t = 0 to skip text.
 *
 * Logo image type must be gif, jpeg, jpg, or png
 */
$logo_i     = "logo.png";
$logo_w     = 32;
$logo_h     = 32;
$logo_t     = 1;

//** Let users change theme, auto-convert emojis -- 0 = NO, 1 = YES
$css        = 1;
$emo        = 1;

/**
 * Maximum characters per post and date format.
 * Value of "$char" must match "char" in chat.js
 */
$char       = 1024;
$date       = gmdate('Y-m-d H:m');

/**
 * Log mode, maximum size, and folder
 *
 * Mode 0 = daily, 1 = endless -- auto-resets when size is reached
 */
$log_mode   = 0;
$log_size   = 1000000;
$log_fold   = "log";


/*
 ***********************************************************************
 *                                                             UPLOADS *
 ***********************************************************************
 */


//** Enable uploads
$up         = 1;

/*
 * Auto-delete files after $up_old days -- 0 = NO, 1 = YES
 * Applies only when $log_mode = 0
 */
$up_del     = 1;
$up_old     = 30;

//** Uploads folder and maximum size
$up_fold    = "upload";
$up_max     = 500000;

//** Thumbnail width and height -- trimmed if source is larger
$up_tnw     = 64;
$up_tnh     = 64;

/**
 * Image, Base64 -- DO NOT EDIT !!!
 *
 * Convert to Base64 strings to minimise requests.
 * Only these will get thumbnails.
 */
$up_is_b64  = array(
    "gif",
    "jpeg",
    "jpg",
    "png"
);

//** Image, other -- no thumbnails
$up_is_img  = array(
    "bmp",
    "ico"
);

//** Audio
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

//** Document
$up_is_doc  = array(
    "doc",
    "docx",
    "odt",
    "pdf",
    "txt"
);
