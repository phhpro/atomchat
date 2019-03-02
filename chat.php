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
$ver = "20190302";

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
 * @return string data blob
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

if (is_file($log_data) && $log_auto === 1) {

    if ($log_size - filesize($log_data) <= 0) {
        unlink($log_data);
        file_put_contents($log_data, $init);
    }
}

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
            $lang_id = $lang_usr;
        }
    }

    unset($lang_obj);    
}

if (isset($_POST['lang_apply'])) {
    $lang_id = htmlentities($_POST['lang_id'], ENT_QUOTES, 'UTF-8');
    $_SESSION['ac_lang'] = $lang_id;

    if (isset($_COOKIE['ac_lang'])) {
        setcookie('ac_lang', $lang_id, $cook_time, '/');
    }

    $lang_id = $_SESSION['ac_lang'];
} else {

    if (isset($_COOKIE['ac_lang'])) {
        $lang_id = $_COOKIE['ac_lang'];
    } elseif (isset($_SESSION['ac_lang'])) {
        $lang_id = $_SESSION['ac_lang'];
    } else {
        $lang_id = $lang_def;
    }
}

$lang_data = $lang_fold . "/" . $lang_id . ".php";

if (is_file($lang_data)) {
    include $lang_data;
} else {
    /*
     * Handle exception when the selected file has been removed
     * before it could be loaded. Shouldn't really happen, but
     * better save than sorry.
     *
     * This loads the default to prevent a ghost screen without
     * text. If for whatever obscure reason that one's gone too,
     * the script gracefully dies right here.
     */
    $_SESSION['ac_lang'] = $lang_def;

    if (isset($_COOKIE['ac_lang'])) {
        setcookie('ac_lang', $lang_def, $cook_time, '/');
    }

    include $lang_fold . '/' . $lang_def . '.php';

    echo "<p>The requested file is no longer available!</p>\n" .
         "<p><a href=\"$host\" title=\"Click here to try loading " .
         "the default settings\">Click here to try loading the " .
         "default settings.</a></p>\n" .
         "<p>If that fails, and you know how to contact the site " .
         "owner, now would be a good moment.</p>";
    exit;
}

/**
 ***********************************************************************
 *                                                                 CSS *
 ***********************************************************************
 */

if (isset($_POST['css_apply'])) {
    $css_id = htmlentities($_POST['css_id'], ENT_QUOTES, 'UTF-8');

    if ($css_id !== "") {
        $_SESSION['ac_css'] = $css_id;

        if (isset($_COOKIE['ac_css'])) {
            setcookie('ac_css', $css_id, $cook_time, '/');
        }

        $css_id = $_SESSION['ac_css'];
    }
} else {

    if (isset($_COOKIE['ac_css'])) {
        $css_id = $_COOKIE['ac_css'];
    } elseif (isset($_SESSION['ac_css'])) {
        $css_id = $_SESSION['ac_css'];
    } else {
        $css_id = $css_def;
    }
}

$css_file = $css_fold . "/" . $css_id . ".css";

/**
 ***********************************************************************
 *                                                                MISC *
 ***********************************************************************
 */

$su   = $su_pfx . $su_sfx;
$init = "<div class=\"item_log\">LOG INIT $date</div>\n";

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
    file_put_contents($log_data, $init);
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
                            "<span class=\"emo\">$emo_ckey</span>",
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
                               "width=\"$up_tw\" height=\"$up_th\" " .
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
        $post  = "<div class=\"item\">\n" .
                 "    <div class=\"item_head\">\n" .
                 "        <span class=\"item_date\">$date</span> \n" .
                 "        <span class=\"item_name\">" .
                 $_SESSION['ac_name'] . "</span>\n" .
                 "    </div>\n" .
                 "    <pre class=\"item_text\">\n" .
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
 *                                                               LOGIN *
 ***********************************************************************
 */

