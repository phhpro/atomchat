<?php
/**
 * PHP Version 5 and above
 *
 * Main script
 *
 * @category  PHP_Chat
 * @package   PHP_Atomchat
 * @author    P H Claus <phhpro@gmail.com>
 * @copyright 2015 - 2019 P H Claus
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


// Config
if (is_file('config.php')) {
    include './config.php';
} else {
    echo "Missing configuration!";
    exit;
}

//** Version
$make = "20190214";

//** Protocol and URL
$prot = "";

if (isset($_SERVER['HTTPS']) && "on" === $_SERVER['HTTPS']) {
    $prot = "s";
}

$host = "http$prot://" . $_SERVER['HTTP_HOST'] . "/$fold/";

//** Log
if ($log_mode === 0) {
    $log_name = "atomchat-log_" . date('Y-m-d');
} else {
    $log_name = "atomchat-log";
}

$data = "log/" . $log_name . ".html";

if (is_file($data)) {

    if (filesize($data) > $log_size) {
        unlink($data);
    }
}

//** Initial welcome screen and status
$init = "welcome.php";
$stat = "";

/**
 * Function b64enc()
 *
 * @param string $b64_src source
 *
 * @return string data
 */
function b64enc($b64_src)
{
    $b64_src = $b64_src;
    $b64_ext = pathinfo($b64_src, PATHINFO_EXTENSION);
    $b64_get = file_get_contents($b64_src);
    $b64_str = "data:image/" . $b64_ext .
               ";base64," . base64_encode($b64_get);
    return chunk_split($b64_str);
}

//** Logo
if ($logo_i !== "") {
    $logo_i = "<img src=\"" . b64enc($logo_i) . "\" " .
              "width=$logo_w height=$logo_h alt=\"\"/> ";
} else {
    $logo_i = "";
}

if ($logo_t === 1) {
    $logo_t = $page;
} else {
    $logo_t = "";
}

$logo = $logo_i . $logo_t;

//** Emoji
$emo_conf = "emoji.txt";
$emo_parr = array();
$emo_sarr = array();
$emo_code = "";

//** Session
if (get_cfg_var('session.use_strict_mode') !== '1') {
    ini_set('session.use_strict_mode', '1');
}

session_start();
$_SESSION['test'] = 1;

if ($_SESSION['test'] !== 1) {
    echo "<p>Missing session cookie!</p>\n" .
         "<p>Please edit your browser's cookie " .
         "settings and then try again.</p>\n";
    exit;
} else {
    unset($_SESSION['test']);
}

//** Language
$lang_mime = $lang_def;

if (isset($_POST['lang_apply'])) {
    $_SESSION['lang']
        = htmlentities($_POST['lang_id'], ENT_QUOTES, "UTF-8");
}

/**
 * Try language auto-detect
 *
 * Applies to internal strings only to render script output in
 * user language. Global page language MIME is left intact, e.g.
 * if page MIME is set to "de" (German), and user selects "fr"
 * (French), the page's language MIME is still declared as "de"
 * whereas all script output is rendered "fr".
 */
if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $lang_sub = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    $lang_sup = [
        "ar",
        "de",
        "en",
        "es",
        "fr",
        "th",
        "zh"
    ];
    $lang_sub = in_array($lang_sub, $lang_sup) ? $lang_sub : "en";
    $lang_def = $lang_sub;
}

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = $lang_def;
}

$lang_id   = $_SESSION['lang'];
$lang_data = "./lang/$lang_id.php";


//** Folders
if (!is_dir('log')) {

    if (mkdir('log') === false) {
        echo "Failed to create log folder!";
        exit;
    }
}

if ($up === 1) {

    if (!is_dir($up_fold)) {

        if (mkdir($up_fold) === false) {
            echo "Failed to create upload folder!";
            exit;
        }
    }
}

if (!is_dir('lang')) {
    echo "Missing language folder!";
    exit;
}

