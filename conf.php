<?php
/**
 * PHP Version 5 and above
 *
 * Configuration -- Integers 0 = NO, 1 = YES unless otherwise stated
 *
 * @category  PHP_Chat
 * @package   PHP_Atomchat
 * @author    P H Claus <phhpro@gmail.com>
 * @copyright 2015 - 2019 P H Claus
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @version   GIT: Latest
 * @link      https://github.com/phhpro/atomchat
 */


/*
 ***********************************************************************
 *                                                                SELF *
 ***********************************************************************
 */

//** Script folder and title
$fold       = "atomchat";
$page       = "PHP Atomchat";

//** META description and keywords
$meta_des   = "PHP Atomchat Demo";
$meta_key   = "PHP Atomchat Demo";

/*
 ***********************************************************************
 *                                                              BASICS *
 ***********************************************************************
 */

//** Default language ID and folder
$lang_def   = "en";
$lang_fold  = "lang";

//** Let users change theme, default style and folder
$css        = 1;
$css_def    = "light";
$css_fold   = "css";

//** Use emoji conversion, emoji definition
$emo        = 1;
$emo_conf   = "emo.txt";

/*
 ***********************************************************************
 *                                                                LOGO *
 ***********************************************************************
 */

/**
 * Logo image, width, height, text
 * Image must be gif, jpeg, jpg, or png
 * Set $logo_i = "" to skip image, $logo_t = 0 to skip text
 */
$logo_i     = "logo.png";
$logo_w     = 32;
$logo_h     = 32;
$logo_t     = 1;

/*
 ***********************************************************************
 *                                                                POST *
 ***********************************************************************
 */

/*
 * Characters per post, refresh rate and date format
 * Refresh rate in milli seconds -- 1000 ms = 1 s
 * Recommended minimum 2000  -- lower value may freeze browser
 */
$char       = 1024;
$rate       = 2000;
$date       = gmdate('Y-m-d H:m');

/**
 * Randum number suffix to prevent dupes
 * Uses mt_rand() and hence first digit must be 1
 */
$rn_min     = 100;
$rn_max     = 900;

/*
 ***********************************************************************
 *                                                                 LOG *
 ***********************************************************************
 */

//** Log name, folder, mode -- mode 0 = daily, 1 = endless
$log_fold   = "log";
$log_name   = "atomchat-log";
$log_mode   = 0;

//** Auto reset, maximum size, low size warning -- percent of $log_size
$log_auto   = 0;
$log_size   = 100000;
$log_warn   = 10;

/*
 ***********************************************************************
 *                                                          SUPER USER *
 ***********************************************************************
 */

/*
 * Super user prefix and suffix
 *
 * Provides an extra button to reset log without leaving the session.
 *
 * Prefix is your public user name visible to everybody.
 * Suffix doubles as password and is obviously not visible.
 *
 * To login as super user enter prefix and suffix without spaces and
 * exactly as they appear here, e.g. atomchat is not ATOMCHAT. Unless
 * both values match you'll be logged in as user, just not super user.
 */
$su_pfx     = "atom";
$su_sfx     = "chat";

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

//** Delete files after $up_old days -- only when $log_mode = 0
$up_del     = 1;
$up_old     = 30;

//** Thumbnail maximum width, height -- auto-scale
$up_tns     = 100;

//** File types -- gif, jpeg, jpg, png are managed internally

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
