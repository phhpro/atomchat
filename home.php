<?php
/**
 * PHP Version 5 and above
 *
 * Home screen
 *
 * @category  PHP_Chat
 * @package   PHP_Atomchat
 * @author    P H Claus <phhpro@gmail.com>
 * @copyright 2015 - 2019 P H Claus
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @version   GIT: Latest
 * @link      https://github.com/phhpro/atomchat
 */


echo "            <h2>" . $lang['welcome'] . "</h2>\n" .
     "            <p>" . $lang['about'] . "</p>\n" .
     "            <h3>" . $lang['cook_perm'] . "</h3>\n" .
     "            <p>" . $lang['cook_info'] . "</p>\n" .
     "            <noscript>\n" .
     "                <h3>" . $lang['js_warn'] . "</h3>\n" .
     "                <p>" . $lang['js_info'] . "</p>\n" .
     "                <p>" . $lang['js_text'] . "</p>\n" .
     "            </noscript>\n";
