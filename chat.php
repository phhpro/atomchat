<?php
/**
 * PHP Version 5 and above
 *
 * Main script and configuration
 *
 * @category  PHP_Chat
 * @package   PHP_Atomchat
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


// Load config
if (file_exists('config.php')) {
    include './config.php';
} else {
    echo "Missing configuration!";
    exit;
}

//** Script version
$make = 20180427;

//** Link logo
if ($logo !== "") {
    $logo = '<img src="' . $logo . '" width=16 height=16 alt=""/> ';
}

//** Init protocol
$prot = "";

//** Link protocol
if (isset($_SERVER['HTTPS']) && "on" === $_SERVER['HTTPS']) {
    $prot = "s";
}

//** Build URL
$host = "http" . $prot . "://" .
        $_SERVER['HTTP_HOST'] . "/" . $fold . "/";

//** Logfile, initial screen, and status
$data = "log/" . date('Y-m-d') . ".html";
$init = "init.php";
$stat = "";

//** Link emoji config, arrays, and code
$emo_conf = "emoji.txt";
$emo_parr = array();
$emo_sarr = array();
$emo_code = "";

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
if (isset($_POST['lang_apply'])) {
    $_SESSION['lang']
        = htmlentities($_POST['lang_id'], ENT_QUOTES, "UTF-8");
}

//** Fallback default language
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = $lang_def;
}

//** Link language ID and data
$lang_id   = $_SESSION['lang'];
$lang_data = "./lang/" . $lang_id . ".php";

//** Check log folder
if (!is_dir('log')) {

    if (mkdir('log') === false) {
        echo "Cannot write log folder!";
        exit;
    }
}

//** Check upload folder
if ($up === 1) {

    if (!is_dir($up_fold)) {

        if (mkdir($up_fold) === false) {
            echo "Cannot write upload folder!";
            exit;
        }
    }
}

//** Check language folder
if (!is_dir('lang')) {
    echo "Missing language folder!";
    exit;
}

//** Check if file exists and is valid
if (file_exists($lang_data) || $emo === 1) {

    if (file_exists($lang_data)) {
        $up_file_data = $lang_data;
        $up_file_text = "language file";
    }

    if ($emo === 1) {
        $up_file_data = $emo_conf;
        $up_file_text = "emoji configuration";
    }

    $up_file_trim = file_get_contents($up_file_data);

    //** True if file contains only BOM or empty lines => fail
    if (filesize($up_file_data) <16 && trim($up_file_trim) === false) {
        echo "Invalid $up_file_text!";
        exit;
    }
} else {
    echo "Missing $up_file_text!";
    exit;
}

//** Link default language and config
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
    $stat = $lang['lang_miss'];
}

//** Check theme -- renders plain if missing
if (!file_exists("css/" . $css_def . ".css")) {
    $stat = $lang['theme_miss'];
}

//** Login
if (isset($_POST['login'])) {
    $name = htmlentities($_POST['name'], ENT_QUOTES, "UTF-8");

    if ($name === "") {
        header('Location: #MISSING_NAME');
        exit;
    } else {

        //** Init name session -- mt_rand() to prevent dupes
        $_SESSION['name'] = $name . "_" . mt_rand();

        $text = "            <div class=item_log>$date " .
                $_SESSION['name'] . " " . $lang['chat_enter'] .
                "</div>\n";

        if (file_exists($data)) {
            $text .= file_get_contents($data);
        }

        $stat = "";
        file_put_contents($data, $text);
        header('Location: #LOGIN');
        exit;
    }
}

//** Save log
if (isset($_POST['save'])) {
    header('Content-type: text/html');
    header(
        'Content-Disposition: attachment; ' .
        'filename="' . str_replace('log/', '', $data) . '"'
    );

    readfile($data);
    exit;
}

//** Logout
if (isset($_POST['quit'])) {
    $text  = "            <div class=item_log>$date " .
             $_SESSION['name'] . " " . $lang['chat_leave'] .
             "</div>\n";
    $text .= file_get_contents($data);
    file_put_contents($data, $text);
    unset($_SESSION['name']);
    header('Location: #LOGOUT');
    exit;
}

//** Manual update
if (isset($_POST['push'])) {
    header('Location: #PUSH');
    exit;
}

//** Check upload
if ($up === 1) {

    //** Get size, trim name, and link file
    $up_size = $_FILES['file']['size'];
    $up_base = basename($_FILES['file']['name']);
    $up_file = $up_fold . "/" . $up_base;

    //** Link type and URL, and init error status
    $up_type = strtolower(pathinfo($up_file, PATHINFO_EXTENSION));
    $up_open = $host . $up_file;
    $up_fail = 1;
}

//** Initial form state
$up_pass = 0;

//** Post entry
if (isset($_POST['post'])) {
    $name = htmlentities($_POST['name'], ENT_QUOTES, "UTF-8");
    $text = htmlentities($_POST['text'], ENT_QUOTES, "UTF-8");

    //** Check text
    if ($text !== "") {
        $up_pass = 1;

        //** Check emoji conversion
        if ($emo === 1) {

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
                $emo_line   = explode("|", $emo_code);
                $emo_sarr[] = $emo_line;
                $emo_calt   = $emo_line[0];
                $emo_ckey   = $emo_line[1];

                //** Convert emoji
                if (stripos($text, $emo_calt) !== false) {
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
    }

    //** Check upload
    if ($up === 1) {

        //** Check selection
        if (!empty($_FILES['file']['name'])) {

            //** Link MIME type -- image only
            $up_mime = getimagesize($_FILES['file']['tmp_name']);

            //** Check if file exists
            if (file_exists($up_file)) {
                $stat    = $lang['up_exist'];
                $up_fail = 0;
            }

            //** Check size
            if ($up_size >$up_max) {
                $stat    = $lang['up_exceed'];
                $up_fail = 0;
            }

            //** Check image
            if (in_array($up_type, $up_is_img)) {

                //** Check MIME
                if ($up_mime !== false) {

                    //** Build entry
                    $up_link = $lang['up'] . ': <a href="' . $up_open .
                             '" title="' . $lang['up_open'] . '">' .
                             "$up_base ($up_size)</a><br/>" .
                             '<a href="' . $up_open . '" title="' .
                             $lang['up_open'] . '"><img src="' .
                             $up_open . '" width=' . $up_tnw . ' ' .
                             'height=' . $up_tnh . ' alt=""/></a>';
                } else {
                    $stat    = $lang['up_noimg'];
                    $up_fail = 0;
                }
            } elseif (
                //** Check non-image and build entry
                in_array($up_type, $up_is_arc)
                || in_array($up_type, $up_is_doc)
                || in_array($up_type, $up_is_snd)
                || in_array($up_type, $up_is_vid)
            ) {
                $up_link = $lang['up'] . ': <a href="' . $up_open .
                           '" title="' . $lang['up_open'] . '">' .
                           "$up_base ($up_size)</a>";
            } else {
                $stat    = $lang['up_notype'];
                $up_fail = 0;
            }

            //** Update error status
            if ($up_fail === 0) {
                $stat = $lang['up_fail'] . " " . $stat;
            } else {

                //** Finalise upload
                if (
                    move_uploaded_file(
                        $_FILES['file']['tmp_name'], $up_file
                    )
                ) {
                    //** Link entry and clear temp file
                    unlink($up_base);
                    $up_pass = 1;
                } else {
                    $stat = $lang['up_nowrite'];
                }
            }
        }
    }

    //** Link reference
    if ($text !== "" && $up_link !== "") {
        $post = "$text<div class=up_link>$up_link</div>";
    }

    if ($text !== "" && $up_link === "") {
        $post = $text;
    }

    if ($text === "" && $up_link !== "") {
        $post = $up_link;
    }

    //** Build entry and update log
    if ($up_pass === 1) {
        $post = "            <div class=item " . 'id="pid' .
                date('_Ymd_His_') . $_SESSION['name'] . '">' . "\n" .
                "                <div class=item_head>" .
                "<div class=item_date>$date</div> " .
                "<div class=item_name>" . $_SESSION['name'] . "</div>" .
                "</div>\n" .
                "                <div class=item_text>$post</div>\n" .
                "            </div>\n";
        $post .= file_get_contents($data);
        file_put_contents($data, $post);
        header('Location: #POST');
        exit;
    } else {
        header('Location: #INVALID_POST');
        exit;
    }
}

//** Link selected theme
if (isset($_POST['css_apply'])) {
    $css_id = htmlentities($_POST['css_id'], ENT_QUOTES, "UTF-8");

    if ($css_id !== "") {
        $_SESSION['theme'] = $css_id;
    }
}

//** Check theme session and apply
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
     "        <title>" . $page . "</title>\n" .
     '        <meta charset="UTF-8"/>' . "\n" .
     "        <meta name=language " .
     'content="' . $lang_mime . '"/>' . "\n" .
     "        <meta name=description " .
     'content="PHP Atomchat free PHP chat script"/>' . "\n" .
     "        <meta name=keywords " .
     'content="PHP Atomchat,free PHP chat scripts"/>' . "\n" .
     '        <meta name=robots content="noodp, noydir"/>' . "\n" .
     '        <meta name=viewport content="width=device-width, ' .
     'height=device-height, initial-scale=1"/>' . "\n" .
     '        <link rel=icon type="image/png" ' .
     'href="favicon.png"/>' . "\n" .
     "        <link rel=stylesheet " .
     'href="css/' . $css_sel . '.css"/>' . "\n" .
     "    </head>\n" .
     "    <body>\n" .
     "        <header>\n" .
     "            <h1>$logo$page</h1>\n" .
     "        </header>\n";

//** Settings
if (isset($_POST['settings'])) {
    echo "        <article>\n" .
         "            <h2>" . $lang['set'] . "</h2>\n" .
         '            <form action="#CHAT" method=POST ' .
         'accept-charset="UTF-8">' . "\n" .

    //** Language
         "                <div>\n" .
         "                    <p><strong>" .
         $lang['lang'] . "</strong></p>\n" .
         "                    <select name=lang_id " .
         'title="' . $lang['lang_title']. '">' . "\n";

    //** Parse language folder
    $lang_fold = "lang/";
    $lang_list = glob($lang_fold . "*.php");
    sort($lang_list);

    foreach ($lang_list as $lang_item) {

        //** Link source
        $lang_file = file_get_contents($lang_item);
        $lang_line = file($lang_item);

        //** Trim name
        $lang_name = $lang_line[20];
        $lang_name = str_replace(
            "\$lang['__name__']    = \"", "", $lang_name
        );
        $lang_name = str_replace("\";\n", "", $lang_name);

        //** Trim text
        $lang_text = $lang_line[21];
        $lang_text = str_replace(
            "\$lang['__text__']    = \"", "", $lang_text
        );
        $lang_text = str_replace("\";\n", "", $lang_text);

        //** Trim link
        $lang_link = basename($lang_item);
        $lang_link = str_replace(".php", "", $lang_link);

        //** List item
        echo "                    <option " .
             'value="' . $lang_link . '" ' .
             'title="' . $lang_text . '">' .
             $lang_name . "</option>\n";
    }

    unset($lang_item);
    echo "                </select>\n" .
         "                    <input type=submit name=lang_apply " .
         'value="&#x2611; ' . $lang['apply'] . '" ' .
         'title="' . $lang['apply_title'] . '"/>' . "\n" .
         "                </div>\n";

    //** Theme
    if ($css_usr === 1) {
        echo "                <div>\n" .
             "                    <p><strong>" .
             $lang['theme'] . "</strong></p>\n" .
             "                    <select name=css_id " .
             'title="' . $lang['theme_title'] . '">' . "\n";

        //** Parse theme folder
        $css_fold = "css/";
        $css_list = glob($css_fold . "*.css");
        sort($css_list);

        foreach ($css_list as $css_item) {
            $css_link = basename($css_item);
            $css_link = str_replace(".css", "", $css_link);

            echo "                        <option " .
                 'value="' . $css_link . '" ' .
                 'title="' . $lang['theme_title'] . ' ' .
                 ucwords($css_link) . '">' . ucwords($css_link);

                //** Flag current
                if ($css_link === $_SESSION['theme']) {
                    echo " [x]";
                }

            echo "</option>\n";
        }

        unset($css_item);

        echo "                    </select>\n" .
             "                    <input type=submit name=css_apply " .
             'value="&#x2611; ' . $lang['apply'] . '" ' .
             'title="' . $lang['apply_title'] . '"/>' . "\n" .
             "                </div>\n";
    }

    //** Emoji
    if ($emo === 1) {

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

        echo "                <p><strong>" .
             $lang['emo'] . "</strong></p>\n" .
             "                <pre id=emo>\n";

        //** List item
        foreach ($emo_parr as $emo_code) {
 
            if ($emo_code !== "") { 
                $emo_line   = explode("|", $emo_code);
                $emo_sarr[] = $emo_line;
                $emo_calt   = $emo_line[0];
                $emo_ckey   = $emo_line[1];
                echo "$emo_calt <span class=emo>$emo_ckey</span>\n";
            }
        }

        unset($emo_code);
        echo "                </pre>\n";
    }

    //** Upload
    if ($up === 1) {

        //** Summary
        echo "                <h2>" . $lang['up'] . "</h2>\n" .
             "                <p>" .
             $lang['up_max'] . " $up_max.</p>\n" .
             "                <p><strong>" .
             $lang['up_allow'] . "</strong></p>\n" .

        //** Document
             "                <ul>\n" .
             "                    <li><strong>" .
             $lang['up_doc'] . "</strong>\n" .
             "                        <ul>\n";

        foreach ($up_is_doc as $up_doc) {
            echo "                            <li>$up_doc</li>\n";
        }

        unset($up_doc);
        echo "                        </ul>\n" .
             "                    </li>\n" .
             "                </ul>\n" .

        //** Image
             "                <ul>\n" .
             "                    <li><strong>" .
             $lang['up_img'] . "</strong>\n" .
             "                        <ul>\n";

        foreach ($up_is_img as $up_img) {
            echo "                            <li>$up_img</li>\n";
        }

        unset($up_img);
        echo "                        </ul>\n" .
             "                    </li>\n" .
             "                </ul>\n" .

        //** Sound
             "                <ul>\n" .
             "                    <li><strong>" .
             $lang['up_snd'] . "</strong>\n" .
             "                        <ul>\n";

        foreach ($up_is_snd as $up_snd) {
            echo "                            <li>$up_snd</li>\n";
        }

        unset($up_snd);
        echo "                        </ul>\n" .
             "                    </li>\n" .
             "                </ul>\n" .

        //** Video
             "                <ul>\n" .
             "                    <li><strong>" .
             $lang['up_vid'] . "</strong>\n" .
             "                        <ul>\n";

        foreach ($up_is_vid as $up_vid) {
            echo "                            <li>$up_vid</li>\n";
        }

        unset($up_vid);
        echo "                        </ul>\n" .
             "                    </li>\n" .
             "                </ul>\n" .

        //** Archive
             "                <ul>\n" .
             "                    <li><strong>" .
             $lang['up_arc'] . "</strong>\n" .
             "                        <ul>\n";

        foreach ($up_is_arc as $up_arc) {
            echo "                            <li>$up_arc</li>\n";
        }

        unset($up_arc);
        echo "                        </ul>\n" .
             "                    </li>\n" .
             "                </ul>\n";
    }

    //** Close settings
    echo "                <div id=close>\n" .
         "                    <input type=submit " .
         'value="&#x2612; ' . $lang['close'] . '" ' .
         'title="' . $lang['close_title'] . '"/>' . "\n" .
         "                </div>\n" .
         "            </form>\n" .
         "        </article>\n";
}

//** Check name session
if (isset($_SESSION['name']) && !empty($_SESSION['name'])) {
    echo "        <div id=push>\n";

    //** Check data
    if (file_exists($data)) {
        include $data;
    } else {
        $stat = $lang['first'];
    }

    //** Navigation
    echo "        </div>\n" .
         "        <nav>\n" .
         '            <form action="#CHAT" method=POST ' .
         'accept-charset="UTF-8" ' .
         'enctype="multipart/form-data">' . "\n" .
         "                <div>" . $lang['text'] . " " .
         "<small>($char " . $lang['characters'] . ")</small></div>\n" .

         //** Text
         "                <textarea name=text id=text " .
         "rows=3 cols=40 maxlength=$char " .
         'title="' . $lang['text_title'] . '"></textarea>' . "\n" .
         "                <div>\n" .

         //** Name -- hidden
         "                    <input type=hidden name=name " .
         'value="' . $_SESSION['name'] . '"/>' . "\n" .

         //** Quit
         "                    <input type=submit name=quit " .
         'value="&#x2612; ' . $lang['quit'] . '" ' .
         'title="' . $lang['quit_title'] . '"/>' . "\n" .

         //** Settings
         "                    <input type=submit name=settings " .
         'value="&#x2699; ' . $lang['set'] . '" ' .
         'title="' . $lang['set_title'] . '"/>' . "\n" .

         //** Save
         "                    <input type=submit name=save " .
         'value="&#x1F4BE; ' . $lang['save'] . '" ' .
         'title="' . $lang['save_title'] . '"/>' . "\n" .

         //** Push
         "                    <input type=submit name=push " .
         'value="&#x2610; ' . $lang['push'] . '" ' .
         'title="' . $lang['push_title'] . '"/>' . "\n" .

         //** Post
         "                    <input type=submit name=post " .
         'value="&#x2611; ' . $lang['post'] . '" ' .
         'title="' . $lang['post_title'] . '"/>' . "\n" .
         "                </div>\n";

    //** Upload
    if ($up === 1) {
        echo "                <div>\n" .
             "                    <input type=file name=file " .
             'title="' . $lang['up_select'] . '"/>' . "\n" .
             "                    <div><small>" . $lang['up_max'] .
             $up_max . ". " . $lang['up_info'] . "</small></div>\n" .
             "                </div>\n";
    }

    echo "            </form>\n" .

         //** Status
         "            <div id=stat>\n" .
         "                <div>$stat</div>\n" .
         "                <noscript>" .
         $lang['noscript'] . "</noscript>\n" .
         "            </div>\n";
} else {

    //** Initial screen
    if (file_exists($init)) {
        echo "        <article>\n";
        include "./$init";
    }

    //** Login
    $stat = $lang['name_info'];

    echo "        </article>\n" .
         "        <nav>\n" .
         '            <form action="#LOGIN" method=POST ' .
         'accept-charset="UTF-8">' . "\n" .
         "                <div>\n" .
         "                    <label for=name>" .
         $lang['name'] . "</label>\n" .
         "                    <input name=name id=name " .
         'maxlength=16 ' .
         'title="' . $lang['name_title'] . '"/>' . "\n" .
         "                    <input type=submit name=login " .
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
     " PHP Atomchat v$make</a></p>\n" .
     "        </nav>\n" .
     '        <script src="chat.js"></script>' . "\n" .
     "    </body>\n" .
     "</html>\n";
