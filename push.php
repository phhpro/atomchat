<?php
/**
 * PHP Version 5 and above
 *
 * Push helper
 *
 * @category  PHP_Chat
 * @package   PHP_Atomchat
 * @author    P H Claus <phhpro@gmail.com>
 * @copyright 2015 - 2019 P H Claus
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @version   GIT: Latest
 * @link      https://github.com/phhpro/atomchat
 */
header("Content-Type: text/event-stream");
$src = str_replace("\n", "", file_get_contents($_GET['src']));
echo "data: $src\n\n";
