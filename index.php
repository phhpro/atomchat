<?php
/**
 * PHP Version 5 and above
 *
 * @category  PHP_Chat_Scripts
 * @package   PHP_Atom_Chat
 * @author    P H Claus <phhpro@gmail.com>
 * @copyright 2016 - 2018 P H Claus
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
 *
 *
 * Atom Chat is a free PHP IRC like chat script. No database required.
 */


/**
 ***********************************************************************
 *                                                   BEGIN USER CONFIG *
 ***********************************************************************
 */


/**
 * Script folder
 * Intro screen
 */
$ac_fold     = "demo/atomchat";
$ac_iscr     = "intro.html";

/**
 * CSS default theme
 * CSS theme selection by user -- 0 NO, 1 YES
 */
$ac_css_main = "grey";
$ac_css_user = 1;

/**
 * Auto-convert emos -- 0 NO, 1 YES
 * May bloat logs and put extra load on server
 */
$ac_emo_conv = 1;

/**
 * EMO default icon set and file type
 * Only applies if $ac_emo_conv = 1
 */
$ac_emo_icon = "default";
$ac_emo_type = "png";

/**
 * Maximum characters allowed per post
 * Expire inactive session after n minutes -- default 30 = 1800 s
 */
$ac_max_char = 256;
$ac_kill     = 1800;

/**
 * Update screen every n seconds -- default 2 = 2000 ms
 * 3 to 5 seconds may be more suitable to reduce server load but
 * likely to confuse users because of visible lag after posting.
 */
$ac_push = 2000;


/**
 ***********************************************************************
 *                                                     END USER CONFIG *
 ***********************************************************************
 */


/**
 * Script version
 * Script download
 */
$ac_make = "20180103";
$ac_down = '        <div id=ac_down>Powered by ' .
           '<a href="https://github.com/phhpro/atomchat" ' .
           'title="Click here to get a free copy of this script">' .
           'PHP Atom Chat v' . $ac_make . "</a></div>\n";

/**
 * Init info text
 * Stop message to check settings
 */
$ac_info = "";
$ac_stop = "Please check your settings.";

//** Protocol
$ac_prot = "http";

if (isset($_SERVER['HTTPS']) && "on" === $_SERVER['HTTPS']) {
    $ac_prot = $ac_prot . "s";
}

//** URL reference
$ac_host = $ac_prot . "://" . $_SERVER['HTTP_HOST'] . "/" . $ac_fold . "/";

//** Init and test session
session_start();
$_SESSION['ac_test'] = 1;

if ($_SESSION['ac_test'] !== 1) {
    echo "<p>Atom Chat requires session cookies!</p>\n" .
         "<p>Please edit your browser's cookie settings and then try again.</p>\n";
    exit;
} else {
    unset($_SESSION['ac_test']);
}