//** Files
if (is_file($lang_data) || $emo === 1) {

    if (is_file($lang_data)) {
        $file_data = $lang_data;
        $file_text = "language file";
    }

    if ($emo === 1) {
        $file_data = $emo_conf;
        $file_text = "emoji configuration";
    }

    $file_trim = file_get_contents($file_data);

    if (filesize($file_data) <16 && trim($file_trim) === false) {
        echo "Invalid $file_text!";
        exit;
    }
} else {
    echo "Missing $file_text!";
    exit;
}

//** Link language
require $lang_data;
$lang_id   = $_SESSION['lang'];
$lang_user = "lang/$lang_id.php";

if (is_file($lang_user)) {
    include $lang_user;
} else {
    $stat = $lang['lang_miss'];
}

//** Theme
if (!is_file("css/$css_def.css")) {
    $stat = $lang['theme_miss'];
}

//** Login
if (isset($_POST['login'])) {
    $name = htmlentities($_POST['name'], ENT_QUOTES, "UTF-8");

    if ($name === "") {
        header("Location: $host#MISSING_NAME");
        exit;
    } else {
        $_SESSION['name'] = $name . "_" . mt_rand();
        $text = "            <div class=item_log>$date " .
                $_SESSION['name'] . " " . $lang['chat_enter'] .
                "</div>\n";

        if (is_file($data)) {
            $text .= file_get_contents($data);
        }

        $stat = "";
        file_put_contents($data, $text);
        header("Location: $host#LOGIN");
        exit;
    }
}

//** Save log -- download
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
    header("Location: $host#LOGOUT");
    exit;
}

//** Push -- manual update
if (isset($_POST['push'])) {
    header("Location: $host#PUSH");
    exit;
}

//** Initial upload state
$up_pass = 0;

