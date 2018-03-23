<?php
/**
 * PHP Version 5 and above
 *
 * Main script and configuration
 *
 * @category  PHP_Chat_Scripts
 * @package   PHP_Atom_Chat
 * @author    P H Claus <phhpro@gmail.com>
 * @copyright 2015 - 2018 P H Claus
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @version   GIT: Latest
 * @link      https://github.com/phhpro/atomchat
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 */


/**
 ***********************************************************************
 *                                                   BEGIN USER CONFIG *
 ***********************************************************************
 */


//** Values of 0 mean NO -- 1 equals YES


/**
 * Script folder
 */
$fold     = "atomchat";

/**
 * Chat title
 * Chat image -- $image = "" if not needed
 */
$title    = "PHP Atom Chat";
$image    = '<img src=favicon.png width=16 height=16 alt=""/>';

/**
 * Maximum characters allowed per post
 */
$max_char = 256;

/**
 * Default theme
 * User theme selection
 */
$css_def  = "grey";
$css_usr  = 1;

/**
 * Default language
 * Convert emojis
 */
$lang_def = "en";
$emo_auto = 1;


/**
 ***********************************************************************
 *                                                     END USER CONFIG *
 ***********************************************************************
 */


//** Script version
$make     = "20180323";

//** Check protocol
if (isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS']) {
    $prot = "s";
} else {
    $prot = "";    
}

//** Build URL reference
$host     = "http" . $prot . "://" . $_SERVER['HTTP_HOST'] .
            "/" . $fold . "/";

//** Initial screen and status
$init      = "./init.php";
$stat      = "";

//** Link logfile and themes config
$chat_data = "log/" . date('Y-m-d') . ".html";
$css_conf  = "css/__config.txt";

//** Link emoji config, arrays, and code
$emo_conf  = "emoji.txt";
$emo_parr  = array();
$emo_sarr  = array();
$emo_code  = "";

//** Init session
session_start();
$_SESSION['test'] = 1;

//** Test session
if ($_SESSION['test'] !== 1) {
    echo "<p>Missing session cookie!</p>\n" .
         "<p>Please edit your browser's cookie " .
         "settings and then try again.</p>\n";
    exit;
} else {
    unset($_SESSION['test']);
}

//** Check language selection
if (isset($_POST['de'])) {
    $_SESSION['lang'] = "de";
}

if (isset($_POST['en'])) {
    $_SESSION['lang'] = "en";
}

if (isset($_POST['es'])) {
    $_SESSION['lang'] = "es";
}

//** Default language session
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = $lang_def;
}

$lang_id = $_SESSION['lang'];

//** Link language config and data
$lang_conf = "./lang/__config.php";
$lang_data = "./lang/" . $lang_id . ".php";

//** Check log folder
if (!is_dir('log')) {

    if (mkdir('log') === false) {
        echo "Cannot write logfile!";
        exit;
    }
}

//** Check language folder
if (!is_dir('lang')) {
    echo "Missing language folder!";
    exit;
}

//** Check if file exists and is valid
if (
    file_exists($lang_data)
    || file_exists($lang_conf)
    || file_exists($css_conf)
    || $emo_auto === 1
) {

    if (file_exists($lang_data)) {
        $file_data = $lang_data;
        $file_text = "language file";
    }

    if (file_exists($lang_conf)) {
        $file_data = $lang_conf;
        $file_text = "language configuration";
    }

    if (file_exists($css_conf)) {
        $file_data = $css_conf;
        $file_text = "theme configuration";
    }

    if ($emo_auto === 1) {
        $file_data = $emo_conf;
        $file_text = "emoji configuration";
    }

    $file_trim = file_get_contents($file_data);
    /**
     * Check valid file -- returns true if file contains only the BOM
     * (byte order mark) or empty lines, in which case the test failed.
     */
    if (filesize($file_data) <16
        && trim($file_trim) === false
    ) {
        echo "Invalid $file_text!";
        exit;
    }
} else {
    echo "Missing $file_text!";
    exit;
}

//** Link default language ID and load config
$lang_mime = $lang_def;
require $lang_data;

//** Link selected language
$lang_id   = $_SESSION['lang'];
$lang_user = "lang/" . $lang_id . ".php";

