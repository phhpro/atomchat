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


//** Version
$ver = "20190227";

//** Required to get around "Headers already sent" warning
ob_start();

/**
 ***********************************************************************
 *                                                             HEADERS *
 ***********************************************************************
 */

header('Expires: on, 01 Jan 1970 00:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache');
header('Pragma: no-cache');
header_remove('X-Powered-By');

/**
 ***********************************************************************
 *                                                        DEPENDENCIES *
 ***********************************************************************
 */

if (!is_file('conf.php')) {
    $exit = "Missing config!";
} else {
    $conf_trim = file_get_contents('conf.php');

    //** Invalid file can still pass if large enough!
    if (filesize('conf.php') < 16 && trim($conf_trim) === false) {
        $exit = "Invalid config!";
    } else {
        include './conf.php';

        if (!is_dir($lang_fold)) {
            $exit = "Missing language folder!";
        }

        if (!is_dir($log_fold) && mkdir($log_fold) === false) {
            $exit = "Cannot create log folder!";
        }

        if ($up === 1 && !is_dir($up_fold)
            && mkdir($up_fold) === false
        ) {
            $exit = "Cannot create upload folder!";
        }

        if ($emo === 1 && is_file($emo_conf)) {
            $emo_trim = file_get_contents($emo_conf);

            if (filesize($emo_conf) < 16 && trim($emo_trim) === false) {
                $exit = "Invalid emoji definition!";
            } else {
                $emo_parr = array();
                $emo_sarr = array();
                $emo_code = "";
            }
        } else {
            $exit = "Missing emoji definition!";
        }
    }
}

if (isset($exit) && !empty($exit)) {
    echo "$exit Script halted.";
    exit;
}

/**
 ***********************************************************************
 *                                                              BASICS *
 ***********************************************************************
 */
$su   = $su_pfx . $su_sfx;
$home = "home.php";
$stat = "";

/**
 ***********************************************************************
 *                                                           FUNCTIONS *
 ***********************************************************************
 */

if (get_cfg_var('session.use_strict_mode') !== 1) {
    ini_set('session.use_strict_mode', 1);
}

/**
 * Function sessionstat()
 *
 * @return bool session status
 */
function sessionstat()
{
    if (php_sapi_name() !== 'cli'
        && version_compare(phpversion(), '5.4.0', '>=')
    ) {
        return session_status()
            === PHP_SESSION_ACTIVE ? true : false;
    } else {
        return session_id() === '' ? false : true;
    }

    return false;
}

if (sessionstat() === false && !session_start()) {
    echo "Cannot create session! Script halted.";
    exit;
} else {
    session_start();
}

/**
 * Function b64enc()
 *
 * @param string $b64_src source
 *
 * @return string data block
 */
function b64enc($b64_src)
{
    $b64_ext = pathinfo($b64_src, PATHINFO_EXTENSION);
    $b64_get = file_get_contents($b64_src);
    $b64_str = "\"\ndata:image/" . $b64_ext .
               ";base64," . base64_encode($b64_get) . "\" ";
    return $b64_str;
}

//** Base64 types
$b64_type  = array(
    "gif",
    "jpeg",
    "jpg",
    "png"
);

/**
 * Function go()
 *
 * @param string $tag target
 *
 * @return string hashtag reference
 */
function go($tag)
{
    global $host;
    header("Location: $host#$tag");
    exit;
}

/**
 ***********************************************************************
 *                                                                 URL *
 ***********************************************************************
 */

if (isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS']) {
    $prot = "s";
} else {
    $prot = "";
}

$host = "http$prot://" . $_SERVER['HTTP_HOST'] . "/$fold/";

/**
 ***********************************************************************
 *                                                                 LOG *
 ***********************************************************************
 */

if ($log_mode === 0) {
    $log_name = $log_name . "_" . gmdate('Y-m-d');
}

$log_data = $log_fold . "/" . $log_name . ".html";

if ($log_auto === 1 && is_file($log_data)) {
    $log_stat = filesize($log_data);
    $log_temp = $log_size - $log_stat;
    $log_warn = $log_size / 100 * $log_warn;

    if ($log_temp <= $log_warn) {
        $log_stat = "<strong>$log_stat</strong>";
    }

    if ($log_temp <= 0) {
        unlink($log_data);
    }
}

