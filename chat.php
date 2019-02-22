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
$ver = "20190222";

//** Headers
header('Expires: on, 01 Jan 1970 00:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache');
header('Pragma: no-cache');
header_remove('X-Powered-By');

// Dependencies
if (is_file('conf.php')) {
    include './conf.php';
} else {
    echo "No configuration!";
    exit;
}

if (!is_dir($lang_fold)) {
    echo "No language folder!";
    exit;
}

if (!is_dir($log_fold)) {

    if (mkdir($log_fold) === false) {
        echo "Cannot create log folder!";
        exit;
    }
}

if ($up === 1) {

    if (!is_dir($up_fold)) {

        if (mkdir($up_fold) === false) {
            echo "Cannot create upload folder!";
            exit;
        }
    }
}

if ($emo === 1) {

    if (is_file($emo_conf)) {
        $emo_trim = file_get_contents($emo_conf);

        if (filesize($emo_conf) < 16 && trim($emo_trim) === false) {
            echo "Bad emoji definition!";
            exit;
        } else {
            $emo_parr = array();
            $emo_sarr = array();
            $emo_code = "";
        }
    } else {
        echo "No emoji definition!";
        exit;
    }
}

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
    if (php_sapi_name() !== 'cli') {

        if (version_compare(phpversion(), '5.4.0', '>=')) {
            return session_status()
                === PHP_SESSION_ACTIVE ? true : false;
        } else {
            return session_id() === '' ? false : true;
        }
    }

    return false;
}

if (sessionstat() === false) {
    if (!session_start()) {
        echo "Session not supported!";
        exit;
    } else {
        session_start();
    }
}

/**
 * Function rnum()
 *
 * @return integer random number
 */
function rnum()
{
    $rn = mt_rand(1000, 9999);
    return $rn;
}

/**
 * Function b64enc()
 *
 * @param string $b64_src source file
 *
 * @return string data block
 */
function b64enc($b64_src)
{
    $b64_src = $b64_src;
    $b64_ext = pathinfo($b64_src, PATHINFO_EXTENSION);
    $b64_get = file_get_contents($b64_src);
    $b64_str = "\"data:image/" . $b64_ext .
               ";base64," . base64_encode($b64_get) . "\" ";
    return chunk_split($b64_str);
}

//** Protocol and URL
$prot = "";

if (isset($_SERVER['HTTPS']) && "on" === $_SERVER['HTTPS']) {
    $prot = "s";
}

$host = "http$prot://" . $_SERVER['HTTP_HOST'] . "/$fold/";

//** Log
if ($log_mode === 0) {
    $log_name = $log_name . "_" . gmdate('Y-m-d');
}

$log_data = $log_fold . "/" . $log_name . ".html";
$log_stat = filesize($log_data);

if (is_file($log_data)) {

    if ($log_stat > $log_size) {
        unlink($log_data);
    }
}

//** Home screen and initial status
$home = "home.php";
$stat = "";