if (isset($_POST['login'])) {
    $name      = htmlentities($_POST['name'], ENT_QUOTES, 'UTF-8');
    $cook_perm = htmlentities($_POST['cook_perm'], ENT_QUOTES, 'UTF-8');

    if ($name === "") {
        go('MISSING_NAME');
    } else {

        if ($name === $su) {
            $_SESSION['ac_name'] = $su_pfx;
        } else {
            $_SESSION['ac_name']
                = $name . "_" . mt_rand($rn_min, $rn_max);
        }

        $text = "<div class=\"item_log\">$date " .
                $_SESSION['ac_name'] . " LOGIN</div>\n";

        if (is_file($log_data)) {
            $text .= file_get_contents($log_data);
        }

        file_put_contents($log_data, $text);

        if (!isset($_COOKIE['ac_cook'])) {

            if (isset($cook_perm)) {
                setcookie('ac_cook', '1');

                if (count($_COOKIE) > 0) {
                    $cook_time = time() + (86400 * 30);
                    setcookie('ac_cook', '1', $cook_time, '/');
                    setcookie('ac_lang', $lang_def, $cook_time, '/');
                    setcookie('ac_css', $css_def, $cook_time, '/');
                    $_SESSION['ac_lang'] = $_COOKIE['ac_lang'];
                    $_SESSION['ac_css']  = $_COOKIE['ac_css'];
                }
            } else {
                $_SESSION['ac_lang'] = $lang_def;
                $_SESSION['ac_css']  = $css_def;
            }

            go('LOGIN');
        }
    }
}

/**
 ***********************************************************************
 *                                                              LOGOUT *
 ***********************************************************************
 */