/**
 ***********************************************************************
 *                                                                LOGO *
 ***********************************************************************
 */

if ($logo_i !== "") {
    $logo_i = "<img src=" . chunk_split(b64enc($logo_i), 68) .
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

/**
 ***********************************************************************
 *                                                            LANGUAGE *
 ***********************************************************************
 */

$lang_mime = $lang_def;

if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $lang_hal = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    $lang_usr = $lang_hal;
    $lang_php = glob($lang_fold . "/*.php");

    foreach ($lang_php as $lang_obj) {
        $lang_obj = str_replace($lang_fold . "/", "", $lang_obj);
        $lang_obj = str_replace(".php", "", $lang_obj);

        if (strpos($lang_obj, $lang_usr) === false) {
            continue;
        } else {
            $lang_def = $lang_usr;
        }
    }

    unset($lang_obj);
}

if (!isset($_SESSION['ac_lang'])) {
    $_SESSION['ac_lang'] = $lang_def;
}

if (isset($_POST['lang_apply'])) {
    $_SESSION['ac_lang']
        = htmlentities($_POST['lang_id'], ENT_QUOTES, 'UTF-8');
}

$lang_id   = $_SESSION['ac_lang'];
$lang_data = $lang_fold . "/" . $lang_id . ".php";

if (is_file($lang_data)) {
    $lang_trim = file_get_contents($lang_data);

    if (filesize($lang_data) < 16 && trim($lang_trim) === false) {
        $stat      = "Invalid language file: " . $lang_id;
        $lang_data = str_replace($lang_id, $lang_def, $lang_data);
    } else {
        include $lang_data;
    }
}

/**
 ***********************************************************************
 *                                                               LOGIN *
 ***********************************************************************
 */

if (isset($_POST['login'])) {
    $name = htmlentities($_POST['name'], ENT_QUOTES, 'UTF-8');

    if ($name === "") {
        go('MISSING_NAME');
    } else {

        if ($name === $su) {
            $_SESSION['ac_name'] = $su_pfx;
        } else {
            $_SESSION['ac_name']
                = $name . "_" . mt_rand($rn_min, $rn_max);
        }

        $text = "            <div class=item_log>$date " .
                $_SESSION['ac_name'] . " LOGIN</div>\n";

        if (is_file($log_data)) {
            $text .= file_get_contents($log_data);
        }

        $stat = "";
        file_put_contents($log_data, $text);
        go('LOGIN');
    }
}

/**
 ***********************************************************************
 *                                                              LOGOUT *
 ***********************************************************************
 */

if (isset($_POST['quit'])) {
    $text  = "            <div class=item_log>$date " .
             $_SESSION['ac_name'] . " LOGOUT</div>\n";
    $text .= file_get_contents($log_data);
    file_put_contents($log_data, $text);
    unset($_SESSION['ac_name']);
    go('LOGOUT');
}

/**
 ***********************************************************************
 *                                                               THEME *
 ***********************************************************************
 */

if (isset($_POST['css_apply'])) {
    $css_id = htmlentities($_POST['css_id'], ENT_QUOTES, 'UTF-8');

    if ($css_id !== "") {
        $_SESSION['ac_css'] = $css_id;
    }
}

if (isset($_SESSION['ac_css'])) {
    $css_sel = $_SESSION['ac_css'];
} else {
    $css_sel            = $css_def;
    $_SESSION['ac_css'] = $css_sel;
}

$css_file = $css_fold . "/" . $css_sel . ".css";

/**
 ***********************************************************************
 *                                                            SAVE LOG *
 ***********************************************************************
 */

if (isset($_POST['save'])) {
    header('Content-type: text/html');
    header(
        'Content-Disposition: attachment; filename="' .
        str_replace($log_fold . "/", "", $log_data) . '"'
    );
    readfile($log_data);
    exit;
}

/**
 ***********************************************************************
 *                                                           RESET LOG *
 ***********************************************************************
 */

if (isset($_POST['reset']) && is_file($log_data)) {
    unlink($log_data);
    go('RESET_LOG');
}

/**
 ***********************************************************************
 *                                                                POST *
 ***********************************************************************
 */

//** Initial upload state
$pass = 0;

