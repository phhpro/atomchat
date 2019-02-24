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

//** Default language ID and folder
$lang_def   = "en";
$lang_fold  = "lang";

//** Let users change theme, default ID, folder
$css        = 1;
$css_def    = "light";
$css_fold   = "css";

/**
 * Logo image, width, height, text
 * Image must be gif, jpeg, jpg, or png
 * Set $logo_i = "" to skip image, $logo_t = 0 to skip text
 */
$logo_i     = "logo.png";
$logo_w     = 32;
$logo_h     = 32;
$logo_t     = 1;

//** Use emoji conversion, emoji definition
$emo        = 1;
$emo_conf   = "emo.txt";

/*
 * Characters per post and refresh rate
 * Refresh rate in milli seconds -- 1000 ms = 1 s
 * Recommended minimum are 2000  -- lower value may freeze browser
 */
$char       = 1024;
$rate       = 2000;

//** Date format
$date       = gmdate('Y-m-d H:m');

/**
 * Randum number to prevent dupes
 * First value of minimum must be 1
 */
$rn_min     = 100;
$rn_max     = 900;

/**
 * Log mode, maximum size, folder, and name
 * Mode 0 = daily, 1 = endless -- auto-resets if size <= $log_size
 */
$log_mode   = 0;
$log_size   = 1000000;
$log_fold   = "log";
$log_name   = "atomchat-log";


/*
 ***********************************************************************
 *                                                             UPLOADS *
 ***********************************************************************
 */


//** Enable uploads
$up         = 1;

//** Uploads folder and maximum size
$up_fold    = "upload";
$up_max     = 500000;

//** Delete files after $up_old days -- requires $log_mode = 0
$up_del     = 1;
$up_old     = 30;

//** Thumbnail maximum width, height -- auto-scale
$up_tns     = 100;

//** File types -- gif, jpeg, jpg, png are being processed internally

//** Image
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


//** Bind JavaScript -- DO NOT EDIT
$js_char = "var char = $char;";
$js_rate = "var rate = $rate;";