//** Check selected language
if (file_exists($lang_user)) {
    $lang_mime = $lang_id;
    include $lang_user;
} else {
    $stat = $lang['nolang'];
}

//** Check theme -- renders plain if missing
if (!file_exists("css/" . $css_def . ".css")) {
    $stat = $lang['css_missing'];
}

//** Login
if (isset($_POST['login'])) {

    //** Link name
    $name = htmlentities($_POST['name'], ENT_QUOTES, "UTF-8");

    //** Check name
    if ($name === "") {
        header('Location: #MISSING_NAME');
        exit;
    } else {

        //** Init name session -- mt_rand() to prevent dupes
        $_SESSION['name'] = $name . "_" . mt_rand();

        //** Build entry and update data file
        $text  = "            <div class=item_log>" .
                 date('Y-m-d H:i:s') . " " . $_SESSION['name'] .
                 " " . $lang['chat_enter'] . "</div>\n";

        if (file_exists($chat_data)) {
            $text .= file_get_contents($chat_data);
        }

        $stat = "";
        file_put_contents($chat_data, $text);
        header('Location: #LOGIN');
        exit;
    }
}

//** Save data file
if (isset($_POST['save'])) {
    header('Content-type: text/html');
    header(
        'Content-Disposition: attachment; ' .
        'filename="' . str_replace('log/', '', $chat_data) . '"'
    );

    readfile($chat_data);
    exit;
}

//** Logout
if (isset($_POST['quit'])) {

    //** Update data file and clear session
    $text  = "            <div class=item_log>" .
             date('Y-m-d H:i:s') . " " . $_SESSION['name'] .
             " " . $lang['chat_leave'] . "</div>\n";
    $text .= file_get_contents($chat_data);

    file_put_contents($chat_data, $text);

    unset($_SESSION['name']);
    header('Location: #LOGOUT');
    exit;
}

//** Manual update
if (isset($_POST['push'])) {
    header('Location: #PUSH');
    exit;
}

//** Post new entry
if (isset($_POST['post'])) {
    $name = htmlentities($_POST['name'], ENT_QUOTES, "UTF-8");
    $text = htmlentities($_POST['text'], ENT_QUOTES, "UTF-8");

    //** Check empty text
    if (!empty($text)) {

        //** Check emoji conversion
        if ($emo_auto === 1) {

            //** Link primary array
            $emo_open = fopen($emo_conf, 'r');

            //** Parse config
            while (!feof($emo_open)) {
                $emo_line   = fgets($emo_open);
                $emo_line   = trim($emo_line);
                $emo_parr[] = $emo_line;
            }

            fclose($emo_open);

            //** Link secondary array
            $emo_sarr = array();

            //** Parse primary array and split values
            foreach ($emo_parr as $emo_code) {
                $emo_line   = explode('|', $emo_code);
                $emo_sarr[] = $emo_line;
                $emo_calt   = $emo_line[0];
                $emo_ckey   = $emo_line[1];

                //** Convert emoji
                if (strpos($text, $emo_calt) !== false) {
                    $text = trim(
                        str_replace(
                            $emo_calt, "<span class=emo>" .
                            $emo_ckey ."</span>", $text
                        )
                    );
                }
            }

            unset($emo_code);
        }

        //** Build entry and update data file
        $text  = "            <div class=item " . 'id="pid' .
                 date('_Y-m-d_H-i-s_') . $_SESSION['name'] . '">' .
                 "\n                <div class=item_head>" .
                 "<div class=item_date>" .
                 date('Y-m-d H:i:s') . "</div> " .
                 "<div class=item_name>" .
                 $_SESSION['name'] . "</div>" .
                 "</div>\n" .
                 "                <div class=item_text>$text</div>\n" .
                 "            </div>\n";

        $text .= file_get_contents($chat_data);

        file_put_contents($chat_data, $text);
        header('Location: #POST');
        exit;
    } else {
        header('Location: #EMPTY_POST');
        exit;
    }
}

//** Link selected theme
if (isset($_POST['css_apply'])) {
    $css_conf = htmlentities($_POST['css_list'], ENT_QUOTES, "UTF-8");

    if ($css_conf !== "") {
        $_SESSION['theme'] = $css_conf;
    }
}