if (isset($_POST['post'])) {
    $name = htmlentities($_POST['name'], ENT_QUOTES, 'UTF-8');
    $text = htmlentities($_POST['text'], ENT_QUOTES, 'UTF-8');

    if ($text !== "") {
        $text = wordwrap($text, 68);
        $pass = 1;

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
                            $emo_calt,
                            "<span class=emo>$emo_ckey</span>",
                            $text
                        )
                    );
                }
            }

            unset($emo_code);
        }
    }

    if ($up === 1) {

        if (!empty($_FILES['file']['name'])) {
            $up_base = basename($_FILES['file']['name']);
            $up_temp = $_FILES['file']['tmp_name'];
            $up_gimg = getimagesize($_FILES['file']['tmp_name']);
            $up_size = $_FILES['file']['size'];
            $up_file = $up_fold . "/" . $up_base;
            $up_type
                = strtolower(pathinfo($up_file, PATHINFO_EXTENSION));
            $up_rand = mt_rand() . "." . $up_type;
            $up_save = str_replace($up_base, $up_rand, $up_file);
            $up_open = $host . $up_save;

            if ($up_size > $up_max) {
                go('INVALID_FILESIZE');
            } else {

                if (move_uploaded_file(
                    $_FILES['file']['tmp_name'], $up_file
                ) && copy($up_file, $up_save)
                ) {
                    $pass = 1;
                } else {
                    go('WRITE_ERROR');
                }

                unlink($up_file);
            }

            if (in_array($up_type, $b64_type)) {

                if (empty($up_gimg)) {
                    go('INVALID_IMAGE');
                } else {
                    $up_ico = "th_" . basename($up_save);
                    copy($up_save, $up_ico);

                    if ($up_type === "jpeg" || $up_type === "jpg") {
                        $up_src = imagecreatefromjpeg($up_ico);
                    } elseif ($up_type === "png") {
                        $up_src = imagecreatefrompng($up_ico);
                    } else {
                        $up_src = imagecreatefromgif($up_ico);
                    }

                    $up_iw = imagesx($up_src);
                    $up_ih = imagesy($up_src);
                    $up_wh = min($up_tns / $up_iw, $up_tns / $up_ih);
                    $up_tw = ceil($up_wh * $up_iw);
                    $up_th = ceil($up_wh * $up_ih);
                    $up_tn = imagecreatetruecolor($up_tw, $up_th);

                    imagecopyresampled(
                        $up_tn, $up_src,
                        0, 0, 0, 0,
                        $up_tw, $up_th, $up_iw, $up_ih
                    );

                    if ($up_type === "jpeg" || $up_type === "jpg") {
                        imagejpeg($up_tn, $up_ico, 60);
                    } elseif ($up_type === "png") {
                        imagepng($up_ico);
                    } else {
                        imagegif($up_ico);
                    }

                    $up_link = "<br/><a href=\"$up_open\" " .
                               "title=\"" . $lang['up_open'] .
                               "\"><img src=" .
                               chunk_split(b64enc($up_ico), 68) .
                               "width=$up_tw height=$up_th " .
                               "alt=\"\"/></a>";

                    imagedestroy($up_src);
                    imagedestroy($up_tn);
                    unlink($up_ico);
                }
            } elseif (in_array($up_type, $up_is_arc)
                || in_array($up_type, $up_is_doc)
                || in_array($up_type, $up_is_img)
                || in_array($up_type, $up_is_snd)
                || in_array($up_type, $up_is_vid)
            ) {
                $up_link = "<a href=\"$up_open\" " .
                           "title=\"" . $lang['up_open'] . "\">" .
                           str_replace($up_fold . "/", "", $up_save) .
                           "</a>";
            } else {
                go('INVALID_FILETYPE');
            }
        }
    }

    if ($text !== "" && $up_link === "") {
        $post = $text;
    } elseif ($text !== "" && $up_link !== "") {
        $post = $text . " " . $up_link;
    } elseif ($text === "" && $up_link !== "") {
        $post = $up_link;
    } else {
        $pass = 0;
    }

    if ($pass === 1) {
        $post  = "<div class=item>\n" .
                 "    <div class=item_head>\n" .
                 "        <span class=item_date>$date</span> \n" .
                 "        <span class=item_name>" .
                 $_SESSION['ac_name'] . "</span>\n" .
                 "    </div>\n" .
                 "    <pre class=item_text>\n" .
                 "$post\n" .
                 "    </pre>\n" .
                 "</div>\n" .
                 "<hr/>\n";
        $post .= file_get_contents($log_data);
        file_put_contents($log_data, $post);
        go('POST');
    } else {
        go('EMPTY_POST');
    }
}