//** Logo
if ($logo_i !== "") {
    $logo_i = "<img src=" . b64enc($logo_i) .
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

//** Language
$lang_mime = $lang_def;

if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $lang_hal = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    $lang_usr = $lang_hal;
    $lang_php = glob($lang_fold . "/*.php");

    foreach ($lang_php as $lang_obj) {
        $lang_obj = str_replace("lang/", "", $lang_obj);
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
        = htmlentities($_POST['lang_id'], ENT_QUOTES, "UTF-8");
}

$lang_id   = $_SESSION['ac_lang'];
$lang_data = $lang_fold . "/" . $lang_id . ".php";

if (is_file($lang_data)) {
    $lang_trim = file_get_contents($lang_data);

    if (filesize($lang_data) < 16 && trim($lang_trim) === false) {
        $stat = "Bad language file!";
    } else {
        include $lang_data;
    }
} else {
    $stat = "No language file!";
}

//** Login
if (isset($_POST['login'])) {
    $name = htmlentities($_POST['name'], ENT_QUOTES, "UTF-8");

    if ($name === "") {
        header("Location: $host#NO_NAME");
        exit;
    } else {
        $_SESSION['ac_name'] = $name . "_" . rnum();
        $text  = "            <div class=item_log>$date " .
                 $_SESSION['ac_name'] . " LOGIN</div>\n";

        if (is_file($log_data)) {
            $text .= file_get_contents($log_data);
        }

        $stat  = "";
        file_put_contents($log_data, $text);
        header("Location: $host#LOGIN");
        exit;
    }
}

//** Save -- download log
if (isset($_POST['save'])) {
    header('Content-type: text/html');
    header(
        'Content-Disposition: attachment; filename="' .
        str_replace($log_fold . "/", "", $log_data) . '"'
    );
    readfile($log_data);
    exit;
}

//** Logout
if (isset($_POST['quit'])) {
    $text  = "            <div class=item_log>$date " .
             $_SESSION['ac_name'] . " LOGOUT</div>\n";
    $text .= file_get_contents($log_data);
    file_put_contents($log_data, $text);
    unset($_SESSION['ac_name']);
    unset($_SESSION['ac_css']);
    unset($_SESSION['ac_lang']);
    header("Location: $host#LOGOUT");
    exit;
}

//** Initial upload state
$pass = 0;

//** Post
if (isset($_POST['post'])) {
    $name = htmlentities($_POST['name'], ENT_QUOTES, "UTF-8");
    $text = htmlentities($_POST['text'], ENT_QUOTES, "UTF-8");

    if ($text !== "") {
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
        $up_base = basename($_FILES['file']['name']);
        $up_file = $up_fold . "/" . $up_base;
        $up_type = strtolower(pathinfo($up_file, PATHINFO_EXTENSION));
        $_SESSION['ac_rand'] = rnum();
        $up_rand = $_SESSION['ac_name'] . "-" .
                   $_SESSION['ac_rand'] . "." . $up_type;
        $up_save = str_replace($up_base, $up_rand, $up_file);
        $up_text = str_replace("$up_fold/", "", $up_save);
        $up_open = $host . $up_save;

        if (!empty($_FILES['file']['name'])) {
            $up_name = $_FILES['file']['tmp_name'];
            $up_temp = getimagesize($_FILES['file']['tmp_name']);
            $up_size = $_FILES['file']['size'];

            if ($up_size > $up_max) {
                header("Location: $host#BAD_FILESIZE");
                exit;
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
                               "<img src=" . b64enc($up_name) .
                               "width=$up_tnw height=$up_tnh " .
                               "alt=\"\"/></a></p>";
                } else {
                    header("Location: $host#BAD_IMAGE");
                    exit;
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
                header("Location: $host#BAD_FILETYPE");
                exit;
            }

            if (move_uploaded_file(
                $_FILES['file']['tmp_name'], $up_file
            )
            ) {
                copy($up_file, $up_save);
                $pass = 1;
            } else {
                header("Location: $host#WRITE_ERROR");
                exit;
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

    if ($pass === 1) {
        $post  = "            <div class=item>\n" .
                 "                <div class=item_head>\n" .
                 "                    <span class=item_date>" .
                 "$date</span> <span class=item_name>" .
                 $_SESSION['ac_name'] . "</span>\n" .
                 "                </div>\n" .
                 "                <pre class=item_text>$post</pre>\n" .
                 "            </div>\n";
        $post .= file_get_contents($log_data);
        file_put_contents($log_data, $post);
        header("Location: $host#POST");
        exit;
    } else {
        header("Location: $host#BAD_POST");
        exit;
    }
}

if (isset($_POST['css_apply'])) {
    $css_id = htmlentities($_POST['css_id'], ENT_QUOTES, "UTF-8");

    if ($css_id !== "") {
        $_SESSION['ac_css'] = $css_id;
    }
}

if (isset($_SESSION['ac_css'])) {
    $css_sel = $_SESSION['ac_css'];
} else {
    $css_sel              = $css_def;
    $_SESSION['ac_css'] = $css_sel;
}

$css_file = $css_fold . "/" . $css_sel . ".css";

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
     "        <link rel=icon href=\"" . $host . "favicon.png\" " .
     "type=\"image/png\"/>\n" .
     "        <link rel=stylesheet href=\"$host$css_file\"/>\n" .
     "    </head>\n" .
     "    <body id=body>\n" .
     "        <header>\n" .
     "            <h1>$logo</h1>\n" .
     "        </header>\n";

//** Settings
if (isset($_POST['conf'])) {
    echo "        <article>\n" .
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
         "value=\"x\" " . 
         "title=\"" . $lang['close_title'] . "\"/>\n" .
         "                </div>\n" .
         "            </form>\n" .
         "        </article>\n";
}

//** Check name session
if (isset($_SESSION['ac_name']) && !empty($_SESSION['ac_name'])) {
    echo "        <div id=push>\n";

    if (is_file($log_data)) {
        include $log_data;
    } else {
        $stat = $lang['first'];
    }

    //** Navigation
    echo "        </div>\n" .
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
         "onkeydown=chars(this.form); ".
         "onkeypress=chars(this.form); " .
         "onkeyup=chars(this.form);></textarea>\n" .
         "                <div>\n" .

         //** Name -- hidden session token
         "                    <input type=hidden name=name " .
         "value=\"" . $_SESSION['ac_name'] . "\"/>\n" .

         //** Quit
         "                    <input type=submit name=quit " .
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
    if (is_file($log_data)) {
        echo "                <div><small>" . $lang['log_reset'] .
             " $log_stat / $log_size</small></div>\n";
    }

    echo "            </form>\n" .
         "            <div id=stat>\n" .
         "                <div>$stat</div>\n" .
         "                <script src=\"chat.js\"></script>\n" .
         "                <noscript>" .
         $lang['noscript'] . "</noscript>\n" .
         "            </div>\n";
} else {
    echo "        <article>\n";

    if (is_file($home)) {
        include "./$home";
    }

    //** Login
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

//** End mark-up
echo "            <p id=by><a href=\" " .
     "https://github.com/phhpro/atomchat\" " .
     "title=\"" . $lang['get'] . "\">PHP Atomchat v$ver</a></p>\n" .
     "        </nav>\n" .
     "    </body>\n" .
     "</html>\n";

//** Old files
if ($up_del === 1) {
    $up_old = $up_old * 24 * 60 * 60;

    if (file_exists($up_fold)) {

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
}

flush();
