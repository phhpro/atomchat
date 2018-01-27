<?php
/**
 * PHP Version 5 and above
 *
 * @category PHP_Chat_Scripts
 * @package  PHP_Atom_Chat
 * @author   P H Claus <phhpro@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @version  GIT: Latest
 * @link     https://github.com/phhpro/atomchat
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
 *
 *
 * Main script and configuration
 */


/**
 ***********************************************************************
 *                                                   BEGIN USER CONFIG *
 ***********************************************************************
 */


//** Values of 0 mean NO -- 1 equals YES


/**
 * Script folder
 * Initial screen
 */
$ac_fold     = "atomchat";
$ac_init     = "init.php";

/**
 * Chat title
 * Chat image -- leave empty if not needed
 */

$ac_title    = "PHP Atom Chat";
$ac_image    = '<img src=favicon.png width=16 height=16 alt=""/>';

/**
 * Maximum characters allowed per post
 * Expire inactive session -- n * 60 = s (30 m * 60 s = 1800 s)
 */
$ac_max_char = 256;
$ac_expire   = 1800;

/**
 * Default style
 * User style selection
 */
$ac_css_def  = "grey";
$ac_css_usr  = 1;

/**
 * Default language
 * Convert emoji icons -- https://en.wikipedia.org/wiki/Emoji
 */
$ac_lang_def = "en";
$ac_emo_auto = 1;


/**
 ***********************************************************************
 *                                                     END USER CONFIG *
 ***********************************************************************
 */


/**
 * Script version
 * Query string
 * Init status text
 */
$ac_version  = "20180127";
$ac_query = $_SERVER['QUERY_STRING'];
$ac_stat  = "";

//** Init session
session_start();
$_SESSION['ac_test'] = 1;

//** Test session -- static because no language file is available
if ($_SESSION['ac_test'] !== 1) {
    echo "<p>Missing session cookie!</p>\n" .
         "<p>Please edit your browser's cookie " .
         "settings and then try again.</p>\n";
    exit;
} else {
    unset($_SESSION['ac_test']);
}

//** Link language data file and selector config
$ac_lang_data = './lang/' . $ac_lang_def . '.php';
$ac_lang_conf = './lang/__config.php';

/**
 * Check language file
 *
 * This tests only if the file exist.
 * It will fail if it does but is empty or else invalid!
 */
if (file_exists($ac_lang_data) && file_exists($ac_lang_conf)) {
    $ac_lang_mime = $ac_lang_def;
    include $ac_lang_data;
} else {
    //** Static because no language file is available
    echo "<p>Missing or invalid language data file or " .
         "selector configuration!</p>\n" .
         "<p>Please check your settings.</p>\n";
    exit;
}

//** Build language reference
if (isset($ac_query) && strpos($ac_query, "lang_") !== false) {
    $ac_lang_id = str_replace("lang_", "", $ac_query);
} else {
    //** Use default if selected file is missing
    $ac_lang_id = $ac_lang_def;
}

//** Link language user selection
$ac_lang_user = 'lang/' . $ac_lang_id . '.php';

//** Check selected language data file
if (file_exists($ac_lang_user)) {
    $ac_lang_mime = $ac_lang_id;
    include $ac_lang_user;
} else {
    /**
     * There should now be a valid language file so drop
     * static text and use translation strings instead.
     */
    $ac_stat = $ac_lang['lang_def'];
}

//** Stop message
$ac_stop = $ac_lang['stop'];

//** Check protocol
if (isset($_SERVER['HTTPS']) && "on" === $_SERVER['HTTPS']) {
    $ac_prot = "s";
} else {
    $ac_prot = "";    
}

//** Build URL reference
$ac_host = "http" . $ac_prot .
           "://" . $_SERVER['HTTP_HOST'] . "/" . $ac_fold . "/";

