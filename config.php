<?php
/**
 * PHP Version 5 and above
 *
 * User configuration
 *
 * @category  PHP_Chat
 * @package   PHP_Atomchat
 * @author    P H Claus <phhpro@gmail.com>
 * @copyright 2015 - 2018 P H Claus
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @version   GIT: Latest
 * @link      https://github.com/phhpro/atomchat
 */


/**
 * Script folder
 */
$fold       = "atomchat";


/**
 * Page title
 * Logo image (16x16 px) -- $logo = ""; if not needed
 */
$page       = "PHP Atomchat";
$logo       = "favicon.png";


/**
 * Characters allowed per post
 */
$char       = 1024;


/**
 * Default language and theme
 */
$lang_def   = "en";
$css_def    = "grey";


/*
 * Allow users to change theme
 * Auto-convert emojis
 *
 * 0 = NO -- 1 = YES
 */
$css        = 1;
$emo        = 1;


/**
 * Date format
 */
$date       = date('r');


/**
 ***********************************************************************
 * UPLOADS                                            USE WITH CAUTION *
 *                                                                     *
 * This feature might get you into deep water and even break your box. *
 * Enable only when you fully understand the implied security risks!   *
 ***********************************************************************
 */


/**
 * Enable uploads
 */
$up         = 1;


/**
 * Upload folder
 */
$up_fold    = "upload";


/**
 * Maximum upload size -- bytes
 *
 * Make sure this doesn't exceed your filesize limit, if any.
 */
$up_max     = 2048000;


/**
 * Thumbnail width and height -- pixel
 *
 * Image previews are linked to open the original image when clicked.
 * You should keep them small to prevent excessive gaps in the flow.
 */
$up_tnw     = 64;
$up_tnh     = 64;


/**
 * Allowed file types
 *
 * Please note that the script does not check non-image MIME types.
 * There is a chance for fake uploads. As a minimal precaution you
 * should never allow anything directly or indirectly executable on
 * the server, e.g. html, php, js, etc., and remove all types your
 * hosting may forbid, e.g. no mp3 or certain archives.
 */


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
