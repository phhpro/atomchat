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


//** Values of 0 mean NO -- 1 equals YES


/**
 * Script folder
 */
$fold       = "atomchat";


/**
 * Page title
 * Logo image -- $logo = ""; if not needed -- 16x16 px
 */
$page       = "PHP Atomchat";
$logo       = "favicon.png";


/**
 * Characters allowed per post
 */
$char       = 1024;


/**
 * Default theme
 * User theme -- allow users to change theme
 */
$css_def    = "grey";
$css_usr    = 1;


/**
 * Default language
 * Convert emojis
 */
$lang_def   = "en";
$emo        = 1;


/**
 * Date format
 */
$date       = date('r');


/**
 ***********************************************************************
 * UPLOADS                                            USE WITH CAUTION *
 *                                                                     *
 * This feature is rudimentary at best and has potential to break your *
 * box. Enable only when you understand the implied security risks!    *
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
 * Most free hosting providers apply filesize limits, some as little as
 * 500.000 bytes or less. Make sure this doesn't exceed any such limit.
 */
$up_max     = 2048000;


/**
 * Thumbnail width and height -- pixel
 *
 * Image previews are linked to open the original image when clicked.
 * You should keep them small to prevent excessive flow gaps.
 */
$up_tnw     = 64;
$up_tnh     = 64;


/**
 * Allowed file types
 *
 * Be adviced that the current script does not perform particular MIME
 * checks on non-image types. Hence, there is a chance someone could
 * upload a seemingly harmless text file, e.g. "foo.txt", when in fact
 * the contents of that file are executable source.
 *
 * As a minimal precaution you should never explicitely allow anything
 * directly executable on the server, like *. html, *.php, *.js, etc.
 *
 * Some free hosting providers disallow certain file types, e.g. no mp3
 * or archives. Make sure not to add any such types in the arrays below.
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
    "bz2",
    "gz",
    "rar",
    "tgz",
    "xz",
    "zip"
);