//** Try to prevent caching
header('Expires: on, 01 Jan 1970 00:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

//** Check log folder
if (!is_dir('log')) {

    if (mkdir('log') === false) {
        echo "<p>" . $ac_lang['fail_log_dir'] . "</p>\n" .
             "<p>" . $ac_lang['folder_write'] . "</p>\n";
        exit;
    }
}

/**
 * Log data file
 * User name lock
 * Live counter lock
 */
$ac_chat_data = "log/" . date('Ymd') . ".html";
$ac_lock_name = "name.lock";
$ac_lock_live = "live.lock";

//** Check name lock
if (!file_exists($ac_lock_name)) {
    $ac_lock_hand = fopen($ac_lock_name, 'w');
    fwrite($ac_lock_hand, "");
    fclose($ac_lock_hand);
}

//** Check live counter lock
if (!file_exists($ac_lock_live)) {
    $ac_lock_hand = fopen($ac_lock_live, 'w');
    fwrite($ac_lock_hand, 0);
    fclose($ac_lock_hand);
}

//** Link styles and emoji config
$ac_css_conf = "css/__config.txt";
$ac_emo_conf = "emoji.txt";


//** Check style
if (!file_exists("css/" . $ac_css_def . ".css")) {
    echo "<p>" . $ac_lang['miss_css_style'] . "</p>\n" .
         "<p>$ac_stop</p>\n";
    exit;
}

//** Check emoji config
if ($ac_emo_auto === 1) {

    if (!file_exists($ac_emo_conf)) {
        echo "<p>" . $ac_lang['miss_emo_conf'] . "</p>\n" .
             "<p>$ac_stop</p>\n";
        exit;
    }
}

/**
 * Link emoji primary array
 * Link emoji secondary array
 * Link emoji code
 * Init live counter
 */
$ac_emo_parr = array();
$ac_emo_sarr = array();
$ac_emo_code = "";
$ac_live     = (int)file_get_contents($ac_lock_live);

//** Expire session
if (isset($_SESSION['ac_time']) && !empty($_SESSION['ac_time']) 
    && isset($_SESSION['ac_name']) && !empty($_SESSION['ac_name'])
) {
    $ac_time = (time()-(int)$_SESSION['ac_time']);
    $ac_diff = ($ac_expire-$ac_time);

    if ($ac_diff <= 0) {
        //** Update user name lock
        file_put_contents(
            $ac_lock_name, str_replace(
                $_SESSION['ac_name'] . 
                "\n", "", file_get_contents($ac_lock_name)
            )
        );

        //** Update data file
        $ac_text  = "            <div class=ac_item>" .
                    gmdate('Y-m-d H:i:s') . " " . $_SESSION['ac_name'] .
                    " " . $ac_lang['chat_leave'] . "</div>\n";
        $ac_text .= file_get_contents($ac_chat_data);

        file_put_contents($ac_chat_data, $ac_text);

        //** Clear session
        unset($_SESSION['ac_time']);
        unset($_SESSION['ac_name']);

        //** Update live counter and load interface
        $ac_live_data = (int)file_get_contents($ac_lock_live);
        $ac_live_list = $ac_live_data;

        if ($ac_live_list >1) {
            $ac_live_data = ($ac_live_list-1);
        } else {
            $ac_live_data = 0;
        }

        file_put_contents($ac_lock_live, $ac_live_data);
        header('Location: #SESSION_EXPIRED');
        exit;
    }
} else {
    $_SESSION['ac_time'] = time();
}

//** Login
if (isset($_POST['ac_login'])) {
    //** Link user name
    $ac_name = htmlentities($_POST['ac_name'], ENT_QUOTES, "UTF-8");

    //** Check name
    if ($ac_name === "") {
        header('Location: #MISSING_NAME');
        exit;
    } elseif (stripos(file_get_contents($ac_lock_name), $ac_name) !== false) {
        header('Location: #NAME_NOT_AVAILABLE');
        exit;
    } else {
        //** Init session
        $_SESSION['ac_time'] = time();
        $_SESSION['ac_name'] = $ac_name;

        //** Lock user name and update data file
        file_put_contents($ac_lock_name, $ac_name . "\n", FILE_APPEND);

        $ac_text  = "            <div class=ac_item>" .
                    gmdate('Y-m-d H:i:s') . " " . $_SESSION['ac_name'] .
                    " " . $ac_lang['chat_enter'] . "</div>\n";

        if (file_exists($ac_chat_data)) {
            $ac_text .= file_get_contents($ac_chat_data);
        }

        file_put_contents($ac_chat_data, $ac_text);

        //** Update live counter and reload interface
        $ac_live_data = (int)file_get_contents($ac_lock_live);
        $ac_live_list = $ac_live_data;
        $ac_live_data = ($ac_live_list+1);

        file_put_contents($ac_lock_live, $ac_live_data);
        header('Location: #LOGIN');
        exit;
    }
}

//** Save data file
if (isset($_POST['ac_save'])) {
    header('Content-type: text/html');
    header(
        'Content-Disposition: attachment; filename="' . 
        str_replace("log/", "", $ac_chat_data) . '"'
    );

    readfile($ac_chat_data);
    exit;
}

//** Quit session
if (isset($_POST['ac_quit'])) {

    //** Update user name lock
    file_put_contents(
        $ac_lock_name, str_replace(
            $_SESSION['ac_name'] .
            "\n", "", file_get_contents($ac_lock_name)
        )
    );

    //** Update data file
    $ac_text  = "            <div class=ac_item>" .
                gmdate('Y-m-d H:i:s') . " " . $_SESSION['ac_name'] .
                " " . $ac_lang['chat_leave'] . "</div>\n";
    $ac_text .= file_get_contents($ac_chat_data);

    file_put_contents($ac_chat_data, $ac_text);

    //** Clear session
    unset($_SESSION['ac_time']);
    unset($_SESSION['ac_name']);

    //** Update live counter and load interface
    $ac_live_data = (int)file_get_contents($ac_lock_live);
    $ac_live_list = $ac_live_data;

    if ($ac_live_list <1) {
        $ac_live_data = 0;
    } else {
        $ac_live_data = ($ac_live_list-1);
    }

    file_put_contents($ac_lock_live, $ac_live_data);
    header('Location: #LOGOUT');
    exit;
}

//** Push manual update
if (isset($_POST['ac_push'])) {
    header('Location: #PUSH_UPDATE');
    exit;
}

//** New entry
if (isset($_POST['ac_post'])) {
    $ac_name = htmlentities($_POST['ac_name'], ENT_QUOTES, "UTF-8");
    $ac_text = filter_var($_POST['ac_text'], FILTER_SANITIZE_SPECIAL_CHARS);

    //** Skip empty post
    if (!empty($ac_text)) {

        if (!file_exists($ac_chat_data)) {
            file_put_contents($ac_chat_data, $ac_link);
        }

        //** Check emoji conversion
        if ($ac_emo_auto === 1) {

            //** Check empty config -- true if file has only BOM or spaces
            $ac_emo_trim = file_get_contents($ac_emo_conf);

            if (filesize($ac_emo_conf) <16 && trim($ac_emo_trim) === false) {
                $ac_stat = $ac_lang['emo_empty'];
            } else {
                //** Link primary array and config
                $ac_emo_open = fopen($ac_emo_conf, 'r');

                //** Parse config
                while (!feof($ac_emo_open)) {
                    $ac_emo_line   = fgets($ac_emo_open);
                    $ac_emo_line   = trim($ac_emo_line);
                    $ac_emo_parr[] = $ac_emo_line;
                }

                fclose($ac_emo_open);
            }

            //** Link secondary array
            $ac_emo_sarr = array();

            //** Parse lines and split values
            foreach ($ac_emo_parr as $ac_emo_code) {
                $ac_emo_line   = explode("|", $ac_emo_code);
                $ac_emo_sarr[] = $ac_emo_line;
                $ac_emo_calt   = $ac_emo_line[0];
                $ac_emo_ckey   = $ac_emo_line[1];

                //** Convert alternative to emoji
                if (stripos($ac_text, $ac_emo_calt) !== false) {
                    $ac_text = str_replace($ac_emo_calt, "<span class=emo>" . $ac_emo_ckey ."</span>", $ac_text);
                }
            }

            unset($ac_emo_code);
        }

        //** Check latest flag
        $ac_latest = file_get_contents($ac_chat_data);

        //** Strip existing latest flag
        if (strpos($ac_latest, "ac_item latest") !== false) {
            $ac_tatest = str_replace(
                '"ac_item latest"', 'ac_item', $ac_latest
            );

            file_put_contents($ac_chat_data, $ac_tatest);
        }

        //** Update data file and set new latest flag
        $ac_text  = '            <div class="ac_item latest" ' .
            'id="ac_' . gmdate('YmdHis') . '_' . $_SESSION['ac_name'] .
            '">' . gmdate('Y-m-d H:i:s') . " " . "<strong>" .
            $_SESSION['ac_name'] . " &#62;</strong> " . 
            str_replace("&#13;&#10;", "", $ac_text) . "</div>\n";

        $ac_text .= file_get_contents($ac_chat_data);

        file_put_contents($ac_chat_data, $ac_text);
        header('Location: #NEW_POST');
        exit;
    } else {
        header('Location: #EMPTY_POST');
        exit;
    }
}

//** Check current style
if (isset($_POST['ac_css_apply'])) {
    $ac_css_conf = htmlentities($_POST['ac_css_list'], ENT_QUOTES, "UTF-8");

    //** Link selected style
    if ($ac_css_conf !== "") {
        $_SESSION['ac_css'] = $ac_css_conf;
    }
}

//** Check style session and apply theme
if (isset($_SESSION['ac_css'])) {
    $ac_css_sel = $_SESSION['ac_css'];
} else {
    //** Link default style
    $ac_css_sel = $ac_css_def;
    $_SESSION['ac_css'] = $ac_css_sel;
}

//** Header
echo "<!DOCTYPE html>\n" .
     '<html lang="' . $ac_lang_mime . '">' . "\n" .
     "    <head>\n" .
     "        <title>" . $ac_title . "</title>\n" .
     '        <meta charset="UTF-8"/>' . "\n" .
     '        <meta name=language content="' . $ac_lang_mime . '"/>' . "\n" .
     '        <meta name=description content="PHP Atom Chat is a ' .
     'free PHP IRC like chat script. No database required."/>' . "\n" .
     '        <meta name=keywords ' .
     'content="PHP Atom Chat,free PHP chat scripts"/>' . "\n" .
     '        <meta name=robots content="noodp, noydir"/>' . "\n" .
     '        <meta name=viewport content="width=device-width, ' .
     'height=device-height, initial-scale=1"/>' . "\n" .
     '        <link rel=icon href="favicon.ico" ' .
     'type="image/vnd.microsoft.icon"/>' . "\n" .
     '        <link rel=stylesheet href="css/' . $ac_css_sel .
     '.css" type="text/css"/>' . "\n" .
     "    </head>\n" .
     "    <body>\n" .
     "        <header>\n" .
     "            <span id=ac_logo>$ac_image $ac_title</span>\n" .
     "            <span id=ac_live>\n";

//** Language selector
require $ac_lang_conf;

echo "                " . $ac_lang['online'] . " " . $ac_live . "\n" .
     "            </span>\n" .
     "        </header>\n";

//** List styles
if (isset($_POST['ac_css_sel'])) {
    $ac_css_trim = file_get_contents($ac_css_conf);

    //** Check empty config -- true if file has only BOM or spaces
    if (filesize($ac_css_conf) <16 && trim($ac_css_trim) === false) {
        $ac_stat = $ac_lang['css_empty'];
    } else {
        $ac_css_line = file($ac_css_conf);
        echo "        <article>\n" .
             "            <h1>" . $ac_lang['css_sel_head'] . "</h1>\n" .
             "            <p>" . $ac_lang['css_sel_text'] .
             " " . $ac_lang['win_close'] . "</p>\n" .
             '            <form action="#CHAT" id=ac_css_form ' .
             'method=POST accept-charset="UTF-8">' . "\n" .
             "                <div>\n" .
             "                    <select name=ac_css_list>\n";

        //** Init styles item
        $ac_css_item = "";

        //** Parse list and print items
        foreach ($ac_css_line as $ac_css_item) {
            $ac_css_item = trim($ac_css_item);

            echo '                        <option value="' . $ac_css_item . 
                 '" title="' . $ac_lang['select_style'] . ' ' .
                 ucwords($ac_css_item) . '">' . ucwords($ac_css_item);

            //** Flag current style
            if (isset($_SESSION['ac_css'])
                && $ac_css_item === $_SESSION['ac_css']
            ) {
                echo " [x]";
            }

            echo "</option>\n";
        }

        unset($ac_css_item);

        //** Function buttons
        echo "                    </select>\n" .
             '                    <input type=submit name=ac_css_apply ' .
             'value="' . $ac_lang['apply'] . '" title="' .
             $ac_lang['apply_title'] . '"/>' . "\n" .
             '                    <input type=submit name=ac_css_close ' .
             'value="' . $ac_lang['close'] . '" title="' .
             $ac_lang['close_title'] . '"/>' . "\n" .
             "                </div>\n" .
             "            </form>\n" .
             "        </article>\n";
    }
}

//** List emoji conversion table
if (isset($_POST['ac_emo_codes'])) {
    $ac_emo_trim = file_get_contents($ac_emo_conf);

    //** Check empty config -- true if file has only BOM or spaces
    if (filesize($ac_emo_conf) <16 && trim($ac_emo_trim) === false) {
        $ac_stat = $ac_lang['emo_empty'];
    } else {
        //** Link primary array and config
        $ac_emo_parr = array();
        $ac_emo_open = fopen($ac_emo_conf, 'r');

        //** Parse list
        while (!feof($ac_emo_open)) {
            $ac_emo_line   = fgets($ac_emo_open);
            $ac_emo_line   = trim($ac_emo_line);
            $ac_emo_parr[] = $ac_emo_line;
        }

        fclose($ac_emo_open);
    }

    echo "        <article>\n" .
         "            <h1>" . $ac_lang['emo_table'] ."</h1>\n" .
         "            <p>" . $ac_lang['emo_table_text'] .
         " " . $ac_lang['win_close'] . "</p>\n" .
         '            <form action="#CHAT" id=ac_css_form ' .
         'method=POST accept-charset="UTF-8">' . "\n" .
         "                <pre>\n";

    //** Print list
    foreach ($ac_emo_parr as $ac_emo_code) {
        $ac_emo_line   = explode("|", $ac_emo_code);
        $ac_emo_sarr[] = $ac_emo_line;
        $ac_emo_calt   = htmlentities($ac_emo_line[0]);
        $ac_emo_ckey   = $ac_emo_line[1];

        echo "$ac_emo_calt == <span class=emo>$ac_emo_ckey</span>\n";
    }

    unset($ac_emo_code);

    echo "                </pre>\n" .
         "                <div>\n" . 
         '                    <input type=submit name=ac_emo_close ' .
         'value="' . $ac_lang['close'] . '" title="' .
         $ac_lang['close_title'] . '"/>' . "\n" .
         "                </div>\n" .
         "            </form>\n" .
         "        </article>\n";
}

//** Check user name session
if (isset($_SESSION['ac_name']) && !empty($_SESSION['ac_name'])) {
    echo "        <div id=ac_push>\n";

    //** Check existing data file
    if (file_exists($ac_chat_data)) {
        include $ac_chat_data;
    } else {
        $ac_stat = $ac_lang['log_empty'];
    }

    //** Function buttons
    echo "        </div>\n" .
         "        <nav>\n" .
         '            <form action="#CHAT" method=POST ' .
         'id=ac_chat_form accept-charset="UTF-8">' . "\n" .
         '                <div id=ac_char>' . $ac_lang['text'] . ' ' .
         "<small>(" . $ac_max_char . " " . $ac_lang['characters'] .
         ")</small></div>\n" .
         '                <textarea name=ac_text id=ac_text ' .
         'rows=4 cols=60 maxlength=' . $ac_max_char .
         ' title="' . $ac_lang['text_title'] . '"></textarea>' . "\n" .
         "                <div>\n" .
         '                    <input type=hidden name=ac_name ' .
         'value="' . $_SESSION['ac_name'] . '"/>' . "\n" .
         '                    <input type=submit name=ac_quit ' .
         'value="' . $ac_lang['quit'] . '" title="' .
         $ac_lang['quit_title'] . '"/>' . "\n";

    //** Check style user selection
    if ($ac_css_usr === 1) {
        echo '                    <input type=submit ' .
             'name=ac_css_sel value="' . $ac_lang['style'] . '" ' .
             'title="' . $ac_lang['style_title'] . '"/>' . "\n";
    }

    //** Check emoji conversion
    if ($ac_emo_auto === 1) {
        echo '                    <input type=submit ' .
             'name=ac_emo_codes value="' . $ac_lang['emos'] . '" ' .
             'title="' . $ac_lang['emos_title'] . '"/>' . "\n";
    }

    //** Core navigation
    echo '                    <input type=submit name=ac_save ' .
         'value="' . $ac_lang['save'] . '" ' .
         'title="' . $ac_lang['save_title'] . '"/>' . "\n" .
         '                    <input type=submit name=ac_push ' .
         'value="' . $ac_lang['push'] . '" ' .
         'title="' . $ac_lang['push_title'] . '"/>' . "\n" .
         '                    <input type=submit name=ac_post ' .
         'value="' . $ac_lang['post'] . '" ' .
         'title="' . $ac_lang['post_title'] . '"/>' . "\n" .
         "                </div>\n" .
         "            </form>\n" .
         "            <div id=ac_stat>\n" .
         "                <div>$ac_stat</div>\n" .
         "                <noscript>" . $ac_lang['noscript'] .
         "</noscript>\n" .
         "            </div>\n" .
         "        </nav>\n";
} else {
    //** Check initial screen
    if (file_exists($ac_init)) {
        echo "        <div id=ac_push>\n";
        include "./" . $ac_init;
    }

    //** Login
    echo "        </div>\n" .
         "        <nav>\n" .
         '            <form action="#LOGIN" method=POST ' .
         'id=ac_login_form accept-charset="UTF-8">' . "\n" .
         "                <div>\n" .
         "                    <label for=ac_name>" . $ac_lang['name'] .
         "</label>\n" .
         '                    <input name=ac_name id=ac_name ' .
         'maxlength=16 title="' . $ac_lang['name_title'] . '"/>' . "\n" .
         '                    <input type=submit name=ac_login ' .
         'value="' . $ac_lang['login'] . '" title="' .
         $ac_lang['login_title'] . '"/>' . "\n" .
         "                </div>\n" .
         "            </form>\n" .
         "            <div id=ac_stat>\n" .
         "                <div>$ac_stat</div>\n" .
         "                <noscript>" . $ac_lang['noscript'] .
         "</noscript>\n" .
         "            </div>\n" .
         "        </nav>\n";
}

//** Footer
echo '        <footer><a href="https://github.com/phhpro/atomchat" ' .
     'title="' . $ac_lang['external'] . '">' .
     $ac_lang['powered_by'] . " PHP Atom Chat v$ac_version</a></footer>\n" .
     '        <script src="chat.js"></script>' . "\n" .
     "    </body>\n" .
     "</html>\n";