/**
 ***********************************************************************
 *                                                        BEGIN MARKUP *
 ***********************************************************************
 */

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
     "        <link rel=icon href=\"" . $host . "favicon.png\" " .
     "type=\"image/png\"/>\n" .
     "        <link rel=stylesheet href=\"$host$css_file\"/>\n" .
     "    </head>\n" .
     "    <body>\n" .
     "        <header>\n" .
     "            <h1>$logo</h1>\n" .
     "        </header>\n";

/**
 ***********************************************************************
 *                                                       CONFIG SCREEN *
 ***********************************************************************
 */

if (isset($_POST['conf'])) {
    echo "        <article id=conf>\n" .
         "            <h2>" . $lang['conf']. "</h2>\n" .
         "            <form action=\"$host#CHAT\" method=POST " .
         "accept-charset=\"UTF-8\">\n" .

         //** Language
         "                <div>\n" .
         "                    <p>\n" .
         "                        <label for=name_id>" .
         $lang['lang'] . "</label>\n" .
         "                        <select name=lang_id " .
         "title=\"" . $lang['lang_title']. "\">\n";

    $lang_list = glob($lang_fold . "/*.php");
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
        echo "                            <option " .
             "value=\"$lang_link\" lang=\"$lang_link\" " .
             "title=\"$lang_text\">$lang_name</option>\n";
    }

    unset($lang_item);
    echo "                        </select>\n" .
         "                        <input type=submit " .
         "name=lang_apply value=\"#\" " .
         "title=\"" . $lang['apply_title'] . "\"/>\n" .
         "                    </p>\n" .
         "                </div>\n";

    //** Theme
    if ($css === 1) {
        echo "                <div>\n" .
             "                    <p>\n" .
             "                        <label for=css_id>" .
             $lang['theme'] . "</label>\n" .
             "                        <select name=css_id " .
             "title=\"" . $lang['theme_title'] . "\">\n";

        $css_list = glob($css_fold . "/*.css");
        sort($css_list);

        foreach ($css_list as $css_item) {
            $css_link = basename($css_item);
            $css_link = str_replace(".css", "", $css_link);
            $css_text = str_replace(array("-", "_"), " ", $css_link);
            echo "                            <option value=\"".
                 "$css_link\" title=\"" . $lang['theme_title'] . " " .
                 ucwords($css_text) . "\">" . ucwords($css_text);

            if ($css_link === $_SESSION['ac_css']) {
                echo " [x]";
            }

            echo "</option>\n";
        }

        unset($css_item);
        echo "                        </select>\n" .
             "                        <input type=submit " .
             "name=css_apply value=\"#\" " .
             "title=\"" . $lang['apply_title'] . "\"/>\n" .
             "                    </p>\n" .
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

        //** Image, Base64
             "                <ul>\n" .
             "                    <li><strong>" .
             $lang['up_is_b64'] . "</strong>\n" .
             "                        <ul>\n";

        foreach ($b64_type as $up_b64) {
            echo "                            <li>$up_b64</li>\n";
        }

        unset($up_doc);
        echo "                        </ul>\n" .
             "                    </li>\n" .
             "                </ul>\n" .

        //** Image, other
             "                <ul>\n" .
             "                    <li><strong>" .
             $lang['up_is_img'] . "</strong>\n" .
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
             $lang['up_is_snd'] . "</strong>\n" .
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
             $lang['up_is_vid'] . "</strong>\n" .
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
             $lang['up_is_doc'] . "</strong>\n" .
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
             $lang['up_is_arc'] . "</strong>\n" .
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
         "value=\"x\" " . 
         "title=\"" . $lang['close_title'] . "\"/>\n" .
         "                </div>\n" .
         "            </form>\n" .
         "        </article>\n";
}

/**
 ***********************************************************************
 *                                                         CHAT SCREEN *
 ***********************************************************************
 */

