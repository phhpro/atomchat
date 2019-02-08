<?php
/**
 * PHP Version 5 and above
 *
 * Preloader -- custom pre-requisites, access control, etc.
 *
 * @category  PHP_Chat
 * @package   PHP_Atomchat
 * @author    P H Claus <phhpro@gmail.com>
 * @copyright 2015 - 2019 P H Claus
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @version   GIT: Latest
 * @link      https://github.com/phhpro/atomchat
 */


//** Required to bypass "Headers already sent" warning
ob_start();

require './chat.php';