//** Try to prevent caching
header('Expires: on, 01 Jan 1970 00:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

//** Check log folder
if (!is_dir('log')) {

    if (mkdir('log') === false) {
        echo "<p>Failed to create log dir!</p>\n" .
             "<p>Please make sure the script folder is writeable.</p>\n";
        exit;
    }
}

/**
 * Log data file
 * User name lock
 * Live counter lock
 */
$ac_chat_data = "./log/chat_" . $_SERVER['HTTP_HOST'] . "_" . date('Ymd') . ".html";
$ac_lock_name = "name.lock";
$ac_lock_live = "live.lock";

//** Check name lock
if (!file_exists($ac_lock_name)) {
    $ac_lock_hand = fopen($ac_lock_name, "w");
    fwrite($ac_lock_hand, "");
    fclose($ac_lock_hand);
}

//** Check live counter lock
if (!file_exists($ac_lock_live)) {
    $ac_lock_hand = fopen($ac_lock_live, "w");
    fwrite($ac_lock_hand, 0);
    fclose($ac_lock_hand);
}

//** Link CSS and EMO config
$ac_css_conf = "./css/__config.txt";
$ac_emo_conf = "./emo/" . $ac_emo_icon . "/__config.txt";

//** Check CSS theme
if (!file_exists("./css/" . $ac_css_main . ".css")) {
    echo "<p>Missing CSS theme!</p>\n" .
         "<p>$ac_stop</p>\n";
    exit;
}

//** Check EMO folder
if (!is_dir("./emo/" . $ac_emo_icon)) {
    echo "<p>Missing EMO icon set!</p>\n" .
         "<p>$ac_stop</p>\n";
    exit;
}

//** Check EMO config
if (!file_exists($ac_emo_conf)) {
    echo "<p>Missing EMO configuration!</p>\n" .
         "<p>$ac_stop</p>\n";
    exit;
}

/**
 * Link EMO primary array
 * Link EMO secondary array
 * Link EMO code
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
    $ac_diff = ((int)$ac_kill-(int)$ac_time);

    if ($ac_diff <= 0) {
        //** Update user name lock
        file_put_contents(
            $ac_lock_name, str_replace(
                $_SESSION['ac_name'] . 
                "\n", "", file_get_contents($ac_lock_name)
            )
        );

        //** Update data file
        $ac_text  = "      <div class=ac_item>" . gmdate('Y-m-d H:i:s') . 
                    " Atom Chat &#62; " . $_SESSION['ac_name'] . 
                    " left the chat</div>\n";
        $ac_text .= file_get_contents($ac_chat_data);
        file_put_contents($ac_chat_data, $ac_text);

        //** Clear session
        unset($_SESSION['ac_time']);
        unset($_SESSION['ac_name']);

        //** Update live counter and load interface
        $ac_live_data = (int)file_get_contents($ac_lock_live);
        $ac_live_list = $ac_live_data;

        if ($ac_live_list >1) {
            $ac_live_data = ((int)$ac_live_list-1);
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

    //** Check missing user name
    if ($ac_name === "") {
        header('Location: #MISSING_NAME');
        exit;
        //** Check if user name is available
    } elseif (stripos(file_get_contents($ac_lock_name), $ac_name) !== false) {
        header('Location: #NAME_NOT_AVAILABLE');
        exit;
    } else {
        //** Init session
        $_SESSION['ac_time'] = time();
        $_SESSION['ac_name'] = $ac_name;

        //** Lock user name and update data file
        file_put_contents($ac_lock_name, $ac_name . "\n", FILE_APPEND);
        $ac_text  = "      <div class=ac_item>" . gmdate('Y-m-d H:i:s') . 
                    " Atom Chat &#62; " . $_SESSION['ac_name'] . 
                    " entered the chat</div>\n";

        if (file_exists($ac_chat_data)) {
            $ac_text .= file_get_contents($ac_chat_data);
        }

        file_put_contents($ac_chat_data, $ac_text);

        //** Update live counter and reload interface
        $ac_live_data = (int)file_get_contents($ac_lock_live);
        $ac_live_list = $ac_live_data;
        $ac_live_data = ((int)$ac_live_list+1);
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
            $_SESSION['ac_name'] . "\n", "", file_get_contents($ac_lock_name)
        )
    );

    //** Update data file
    $ac_text  = "      <div class=ac_item>" . gmdate('Y-m-d H:i:s') . 
                " Atom Chat &#62; " . $_SESSION['ac_name'] . 
                " left the chat</div>\n";
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
        $ac_live_data = ((int)$ac_live_list-1);
    }

    file_put_contents($ac_lock_live, $ac_live_data);
    header('Location: #LOGOUT');
    exit;
}

//** Manual update
if (isset($_POST['ac_push'])) {
    header('Location: #MANUAL_UPDATE');
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

        //** Check EMO conversion
        if ($ac_emo_conv === 1) {

            //** Check empty config -- true if file has only BOM or spaces
            $ac_emo_trim = file_get_contents($ac_emo_conf);

            if (filesize($ac_emo_conf) <16 && trim($ac_emo_trim) === false) {
                $ac_info = "Empty EMO configuration!";
            } else {
                //** Link primary array and config
                $ac_emo_open = fopen($ac_emo_conf, "r");

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
                $ac_emo_cvar   = $ac_emo_line[1];
                $ac_emo_ckey   = $ac_emo_line[2];

                //** Alternate to keyword -- word -> wOrDs -> SWORDS
                if (stripos($ac_text, $ac_emo_calt) !== false) {
                    $ac_text = str_replace($ac_emo_calt, $ac_emo_ckey, $ac_text);
                }

                //** Variant to keyword
                if (stripos($ac_text, $ac_emo_cvar) !== false) {
                    $ac_text = str_replace($ac_emo_cvar, $ac_emo_ckey, $ac_text);
                }

                //** Keyword to icon
                if (stripos($ac_text, $ac_emo_ckey) !== false) {
                    $ac_text = str_replace(
                        $ac_emo_ckey, '<img src="' . $ac_host . 'emo/' . 
                        $ac_emo_icon . '/' . $ac_emo_ckey . '.' . 
                        $ac_emo_type . '" width=24 height=24 alt="' . 
                        $ac_emo_ckey . '"/>', $ac_text
                    );
                }
            }

            unset($ac_emo_code);
        }

        //** Update data file
        $ac_text  = '      <div id="' . gethostbyaddr(
            $_SERVER['REMOTE_ADDR']
        ) . '_' . gmdate('Ymd-His') . '_' . 
            $ac_name . '" class=ac_item>' . gmdate('Y-m-d H:i:s') . " " . 
            $ac_name . " &#62; " . 
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

//** Link CSS theme
if (isset($_POST['ac_css_apply'])) {
    $ac_css_conf = htmlentities($_POST['ac_css_list'], ENT_QUOTES, "UTF-8");

    //** Link selected theme
    if ($ac_css_conf !== "") {
        $_SESSION['ac_css'] = $ac_css_conf;
    }
}

//** Check CSS session and apply theme
if (isset($_SESSION['ac_css'])) {
    $ac_css_theme = $_SESSION['ac_css'];
} else {
    //** Default CSS theme
    $ac_css_theme = $ac_css_main;
}

//** Print header
echo "<!DOCTYPE html>\n" .
     '<html lang="en-GB">' . "\n" .
     "  <head>\n" .
     "    <title>Atom Chat - " . $_SERVER['HTTP_HOST'] . "</title>\n" .
     '    <meta charset="UTF-8"/>' . "\n" .
     '    <meta name=language content="en-GB"/>' . "\n" .
     '    <meta name=description ' .
     'content="Atom Chat is a free PHP IRC like chat script"/>' . "\n" .
     '    <meta name=keywords ' .
     'content="PHP Atom Chat,free PHP chat scripts"/>' . "\n" .
     '    <meta name=robots content="noodp, noydir"/>' . "\n" .
     '    <meta name=viewport ' .
     'content="width=device-width, height=device-height, ' .
     'initial-scale=1"/>' . "\n" .
     '    <link rel=icon href="' . $ac_host . 'logo.png" ' .
     'type="image/png"/>' . "\n" .
     '    <link rel=stylesheet ' .
     'href="' . $ac_host . 'css/' . $ac_css_theme . '.css" ' .
     'type="text/css"/>' . "\n" .
     "  </head>\n" .
     "  <body>\n" .
     '    <div id=ac_head><span id=ac_logo><img src="' . $ac_host .
     'logo.png" width=16 height=16 alt=""/> Atom Chat</span> ' .
     '<span id=ac_live> Live: ' . $ac_live . '</span></div>' . "\n";

//** List CSS themes
if (isset($_POST['ac_csst'])) {
    $ac_css_trim = file_get_contents($ac_css_conf);

    //** Check empty config -- true if file has only BOM or spaces
    if (filesize($ac_css_conf) <16 && trim($ac_css_trim) === false) {
        $ac_info = "Empty CSS configuration! (Not checking empty lines)";
    } else {
        $ac_css_line = file($ac_css_conf);
        echo "    <div id=ac_sub>\n" .
             '      <form action="#CHAT" id=ac_css_form method=POST ' .
             'accept-charset="UTF-8">' . "\n" .
             "        <div>\n" .
             "          <select name=ac_css_list>\n";

        //** Init CSS item
        $ac_css_item = "";

        //** Parse list and print items
        foreach ($ac_css_line as $ac_css_item) {
            $ac_css_item = trim($ac_css_item);
            echo '            <option value="' . $ac_css_item . 
                 '" title="Click here to select the ' . 
            ucwords($ac_css_item) . ' theme">' . ucwords($ac_css_item);

            //** Flag current theme
            if (isset($_SESSION['ac_css'])
                && $ac_css_item === $_SESSION['ac_css']
            ) {
                echo " [x]";
            }

            echo "</option>\n";
        }

        unset($ac_css_item);
        echo "          </select>\n" .
             '          <input type=submit name=ac_css_apply value=Apply ' .
             'title="Click here to apply the selected theme"/>' . "\n" .
             '          <input type=submit name=ac_css_close value=Close ' .
             'title="Click here to close this window"/>' . "\n" .
             "        </div>\n" .
             "      </form>\n" .
             "    </div>\n";
    }
}

//** List EMO conversion
if (isset($_POST['ac_emos'])) {
    $ac_emo_trim = file_get_contents($ac_emo_conf);

    //** Check empty config -- true if file has only BOM or spaces
    if (filesize($ac_emo_conf) <16 && trim($ac_emo_trim) === false) {
        $ac_info = "Empty EMO configuration! (Not checking empty lines)";
    } else {
        //** Link primary array and config
        $ac_emo_parr = array();
        $ac_emo_open = fopen($ac_emo_conf, "r");

        //** Parse list
        while (!feof($ac_emo_open)) {
            $ac_emo_line   = fgets($ac_emo_open);
            $ac_emo_line   = trim($ac_emo_line);
            $ac_emo_parr[] = $ac_emo_line;
        }

        fclose($ac_emo_open);
    }

    echo "    <div id=ac_sub>\n" .
         "      <h1>Emoticon Conversion Table</h1>\n" .
         "      <p>The following text alternatives, variants, and " .
         "keywords are converted to icons. Spelling is case " .
         "insensitive, e.g. ABC, Abc, or abc all match.</p>\n" .
         "      <pre>\n";

    //** Print list
    foreach ($ac_emo_parr as $ac_emo_code) {
        $ac_emo_line   = explode("|", $ac_emo_code);
        $ac_emo_sarr[] = $ac_emo_line;
        $ac_emo_calt   = htmlentities($ac_emo_line[0]);
        $ac_emo_cvar   = $ac_emo_line[1];
        $ac_emo_ckey   = $ac_emo_line[2];
        echo '<img src="' . $ac_host . 'emo/' . $ac_emo_icon . '/' .
             $ac_emo_ckey . '.' . $ac_emo_type .
            '" width=24 height=24 alt=""/> == ' . $ac_emo_calt . ' | ' .
            $ac_emo_cvar . ' | ' . $ac_emo_ckey . "\n";
    }

    unset($ac_emo_code);
    echo "      </pre>\n" .
         "      <p><strong>Examples</strong></p>\n" .
         "      <p>\n" .
         "        <code>psst santa has a gift for you :)</code><br/>\n" .
         '        <img src="' . $ac_host . 'emo/' . $ac_emo_icon . '/psst.' .
         $ac_emo_type . '" width=24 height=24 alt=""/> <img src="' .
         $ac_host . 'emo/' . $ac_emo_icon . '/santa.' . $ac_emo_type .
         '" width=24 height=24 alt=""/> has a <img src="' . $ac_host .
         'emo/' . $ac_emo_icon . '/gift.' . $ac_emo_type .
         '" width=24 height=24 alt=""/> for you <img src="' . $ac_host .
         'emo/' . $ac_emo_icon . '/smile.' . $ac_emo_type .
         '" width=24 height=24 alt=""/>' . "\n" .
         "      </p>\n" .
         "      <p>\n" .
         "        <code>i am so :( i want to :*</code><br/>\n" .
         '        i am so <img src="' . $ac_host . 'emo/' . $ac_emo_icon .
         '/sad.' . $ac_emo_type . '" width=24 height=24 alt=""/> i want to ' .
         '<img src="' . $ac_host . 'emo/' . $ac_emo_icon . '/cry.' .
         $ac_emo_type . '" width=24 height=24 alt=""/>' . "\n" .
         "      </p>\n" .
         '      <form action="#CHAT" id=ac_emo_form method=POST ' .
         'accept-charset="UTF-8">' . "\n" .
         "        <div>\n" .
         '          <input type=submit name=ac_emo_close value=Close ' .
         'title="Click here to close this window"/>' . "\n" .
         "        </div>\n" .
         "      </form>\n" .
         "    </div>\n";
}

//** Check user name session
if (isset($_SESSION['ac_name']) && !empty($_SESSION['ac_name'])) {
    echo "    <div id=ac_push>\n";

    //** Check existing data file
    if (file_exists($ac_chat_data)) {
        include $ac_chat_data;
    } else {
        $ac_info = "Empty log file!";
    }

    echo "    </div>\n" .
         "    <div id=ac_menu>\n" .
         '      <form action="#CHAT" method=POST id=ac_chat_form ' .
         'accept-charset="UTF-8">' . "\n" .
         "        <div id=ac_char>Text " .
         "<small>($ac_max_char characters)</small></div>\n" .
         "        <div>\n" .
         '          <textarea name=ac_text id=ac_text rows=4 cols=60 ' .
         'maxlength=' . $ac_max_char .
         ' title="Type here to enter your message"></textarea>' . "\n" .
         "        </div>\n" .
         "        <div>\n" .
         '          <input type=hidden name=ac_name ' .
         'value="' . $_SESSION['ac_name'] . '"/>' . "\n" .
         "          <input type=submit name=ac_quit value=Quit " .
         'title="Click here to quit the session"/>' . "\n";

    //** Check CSS user selection
    if ($ac_css_user === 1) {
        echo '          <input type=submit name=ac_csst value=Theme ' .
             'title="Click here to change the current theme"/>' . "\n";
    }

    //** Check EMO conversion
    if ($ac_emo_conv === 1) {
        echo "          <input type=submit name=ac_emos value=Emos " .
             'title="Click here to review all available emo codes"/>' . "\n";
    }

    echo '          <input type=submit name=ac_save value=Save ' .
         'title="Click here to save the session"/>' . "\n" .
        '          <input type=submit name=ac_push value=Push ' .
         'title="Click here to manually update the session"/>' . "\n" .
         '          <input type=submit name=ac_post value=Post ' .
         'title="Click here to post your message"/>' . "\n" .
        "        </div>\n" .
        $ac_down . 
        "      </form>\n" .
        "      <div id=ac_info>\n" .
        "        $ac_info<br/>\n" .
        "        <noscript>JavaScript disabled or not available!</noscript>\n" .
        "      </div>\n" .
        "    </div>\n";
} else {

    //** Load intro screen
    if (file_exists($ac_iscr)) {
        echo "    <div id=ac_push>\n";
        include "./" . $ac_iscr;
    }

    echo "    <div id=ac_menu>\n" .
         '      <form action="#LOGIN" method=POST id=ac_login_form ' .
         'accept-charset="UTF-8">' . "\n" .
         "        <div>\n" .
         "          <label for=ac_name>Name</label>\n" .
         "          <input name=ac_name id=ac_name maxlength=16 " .
         'title="Type here to enter your user name."/>' . "\n" .
         "          <input type=submit name=ac_login value=Login " .
         'title="Click here to login"/>' . "\n" .
         "        </div>\n" .
         $ac_down . 
         "      </form>\n" .
         "      <div id=ac_info>\n" .
         "        $ac_info<br/>\n" .
         "        <noscript>JavaScript disabled or not available!</noscript>\n" .
         "      </div>\n" .
         "    </div>\n";
}

echo '    <script src="atomchat.js"></script>' . "\n" .
     "  </body>\n" .
     "</html>\n";