if (isset($_POST['quit'])) {
    $text  = "<div class=\"item_log\">$date " .
             $_SESSION['ac_name'] . " LOGOUT</div>\n";
    $text .= file_get_contents($log_data);
    file_put_contents($log_data, $text);
    unset($_SESSION['ac_name']);
    go('LOGOUT');
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
     "        <meta name=\"language\" content=\"$lang_mime\"/>\n" .
     "        <meta name=\"description\" " .
     "content=\"$meta_des - PHP Atomchat free PHP chat scripts\"/>\n" .
     "        <meta name=\"keywords\" " .
     "content=\"$meta_key,PHP Atomchat,free PHP chat scripts\"/>\n" .
     "        <meta name=\"robots\" content=\"noodp, noydir\"/>\n" .
     "        <meta name=\"viewport\" content=\"width=device-width, " .
     "height=device-height, initial-scale=1\"/>\n" .
     "        <link rel=\"icon\" href=\"" . $host . "favicon.png\" " .
     "type=\"image/png\"/>\n" .
     "        <link rel=\"stylesheet\" href=\"$host$css_file\"/>\n" .
     "    </head>\n" .
     "    <body>\n" .
     "        <header class=\"block\">\n" .
     "            <h1 id=\"anim\">" .
     "<span></span><span></span><span></span> PHP Atom Chat</h1>\n" .
     "        </header>\n";

/**
 ***********************************************************************
 *                                                             CHATLOG *
 ***********************************************************************
 */

if (isset($_SESSION['ac_name']) && !empty($_SESSION['ac_name'])) {
    echo "        <form action=\"$host#CHAT\" name=\"chat\" " .
         "method=\"POST\" accept-charset=\"UTF-8\" " .
         "enctype=\"multipart/form-data\">\n" .
         "            <article class=\"block\" id=\"push\">\n";

    if (is_file($log_data)) {
        include $log_data;
    } else {
        file_put_contents($log_data, $init);
        include $log_data;
    }

    echo "            </article>\n";

    /**
     *******************************************************************
     *                                                        SETTINGS *
     *******************************************************************
     */

    if (isset($_POST['conf'])) {
        echo "            <article class=\"block\" id=\"settings\">\n" .
             "                <h2>" . $lang['conf'] . "\n" .

             //** Close
             "                    <input type=\"submit\" value=\"x\" " .
             "title=\"" . $lang['close'] . "\" class=\"flr\"/>\n" .
             "                </h2>\n" .

             //** Language
             "                <div>\n" .
             "                    <label for=\"lang_id\">" .
             $lang['lang'] . "</label>\n" .
             "                    <select name=\"lang_id\" " .
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
            echo "                        <option " .
                 "value=\"$lang_link\" lang=\"$lang_link\" " .
                 "title=\"$lang_text\">$lang_name</option>\n";
        }

        unset($lang_item);
        echo "                    </select>\n" .
             "                    <input type=\"submit\" " .
             "name=\"lang_apply\" value=\"#\" " .
             "title=\"" . $lang['apply'] . "\"/>\n" .
             "                </div>\n";

        //** Theme
        if ($css === 1) {
            echo "                <div>\n" .
                 "                    <label for=\"css_id\">" .
                 $lang['theme'] . "</label>\n" .
                 "                    <select name=\"css_id\" " .
                 "title=\"" . $lang['theme_title'] . "\">\n";

            $css_list = glob($css_fold . "/*.css");
            sort($css_list);

            foreach ($css_list as $css_item) {
                $css_link = basename($css_item);
                $css_link = str_replace(".css", "", $css_link);
                $css_text = str_replace(array("-", "_"), " ", $css_link);
                echo "                        <option value=\"".
                     "$css_link\" title=\"" . $lang['theme_title'] . " " .
                     ucwords($css_text) . "\">" . ucwords($css_text);

                if ($css_link === $_SESSION['ac_css']) {
                    echo " [x]";
                }

                echo "</option>\n";
            }

            unset($css_item);
            echo "                    </select>\n" .
                 "                    <input type=\"submit\" " .
                 "name=\"css_apply\" value=\"#\" " .
                 "title=\"" . $lang['apply'] . "\"/>\n" .
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
            echo "                <h3>" . $lang['emo'] . "</h3>\n" .
                 "                <pre class=\"emo\">\n";

            foreach ($emo_parr as $emo_code) {
     
                if ($emo_code !== "") { 
                    $emo_line   = explode("|", $emo_code);
                    $emo_sarr[] = $emo_line;
                    $emo_calt   = $emo_line[0];
                    $emo_ckey   = $emo_line[1];
                    echo $emo_calt .
                         "<span class=\"emo\">$emo_ckey</span>\n";
                }
            }

            unset($emo_code);
            echo "                </pre>\n";
        }

        //** Upload
        if ($up === 1) {
            echo "                <h3>" . $lang['up'] . "</h3>\n" .
                 "                <p>" .
                 $lang['up_max'] . " $up_max</p>\n" .

            //** Image, Base64
                 "                <ul>\n" .
                 "                    <li>" . $lang['up_is_b64'] . "\n" .
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
                 "                    <li>" . $lang['up_is_img'] . "\n" .
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
                 "                    <li>" . $lang['up_is_snd'] . "\n" .
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
                 "                    <li>" . $lang['up_is_vid'] . "\n" .
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
                 "                    <li>" . $lang['up_is_doc'] . "\n" .
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
                 "                    <li>" . $lang['up_is_arc'] . "\n" .
                 "                        <ul>\n";

            foreach ($up_is_arc as $up_arc) {
                echo "                            <li>$up_arc</li>\n";
            }

            unset($up_arc);
            echo "                        </ul>\n" .
                 "                    </li>\n" .
                 "                </ul>\n";
        }

        //** Credits
        if (is_file('credits.php')) {
            echo "                <h3>" . $lang['credits'] . "</h3>\n";
            include 'credits.php';
        }

        echo "        </article>\n";
    }

    /**
     *******************************************************************
     *                                                          NAVBAR *
     *******************************************************************
     */
    echo "            <nav class=\"block\">\n" .
         "                <div class=\"s\">\n" .
         "                    " . $lang['text'] . " <input disabled " .
         "id=\"char\" size=\"4\" value=\"$char\"/>\n" .
         "                </div>\n" .
         //** Text
         "                <textarea name=\"text\" id=\"text\" " .
         "rows=\"2\" cols=\"45\" maxlength=\"$char\" " .
         "title=\"" . $lang['text_title'] . "\" " .
         "onkeydown=\"chars(this.form);\" ".
         "onkeypress=\"chars(this.form);\" " .
         "onkeyup=\"chars(this.form);\">";

    if (!empty($_POST['text'])) {
        $text_tmp = $_POST['text'];
    }

    if (isset($text_tmp)) {
        echo $text_tmp;
    }

    echo "</textarea>\n" .
         "                <div>\n" .

         //** Name -- hidden session token
         "                    <input type=\"hidden\" name=\"name\" " .
         "value=\"" . $_SESSION['ac_name'] . "\"/>\n";

    //** Reset log -- super user only
    if ($_SESSION['ac_name'] === $su_pfx) {
        echo "                    <input type=\"submit\" " .
             "name=\"reset\" id=\"reset\" value=\"=\" " .
             "title=\"" . $lang['reset'] . "\"/>\n";
    }

         //** Quit
    echo "                    <input type=\"submit\" name=\"quit\" " .
         "id=\"quit\" value=\"x\" " .
         "title=\"" . $lang['quit_title'] . "\"/>\n" .

         //** Save
         "                    <input type=\"submit\" name=\"save\" " .
         "id=\"save\" value=\"v\" " .
         "title=\"" . $lang['save'] . "\"/>\n" .

         //** Conf
         "                    <input type=\"submit\" name=\"conf\" " .
         "id=\"conf\" value=\"?\" " .
         "title=\"" . $lang['conf_title'] . "\"/>\n" .

         //** Post
         "                    <input type=\"submit\" name=\"post\" " .
         "id=\"post\" value=\"#\" " .
         "title=\"" . $lang['post'] . "\"/>\n" .
         "                </div>\n";

    //** Upload
    if ($up === 1) {
        echo "                <div>\n" .
             "                    <input type=\"file\" name=\"file\" " .
             "title=\"" . $lang['up_select'] . "\"/>\n" .
             "                    <div class=\"s\">" .
             $lang['up_max'] . " $up_max</div>\n" .
             "                </div>\n";
    }

    echo "            </nav>\n" .
         "        </form>\n";
} else {
    /**
    ********************************************************************
    *                                                            LOGIN *
    ********************************************************************
    */

    echo "        <form action=\"$host#LOGIN\" method=\"POST\" " .
         "accept-charset=\"UTF-8\">\n" .
         "            <article class=\"block\" id=\"home\">\n" .
         "                <h2>" . $lang['welcome'] . "</h2>\n";

    //** Cookie
    if (!isset($_COOKIE['ac_cook'])) {
        echo "                <h3>" . $lang['cook_perm'] . "</h3>\n" .
             "                <p>\n" .
             "                    " . $lang['cook_ask'] . "\n" .
             "                    <input type=\"checkbox\" " .
             "name=\"cook_perm\" id=\"cook_perm\" ".
             "title=\"" . $lang['cook_title'] . "\"/> \n" .
             "                </p>\n" .
             "                <p>" . $lang['cook_del'] . "</p>\n";
    }

    //** Info
    echo "                <p>" . $lang['name_info'] . "</p>\n" .
         "                <noscript>\n" .
         "                    <h3>" . $lang['js_warn'] . "</h3>\n" .
         "                    <p>" . $lang['js_info'] . "</p>\n" .
         "                    <p>" . $lang['js_text'] . "</p>\n" .
         "                </noscript>\n" .
         "            </article>\n" .
         "            <nav class=\"block\" id=\"login\">\n" .
         "                <div>\n" .

         //** Name
         "                    <label for=\"name\">" .
         $lang['name'] . "</label>\n" .
         "                    <input name=\"name\" id=\"name\" " .
         "maxlength=\"16\" title=\"" . $lang['name_title'] . "\"/> \n" .

         //** Login
         "                    <input type=\"submit\" name=\"login\" " .
         "value=\"&gt;\" title=\"" . $lang['login'] . "\"/>\n" .
         "                </div>\n" .
         "            </nav>\n" .
         "        </form>\n";
}

/**
 ***********************************************************************
 *                                                          END MARKUP *
 ***********************************************************************
 */

echo "        <div id=\"by\">\n" .
     "            <a href=\"https://github.com/phhpro/atomchat\" " .
     "title=\"" . $lang['get'] . "\">PHP Atomchat v$ver</a>\n" .
     "        </div>\n" .
     "        <script>\n" .
     "        var char = $char;\n" .
     "        var rate = $rate;\n" .
     "        var data = \"$log_data\";\n" .
     "        </script>\n" .
     "        <script src=\"chat.js\"></script>\n" .
     "    </body>\n" .
     "</html>\n";

/**
 ***********************************************************************
 *                                                          DELETE OLD *
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