//** Post
if (isset($_POST['post'])) {
    $name = htmlentities($_POST['name'], ENT_QUOTES, "UTF-8");
    $text = htmlentities($_POST['text'], ENT_QUOTES, "UTF-8");

    if ($text !== "") {
        $up_pass = 1;

        if ($emo === 1) {
            $emo_open = fopen($emo_conf, 'r');

            while (!feof($emo_open)) {
                $emo_line   = fgets($emo_open);
                $emo_line   = trim($emo_line);
                $emo_parr[] = $emo_line;
            }

            fclose($emo_open);

            foreach ($emo_parr as $emo_code) {
                $emo_line   = explode("|", $emo_code);
                $emo_sarr[] = $emo_line;
                $emo_calt   = $emo_line[0];
                $emo_ckey   = $emo_line[1];

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

    if ($up === 1) {
        $up_base = basename($_FILES['file']['name']);
        $up_file = $up_fold . "/" . $up_base;
        $up_type = strtolower(pathinfo($up_file, PATHINFO_EXTENSION));
        $_SESSION['ac_rand'] = mt_rand();
        $up_rand = $_SESSION['name'] . "-" .
                   $_SESSION['ac_rand'] . "." . $up_type;
        $up_save = str_replace($up_base, $up_rand, $up_file);
        $up_text = str_replace("$up_fold/", "", $up_save);
        $up_open = $host . $up_save;
        $up_fail = 1;

        if (!empty($_FILES['file']['name'])) {
            $up_name = $_FILES['file']['tmp_name'];
            $up_temp = getimagesize($_FILES['file']['tmp_name']);
            $up_size = $_FILES['file']['size'];

            if ($up_size > $up_max) {
                $stat    = $lang['up_exceed'];
                $up_fail = 0;
            }

            if (in_array($up_type, $up_is_b64)) {

                if ($up_temp !== false) {
                    $up_iw = $up_temp[0];
                    $up_ih = $up_temp[1];

                    if ($up_iw <= $up_tnw) {
                        $up_tnw = $up_iw;
                    }

                    if ($up_ih <= $up_tnh) {
                        $up_tnh = $up_ih;
                    }

                    $up_link = "<p><a href=\"$up_open\" " .
                               "title=\"" . $lang['up_open'] . "\">" .
                               "<img src=\"" . b64enc($up_name) .
                               "\" width=$up_tnw height=$up_tnh " .
                               "alt=\"\"/></a></p>";
                } else {
                    $stat    = $lang['up_noimg'];
                    $up_fail = 0;
                }
            } elseif (in_array($up_type, $up_is_arc)
                || in_array($up_type, $up_is_doc)
                || in_array($up_type, $up_is_img)
                || in_array($up_type, $up_is_snd)
                || in_array($up_type, $up_is_vid)
            ) {
                $up_link = "<p><a href=\"$up_open\" " .
                           "title=\"" . $lang['up_open'] . "\">" .
                           "$up_text</a></p>";
            } else {
                $stat    = $lang['up_notype'];
                $up_fail = 0;
            }

            if ($up_fail === 0) {
                $stat = $lang['up_fail'] . " " . $stat;
            } else {
                if (move_uploaded_file(
                    $_FILES['file']['tmp_name'], $up_file
                )
                ) {
                    copy($up_file, $up_save);
                    $up_pass = 1;
                } else {
                    $stat = $lang['up_nowrite'];
                }
            }
        }

        unlink($up_file);
    }

    if ($text !== "" && $up_link === "") {
        $post = $text;
    }

    if ($text !== "" && $up_link !== "") {
        $post = $text . $up_link;
    }

    if ($text === "" && $up_link !== "") {
        $post = $up_link;
    }

    if ($up_pass === 1) {
        $post = "            <div class=item id=\"pid" .
                date('_Ymd_His_') . $_SESSION['name'] . "\">\n" .
                "                <div class=item_head>\n" .
                "                    <span class=item_date>" .
                "$date</span> <span class=item_name>" .
                $_SESSION['name'] . "</span>\n" .
                "                </div>\n" .
                "                <pre class=item_text>$post</pre>\n" .
                "            </div>\n";
        $post .= file_get_contents($data);
        file_put_contents($data, $post);
        header("Location: $host#POST");
        exit;
    } else {
        header("Location: $host#INVALID_POST");
        exit;
    }
}

//** Link theme
if (isset($_POST['css_apply'])) {
    $css_id = htmlentities($_POST['css_id'], ENT_QUOTES, "UTF-8");

    if ($css_id !== "") {
        $_SESSION['theme'] = $css_id;
    }
}

if (isset($_SESSION['theme'])) {
    $css_sel = $_SESSION['theme'];
} else {
    $css_sel           = $css_def;
    $_SESSION['theme'] = $css_sel;
}

//** Headers
header('Expires: on, 01 Jan 1970 00:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header_remove('X-Powered-By');

//** Begin mark-up
echo "<!DOCTYPE html>\n" .
     "<html lang=\"$lang_mime\">\n" .
     "    <head>\n" .
     "        <title>$page</title>\n" .
     "        <meta charset=\"UTF-8\"/>\n" .
     "        <meta name=language content=\"$lang_mime\"/>\n" .
     "        <meta name=description " .
     "content=\"$meta_des - PHP Atomchat free PHP chat scripts\"/>\n" .
     "        <meta name=keywords " .
     "content=\"$meta_key,PHP Atomchat,free PHP chat scripts\"/>\n" .
     "        <meta name=robots content=\"noodp, noydir\"/>\n" .
     "        <meta name=viewport content=\"width=device-width, " .
     "height=device-height, initial-scale=1\"/>\n" .
     "        <link rel=icon href=\"favicon.png\" " .
     "type=\"image/png\"/>\n" .
     "        <link rel=stylesheet href=\"css/$css_sel.css\"/>\n" .
     "    </head>\n" .
     "    <body>\n" .
     "        <header>\n" .
     "            <h1>$logo</h1>\n" .
     "        </header>\n";

//** Config
if (isset($_POST['conf'])) {
    echo "        <article>\n" .
         "            <h2>" . $lang['conf']. "</h2>\n" .
         "            <form action=\"$host#CHAT\" method=POST " .
         "accept-charset=\"UTF-8\">\n" .

         //** Language
         "                <div>\n" .
         "                    <p><strong>" .
         $lang['lang'] . "</strong></p>\n" .
         "                    <select name=lang_id " .
         "title=\"" . $lang['lang_title']. "\">\n";

    $lang_fold = "lang/";
    $lang_list = glob($lang_fold . "*.php");
    sort($lang_list);

    foreach ($lang_list as $lang_item) {
        $lang_file = file_get_contents($lang_item);
        $lang_line = file($lang_item);
        $lang_name = $lang_line[20];
        $lang_name = str_replace(
            "\$lang['__name__']    = \"", "", $lang_name
        );
        $lang_name = str_replace("\";\n", "", $lang_name);
        $lang_text = $lang_line[21];
        $lang_text = str_replace(
            "\$lang['__text__']    = \"", "", $lang_text
        );
        $lang_text = str_replace("\";\n", "", $lang_text);
        $lang_link = basename($lang_item);
        $lang_link = str_replace(".php", "", $lang_link);
        echo "                    <option value=\"$lang_link\" " .
             "title=\"$lang_text\">$lang_name</option>\n";
    }

    unset($lang_item);
    echo "                </select>\n" .
         "                    <input type=submit name=lang_apply " .
         "value=\"" . $lang['apply'] . "\" " .
         "title=\"" . $lang['apply_title'] . "\"/>\n" .
         "                </div>\n";

    //** Theme
    if ($css === 1) {
        echo "                <div>\n" .
             "                    <p><strong>" .
             $lang['theme'] . "</strong></p>\n" .
             "                    <select name=css_id " .
             "title=\"" . $lang['theme_title'] . "\">\n";

        $css_fold = "css/";
        $css_list = glob($css_fold . "*.css");
        sort($css_list);

        foreach ($css_list as $css_item) {
            $css_link = basename($css_item);
            $css_link = str_replace(".css", "", $css_link);
            echo "                        <option value=\"".
                 "$css_link\" title=\"" . $lang['theme_title'] . " " .
                 ucwords($css_link) . "\">" . ucwords($css_link);

            if ($css_link === $_SESSION['theme']) {
                echo " [x]";
            }

            echo "</option>\n";
        }

        unset($css_item);
        echo "                    </select>\n" .
             "                    <input type=submit name=css_apply " .
             "value=\"" . $lang['apply'] . "\" " .
             "title=\"" . $lang['apply_title'] . "\"/>\n" .
             "                </div>\n";
    }

    //** Emoji
    if ($emo === 1) {
        $emo_open = fopen($emo_conf, 'r');

        while (!feof($emo_open)) {
            $emo_line   = fgets($emo_open);
            $emo_line   = trim($emo_line);
            $emo_parr[] = $emo_line;
        }

        fclose($emo_open);
        echo "                <p><strong>" .
             $lang['emo'] . "</strong></p>\n" .
             "                <pre class=emo>\n";

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
        echo "                <h2>" . $lang['up'] . "</h2>\n" .
             "                <p>" .
             $lang['up_max'] . " $up_max</p>\n" .
             "                <p><strong>" .
             $lang['up_allow'] . "</strong></p>\n" .



        //** Image, Base64
             "                <ul>\n" .
             "                    <li><strong>" .
             $lang['up_b64'] . "</strong>\n" .
             "                        <ul>\n";

        foreach ($up_is_b64 as $up_b64) {
            echo "                            <li>$up_b64</li>\n";
        }

        unset($up_doc);
        echo "                        </ul>\n" .
             "                    </li>\n" .
             "                </ul>\n" .

        //** Image, other
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

        //** Audio
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

    echo "                <div id=close>\n" .
         "                    <input type=submit " .
         "value=\"" . $lang['close'] . "\" " .
         "title=\"" . $lang['close_title'] . "\"/>\n" .
         "                </div>\n" .
         "            </form>\n" .
         "        </article>\n";
}

//** Check name session
if (isset($_SESSION['name']) && !empty($_SESSION['name'])) {
    echo "        <div id=push>\n";

    if (is_file($data)) {
        include $data;
    } else {
        $stat = $lang['first'];
    }

    //** Navigation
    echo "        </div>\n" .
         "        <nav>\n" .
         "            <form action=\"$host#CHAT\" name=chat " .
         "method=POST accept-charset=\"UTF-8\" " .
         "enctype=\"multipart/form-data\">\n" .
         "                <div>" . $lang['text'] . " " .
         "                    <input disabled id=char " .
         "size=4 value=\"$char\"/>\n" .
         "                </div>\n" .

         //** Text
         "                <textarea name=text id=text " .
         "rows=3 cols=40 maxlength=$char " .
         "title=\"" . $lang['text_title'] . "\" " .
         "onKeyPress=chars(this.form); " .
         "onKeyDown=chars(this.form); ".
         "onKeyUp=chars(this.form);></textarea>\n" .
         "                <div>\n" .

         //** Name -- hidden session token
         "                    <input type=hidden name=name " .
         "value=\"" . $_SESSION['name'] . "\"/>\n" .

         //** Quit
         "                    <input type=submit name=quit " .
         "value=\"&#x23F8; " . $lang['quit'] . "\" " .
         "title=\"" . $lang['quit_title'] . "\"/>\n" .

         //** Conf
         "                    <input type=submit name=conf " .
         "value=\"&#x1F4F6; " . $lang['conf'] . "\" " .
         "title=\"" . $lang['conf_title'] . "\"/>\n" .

         //** Save
         "                    <input type=submit name=save " .
         "value=\"&#x1F53D; " . $lang['save'] . "\" " .
         "title=\"" . $lang['save_title'] . "\"/>\n" .

         //** Push
         "                    <input type=submit name=push " .
         "value=\"&#x1F501; " . $lang['push'] . "\" " .
         "title=\"" . $lang['push_title'] . "\"/>\n" .

         //** Post
         "                    <input type=submit name=post " .
         "value=\"&#x1F53C; " . $lang['post'] . "\" " .
         "title=\"" . $lang['post_title'] . "\"/>\n" .
         "                </div>\n";

    //** Upload
    if ($up === 1) {
        echo "                <div>\n" .
             "                    <input type=file name=file " .
             "title=\"" . $lang['up_select'] . "\"/>\n" .
             "                    <div><small>" .
             $lang['up_max'] . " $up_max</small></div>\n" .
             "                </div>\n";
    }

    //** Log status
    if (is_file($data)) {
        echo "                <div><small>" . $lang['log_reset'] .
             " " . filesize($data) . " / $log_size </small></div>\n";
    }

    echo "            </form>\n" .
         "            <div id=stat>\n" .
         "                <div>$stat</div>\n" .
         "                <noscript>" .
         $lang['noscript'] . "</noscript>\n" .
         "            </div>\n";
} else {

    if (is_file($init)) {
        echo "        <article>\n";
        include "./$init";
    }

    //** Login
    $stat = $lang['name_info'];
    echo "        </article>\n" .
         "        <nav>\n" .
         "            <form action=\"$host#LOGIN\" method=POST " .
         "accept-charset=\"UTF-8\">\n" .
         "                <div>\n" .
         "                    <label for=name>" .
         $lang['name'] . "</label>\n" .
         "                    <input name=name id=name maxlength=16 " .
         "title=\"" . $lang['name_title'] . "\"/>\n" .
         "                    <input type=submit name=login " .
         "value=\"" . $lang['login'] . "\" " .
         "title=\"" . $lang['login_title'] . "\"/>\n" .
         "                </div>\n" .
         "            </form>\n" .
         "            <div id=stat>\n" .
         "                <div>$stat</div>\n" .
         "                <noscript>" .
         $lang['noscript'] . "</noscript>\n" .
         "            </div>\n";
}

//** End mark-up
echo "            <p id=by><a href=\" " .
     "https://github.com/phhpro/atomchat\" title=\"" . $lang['get'] .
     "\">" . $lang['by'] . " PHP Atomchat v$make</a></p>\n" .
     "        </nav>\n" .
     "        <script src=\"chat.js\"></script>\n" .
     "    </body>\n" .
     "</html>\n";
