<?php
/**
 * PHP Version 5 and above
 *
 * Preloader -- may contain custom prerequisites, like access control,
 *              or serve as a maintenance reminder, etc.
 *
 * @category  PHP_Chat_Scripts
 * @package   PHP_Atom_Chat
 * @author    P H Claus <phhpro@gmail.com>
 * @copyright 2015 - 2018 P H Claus
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @version   GIT: Latest
 * @link      https://github.com/phhpro/atomchat
 */


//** Bypass headers already sent warning -- just in case
ob_start();

//** Load script -- ok to change chat.php to restrict spoofing
require './chat.php';