//** Check theme session and apply stylesheet
if (isset($_SESSION['theme'])) {
    $css_sel = $_SESSION['theme'];
} else {
    $css_sel           = $css_def;
    $_SESSION['theme'] = $css_sel;
}

//** Try to prevent caching
header('Expires: on, 01 Jan 1970 00:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

//** Header
echo "<!DOCTYPE html>\n" .
     '<html lang="' . $lang_mime . '">' . "\n" .
     "    <head>\n" .
     "        <title>" . $title . "</title>\n" .
     '        <meta charset="UTF-8"/>' . "\n" .
     '        <meta name=language content="' . $lang_mime .
     '"/>' . "\n" .
     '        <meta name=description content="PHP Atom Chat free ' .
     'PHP chat script. No database required."/>' . "\n" .
     '        <meta name=keywords ' .
     'content="PHP Atom Chat,free PHP chat scripts"/>' . "\n" .
     '        <meta name=robots content="noodp, noydir"/>' . "\n" .
     '        <meta name=viewport content="width=device-width, ' .
     'height=device-height, initial-scale=1"/>' . "\n" .
     '        <link rel=icon href="favicon.ico"/>' . "\n" .
     '        <link rel=stylesheet href="css/' . $css_sel .
     '.css"/>' . "\n" .
     "    </head>\n" .
     "    <body>\n" .
     "        <header>\n" .
     "            <h1>$image $title</h1>\n" .
     "        </header>\n";

//** List languages
if (isset($_POST['lang'])) {
    echo "        <article>\n" .
         "            <h2>" . $lang['lang_header'] . "</h2>\n" .
         '            <form action="#CHAT" method=POST ' .
         'accept-charset="UTF-8">' . "\n" .
         "                <div>\n";

    include $lang_conf;

    echo "                </div>\n" .
         "            </form>\n" .
         "        </article>\n";
}

//** List themes
if (isset($_POST['css_sel'])) {
    $css_line = file($css_conf);

    echo "        <article>\n" .
         "            <h2>" . $lang['css_header'] . "</h2>\n" .
         '            <form action="#CHAT" method=POST ' .
         'accept-charset="UTF-8">' . "\n" .
         "                <div>\n" .
         "                    <select name=css_list>\n";

    //** Parse list and print items
    foreach ($css_line as $css_item) {
        $css_item = trim($css_item);

        echo "                        <option " .
             'value="' . $css_item . '" ' .
             'title="' . $lang['css_select'] . ' ' .
             ucwords($css_item) . '">' . ucwords($css_item);

        //** Flag current theme
        if (isset($_SESSION['theme'])
            && $css_item === $_SESSION['theme']
        ) {
            echo " [x]";
        }

        echo "</option>\n";
    }

    unset($css_item);

    //** Function buttons
    echo "                    </select>\n" .
         "                    <input type=submit " .
         'name=css_apply value="' . $lang['apply'] . '" ' .
         'title="' . $lang['apply_title'] . '"/>' . "\n" .
         "                    <input type=submit " .
         'name=css_close value="' . $lang['close'] . '" ' .
         'title="' . $lang['close_title'] . '"/>' . "\n" .
         "                </div>\n" .
         "            </form>\n" .
         "        </article>\n";
}

//** List emoji table
if (isset($_POST['emo_codes'])) {

    //** Link primary array and config
    $emo_parr = array();
    $emo_open = fopen($emo_conf, 'r');

    //** Parse list
    while (!feof($emo_open)) {
        $emo_line   = fgets($emo_open);
        $emo_line   = trim($emo_line);
        $emo_parr[] = $emo_line;
    }

    fclose($emo_open);

    echo "        <article>\n" .
         "            <h2>" . $lang['emo_header'] ."</h2>\n" .
         '            <form action="#CHAT" method=POST ' .
         'accept-charset="UTF-8">' . "\n" .
         "                <pre id=emo>\n";

    //** Print list
    foreach ($emo_parr as $emo_code) {
        $emo_line   = explode('|', $emo_code);
        $emo_sarr[] = $emo_line;
        $emo_calt   = $emo_line[0];
        $emo_ckey   = $emo_line[1];

        echo "$emo_calt <span class=emo>$emo_ckey</span>\n";
    }

    unset($emo_code);

    echo "                </pre>\n" .
         "                <div>\n" .
         "                    <input type=submit " .
         'name=emo_close value="' . $lang['close'] . '" ' .
         'title="' . $lang['close_title'] . '"/>' . "\n" .
         "                </div>\n" .
         "            </form>\n" .
         "        </article>\n";
}

//** Check name session
if (isset($_SESSION['name']) && !empty($_SESSION['name'])) {
    echo "        <div id=push>\n";

    //** Check existing data file
    if (file_exists($chat_data)) {
        include $chat_data;
    } else {
        $stat = $lang['first'];
    }

    //** Navigation
    echo "        </div>\n" .
         "        <nav>\n" .
         '            <form action="#CHAT" method=POST ' .
         'accept-charset="UTF-8">' . "\n" .
         "                <div id=char>" . $lang['text'] . " " .
         "<small>(" . $max_char . " " . $lang['characters'] .
         ")</small></div>\n" .

         //** Text
         "                <textarea name=text id=text " .
         "rows=3 cols=40 maxlength=$max_char " .
         'title="' . $lang['text_title'] . '"></textarea>' . "\n" .
         "                <div>\n" .

         //** Name -- hidden
         "                    <input type=hidden " .
         'name=name value="' . $_SESSION['name'] . '"/>' . "\n" .

         //** Quit
         "                    <input type=submit " .
         'name=quit value="' . $lang['quit'] . '" ' .
         'title="' . $lang['quit_title'] . '"/>' . "\n" .

         //** Language
         "                    <input type=submit " .
         'name=lang value="' . $lang['lang'] . '" ' .
         'title="' . $lang['lang_title'] . '"/>' . "\n";

    //** Theme
    if ($css_usr === 1) {
        echo '                    <input type=submit ' .
             'name=css_sel value="' . $lang['theme'] . '" ' .
             'title="' . $lang['theme_title'] . '"/>' . "\n";
    }

    //** Emoji
    if ($emo_auto === 1) {
        echo "                    <input type=submit " .
             'name=emo_codes value="' . $lang['emo'] . '" ' .
             'title="' . $lang['emo_title'] . '"/>' . "\n";
    }

    //** Save
    echo "                    <input type=submit " .
         'name=save value="' . $lang['save'] . '" ' .
         'title="' . $lang['save_title'] . '"/>' . "\n" .

         //** Push
         "                    <input type=submit " .
         'name=push value="' . $lang['push'] . '" ' .
         'title="' . $lang['push_title'] . '"/>' . "\n" .

         //** Post
         "                    <input type=submit " .
         'name=post value="' . $lang['post'] . '" ' .
         'title="' . $lang['post_title'] . '"/>' . "\n" .

         "                </div>\n" .
         "            </form>\n" .
         "            <div id=stat>\n" .
         "                <div>$stat</div>\n" .
         "                <noscript>" . $lang['noscript'] .
         "</noscript>\n" .
         "            </div>\n";
} else {

    //** Load initial screen
    if (file_exists($init)) {
        echo "        <article>\n";
        include $init;
    }

    //** Login
    $stat = $lang['login_info'];

    echo "        </article>\n" .
         "        <nav>\n" .
         '            <form action="#LOGIN" method=POST ' .
         'accept-charset="UTF-8">' . "\n" .
         "                <div>\n" .
         "                    <label for=name>" .
         $lang['name'] . "</label>\n" .
         '                    <input name=name id=name ' .
         'maxlength=16 ' .
         'title="' . $lang['name_title'] . '"/>' . "\n" .
         '                    <input type=submit name=login ' .
         'value="' . $lang['login'] . '" ' .
         'title="' . $lang['login_title'] . '"/>' . "\n" .
         "                </div>\n" .
         "            </form>\n" .
         "            <div id=stat>\n" .
         "                <div>$stat</div>\n" .
         "                <noscript>" .
         $lang['noscript'] . "</noscript>\n" .
         "            </div>\n";
}

//** Footer
echo "            <p id=by>" .
     '<a href="https://github.com/phhpro/atomchat" ' .
     'title="' . $lang['get'] . '">' . $lang['by'] .
     " PHP Atom Chat v$make</a></p>\n" .
     "        </nav>\n" .
     '        <script src="chat.js"></script>' . "\n" .
     "    </body>\n" .
     "</html>\n";