if (isset($_SESSION['ac_name']) && !empty($_SESSION['ac_name'])) {
    echo "        <article id=push>\n";

    if (is_file($log_data)) {
        include $log_data;
    } else {
        $stat = $lang['first'];
    }

    //** Navigation
    echo "        </article>\n" .
         "        <nav>\n" .
         "            <form action=\"$host#CHAT\" name=chat " .
         "method=POST accept-charset=\"UTF-8\" " .
         "enctype=\"multipart/form-data\">\n" .
         "                <div>\n" .
         "                    " . $lang['text'] . " " .
         "<input disabled id=char size=4 value=\"$char\"/>\n" .
         "                </div>\n" .

         //** Text
         "                <textarea name=text id=text " .
         "rows=2 cols=40 maxlength=$char " .
         "title=\"" . $lang['text_title'] . "\" " .
         "onkeydown=\"chars(this.form);\" ".
         "onkeypress=\"chars(this.form);\" " .
         "onkeyup=\"chars(this.form);\"></textarea>\n" .
         "                <div>\n" .

         //** Name -- hidden session token
         "                    <input type=hidden name=name " .
         "value=\"" . $_SESSION['ac_name'] . "\"/>\n";

    //** Reset log -- super user only
    if ($_SESSION['ac_name'] === $su_pfx) {
        echo "                    <input type=submit name=reset " .
             "value=\"=\" " .
             "title=\"" . $lang['reset'] . "\"/>\n";
    }

         //** Quit
    echo "                    <input type=submit name=quit " .
         "value=\"x\" " .
         "title=\"" . $lang['quit_title'] . "\"/>\n" .

         //** Conf
         "                    <input type=submit name=conf " .
         "value=\"?\" " .
         "title=\"" . $lang['conf_title'] . "\"/>\n" .

         //** Save
         "                    <input type=submit name=save " .
         "value=\"v\" " .
         "title=\"" . $lang['save_title'] . "\"/>\n" .

         //** Post
         "                    <input type=submit name=post " .
         "value=\"#\" " .
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
    if (is_file($log_data) && $log_auto === 1) {
        echo "                <div><small>" . $lang['reset'] .
             " $log_stat / $log_size</small></div>\n";
    }

    //** Default status and JS helper
    echo "            </form>\n" .
         "            <div id=stat>\n" .
         "                <div>$stat</div>\n" .
         "                <script>\n" .
         "                var char = $char;\n" .
         "                var rate = $rate;\n" .
         "                var data = \"$log_data\";\n" .
         "                </script>\n" .
         "                <script src=\"chat.js\"></script>\n" .
         "                <noscript>" .
         $lang['noscript'] . "</noscript>\n" .
         "            </div>\n";
} else {
    /**
    ********************************************************************
    *                                                     LOGIN SCREEN *
    ********************************************************************
    */

    echo "        <article>\n";

    if (is_file($home)) {
        include "./$home";
    }

    $stat = $lang['name_info'];
    echo "        </article>\n" .
         "        <nav id=login>\n" .
         "            <form action=\"$host#LOGIN\" method=POST " .
         "accept-charset=\"UTF-8\">\n" .
         "                <div>\n" .
         "                    <label for=name>" .
         $lang['name'] . "</label>\n" .
         "                    <input name=name id=name maxlength=16 " .
         "title=\"" . $lang['name_title'] . "\"/>\n" .
         "                    <input type=submit name=login " .
         "value=\"&gt;\" " .
         "title=\"" . $lang['login_title'] . "\"/>\n" .
         "                </div>\n" .
         "            </form>\n" .
         "            <div id=stat>\n" .
         "                <div>$stat</div>\n" .
         "                <noscript>" .
         $lang['noscript'] . "</noscript>\n" .
         "            </div>\n";
}

/**
 ***********************************************************************
 *                                                          END MARKUP *
 ***********************************************************************
 */

echo "            <p id=by><a href=\" " .
     "https://github.com/phhpro/atomchat\" " .
     "title=\"" . $lang['get'] . "\">PHP Atomchat v$ver</a></p>\n" .
     "        </nav>\n" .
     "    </body>\n" .
     "</html>\n";

/**
 ***********************************************************************
 *                                                    DELETE OLD FILES *
 ***********************************************************************
 */

if ($up_del === 1) {
    $up_old = $up_old * 24 * 60 * 60;

    foreach (new DirectoryIterator($up_fold) as $up_obj) {

        if ($up_obj -> isDot()) {
            continue;
        }

        if ($up_obj -> isFile()
            && time() - $up_obj -> getMTime() >= $up_old
        ) {
            unlink($up_obj -> getRealPath());
        }
    }
}

//** Lulu time
flush();
