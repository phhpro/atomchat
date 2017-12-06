<?php
/**
 * PHP Version 5 and above
 *
 * Atom Chat is a free PHP IRC like chat script with minimal bloat.
 *
 * Chat logs are stored in plain text files.
 * No database required.
 * Good to go using defaults.
 *
 * @category PHP_Chat_Scripts
 * @package  PHP_Atom_Chat
 * @author   P H Claus <phhpro@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @version  GIT: 20171206
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
 */


/**
 ***********************************************************************
 *                                                   BEGIN USER CONFIG *
 ***********************************************************************
 */


/**
 * Script folder
 */
$ac_fold     = "atomchat";

/**
 * CSS default theme
 * CSS theme selection by user -- 0 off, 1 on
 */
$ac_css_main = "grey";
$ac_css_user = 1;

/**
 * Auto-convert emos -- 0 off, 1 on
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
 * Auto expire inactive session -- default 1800 = 30 minutes
 */
$ac_max_char = 256;
$ac_kill     = 1800;


/**
 ***********************************************************************
 *                                                     END USER CONFIG *
 ***********************************************************************
 */


/**
 * Script version
 * Init info system
 * Stop message for invalid settings
 */
$ac_make = 20171206;
$ac_info = "Powered by Atom Chat v$ac_make";
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
?>
<p>Atom Chat requires session cookies!</p>
<p>Please edit your browser's cookie settings and then try again.</p>
<?php
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
?>
<p>Failed to create log dir!</p>
<p>Please make sure the script folder is writeable.</p>
<?php
    exit;
    }
}

/**
 * Log data file
 * User name lock
 * Live counter lock
 */
$ac_chat_data = "log/chat_" . $_SERVER['HTTP_HOST'] . "_" . date('Ymd') . ".html";
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
?>
<p>Missing CSS theme!</p>
<p><?php echo $ac_stop; ?></p>
<?php
    exit;
}

//** Check EMO folder
if (!is_dir("./emo/" . $ac_emo_icon)) {
?>
<p>Missing EMO icon set!</p>
<p><?php echo $ac_stop; ?></p>
<?php
    exit;
}

//** Check EMO config
if (!file_exists($ac_emo_conf)) {
?>
<p>Missing EMO configuration!</p>
<p><?php echo $ac_stop; ?></p>
<?php
    exit;
}

/**
 * Link EMO primary array
 * Link EMO secondary array
 * Link EMO code
 * Init live counter
 */
$ac_emo_parr = array ();
$ac_emo_sarr = array ();
$ac_emo_code = "";
$ac_live     = (int) file_get_contents($ac_lock_live);

//** Expire session
if (isset($_SESSION['ac_time']) && !empty($_SESSION['ac_time']) 
    && isset($_SESSION['ac_name']) && !empty($_SESSION['ac_name'])
) {
    $ac_time = (time()-(int) $_SESSION['ac_time']);
    $ac_diff = ($ac_kill-$ac_time);

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
        $ac_live_data = (int) file_get_contents($ac_lock_live);
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

    //** Check missing user name
    if ($ac_name === "") {
        header('Location: #MISSING_NAME');
        exit;
        //** Check valid characters
    } elseif (ctype_alpha($ac_name)) {
        //** Check if user name is available
        if (stripos(file_get_contents($ac_lock_name), $ac_name) !== false) {
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
            $ac_live_data = (int) file_get_contents($ac_lock_live);
            $ac_live_list = $ac_live_data;
            $ac_live_data = ($ac_live_list+1);

            file_put_contents($ac_lock_live, $ac_live_data);
            header('Location: #LOGIN');
            exit;
        }
    } else {
        header('Location: #INVALID_NAME');
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
    $ac_text  = "      <div class=ac_item>" . gmdate('Y-m-d H:i:s') . 
                " Atom Chat &#62; " . $_SESSION['ac_name'] . 
                " left the chat</div>\n";
    $ac_text .= file_get_contents($ac_chat_data);
    file_put_contents($ac_chat_data, $ac_text);

    //** Clear session
    unset($_SESSION['ac_time']);
    unset($_SESSION['ac_name']);

    //** Update live counter and load interface
    $ac_live_data = (int) file_get_contents($ac_lock_live);
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
            $ac_emo_sarr = array ();

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

            //** Reset EMO code
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
?>
<!DOCTYPE html>
<html lang="en-GB">
  <head>
    <title>Atom Chat - <?php echo $_SERVER['HTTP_HOST']; ?></title>
    <meta charset="UTF-8"/>
    <meta name=language content="en-GB"/>
    <meta name=description content="Atom Chat is a free PHP IRC like chat script"/>
    <meta name=keywords content="PHP Atom Chat,free PHP chat scripts"/>
    <meta name=robots content="noodp, noydir"/>
    <meta name=viewport content="width=device-width, height=device-height, initial-scale=1"/>
    <link rel=icon href="<?php echo $ac_host; ?>logo.png" type="image/png"/>
    <style>
<?php
readfile("./css/" . $ac_css_theme . ".css");
?>

@media screen and (max-width: 800px) {
  body {
    font-size: 115%;
  }
}
    </style>
  </head>
  <body>
    <div id=ac_head><span id=ac_logo><a href="https://github.com/phhpro/atomchat" title="Powered by Atom Chat. Click here to get it."><img src="<?php echo $ac_host; ?>logo.png" width=16 height=16 alt=""/> Atom Chat</a></span> <span id=ac_live>Online: <?php echo $ac_live; ?></span></div>
<?php
//** List CSS themes
if (isset($_POST['ac_csst'])) {
    $ac_css_trim = file_get_contents($ac_css_conf);

    //** Check empty config -- true if file has only BOM or spaces
    if (filesize($ac_css_conf) <16 && trim($ac_css_trim) === false) {
        $ac_info = "Empty CSS configuration! (Not checking empty lines)";
    } else {
        //** Link lines
        $ac_css_line = file($ac_css_conf);
?>
    <div id=ac_sub>
      <form action="#CHAT" id=ac_css_form method=POST accept-charset="UTF-8">
        <div>
          <select name=ac_css_list>
<?php
    //** Init CSS item
    $ac_css_item = "";

    //** Parse list and print items
foreach ($ac_css_line as $ac_css_item) {
    $ac_css_item = trim($ac_css_item);
    echo '            <option value="' . $ac_css_item . 
     '" title="Click here to select the ' . 
     ucwords($ac_css_item) . ' theme">' . ucwords($ac_css_item);

    //** Flag current theme
    if (isset($_SESSION['ac_css']) && $ac_css_item === $_SESSION['ac_css']) {
        echo " [x]";
    }

    echo "</option>\n";
}

    //** Reset CSS item
    unset($ac_css_item);
?>
          </select>
          <input name=ac_css_apply value=Apply title="Click here to apply the selected theme" type=submit />
          <input name=ac_css_close value=Close title="Click here to close this window" type=submit />
        </div>
      </form>
    </div>
<?php
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
        $ac_emo_parr = array ();
        $ac_emo_open = fopen($ac_emo_conf, "r");

        //** Parse list
        while (!feof($ac_emo_open)) {
            $ac_emo_line   = fgets($ac_emo_open);
            $ac_emo_line   = trim($ac_emo_line);
            $ac_emo_parr[] = $ac_emo_line;
        }

        fclose($ac_emo_open);
    }
?>
    <div id=ac_sub>
      <h1>Emoticon Conversion Table</h1>
      <p>The following icons are auto-converted for every match of their associated text smiley alternative, variant spelling, or natural keyword. Spelling is case insensitive, e.g. ABC, Abc, or abc all match.</p>
      <pre>
<?php
  //** Print list
foreach ($ac_emo_parr as $ac_emo_code) {
    $ac_emo_line   = explode("|", $ac_emo_code);
    $ac_emo_sarr[] = $ac_emo_line;
    $ac_emo_calt   = $ac_emo_line[0];
    $ac_emo_cvar   = $ac_emo_line[1];
    $ac_emo_ckey   = $ac_emo_line[2];
    echo '<img src="' . $ac_host . 'emo/' . $ac_emo_icon . '/' . $ac_emo_ckey . 
         '.' . $ac_emo_type . '" width=24 height=24 alt=""/> == ' . 
         "$ac_emo_calt -&#62;; $ac_emo_cvar -&#62; $ac_emo_ckey\n";
}

  //** Reset code
  unset($ac_emo_code);
?>
      </pre>
      <p><strong>Examples</strong></p>
      <p><code>psst santa has a gift for you :)</code><br/><img src="<?php echo $ac_host; ?>emo/<?php echo $ac_emo_icon; ?>/psst.<?php echo $ac_emo_type; ?>" width=24 height=24 alt=""/> <img src="<?php echo $ac_host; ?>emo/<?php echo $ac_emo_icon; ?>/santa.<?php echo $ac_emo_type; ?>" width=24 height=24 alt=""/> has a <img src="<?php echo $ac_host; ?>emo/<?php echo $ac_emo_icon; ?>/gift.<?php echo $ac_emo_type; ?>" width=24 height=24 alt=""/> for you <img src="<?php echo $ac_host; ?>emo/<?php echo $ac_emo_icon; ?>/smile.<?php echo $ac_emo_type; ?>" width=24 height=24 alt=""/></p>
      <p><code>i'm so :( i want to :*</code><br/>i'm so <img src="<?php echo $ac_host; ?>emo/<?php echo $ac_emo_icon; ?>/sad.<?php echo $ac_emo_type; ?>" width=24 height=24 alt=""/> i want to <img src="<?php echo $ac_host; ?>emo/<?php echo $ac_emo_icon; ?>/cry.<?php echo $ac_emo_type; ?>" width=24 height=24 alt=""/></p>
      <form action="#CHAT" id=ac_emo_form method=POST accept-charset="UTF-8">
        <div><input name=ac_emo_close value=Close title="Click here to close this window" type=submit /></div>
      </form>
    </div>
<?php
}

//** Check user name session
if (isset($_SESSION['ac_name']) && !empty($_SESSION['ac_name'])) {
?>
    <div id=ac_push>
<?php
  //** Check existing data file
if (file_exists($ac_chat_data) && is_writable($ac_chat_data)) {
    include $ac_chat_data;
} else {
    $ac_info = "Missing log or not writable!";
}
?>
    </div>
    <div id=ac_menu>
      <form action="#CHAT" method=POST id=ac_chat_form accept-charset="UTF-8">
        <div id=ac_char>Text <small>(<?php echo $ac_max_char; ?> characters)</small></div>
        <div>
          <textarea name=ac_text id=ac_text rows=4 cols=60 maxlength=<?php echo $ac_max_char; ?>" title="Type here to enter your message"></textarea>
        </div>
        <div>
          <input name=ac_name value="<?php echo $_SESSION['ac_name']; ?>" type=hidden />
          <input name=ac_quit value=Quit title="Click here to quit the current session" type=submit />
<?php
//** Check CSS user selection
if ($ac_css_user === 1) {
?>
          <input name=ac_csst value=Theme title="Click here to change the current theme" type=submit />
<?php
}

//** Check EMO conversion
if ($ac_emo_conv === 1) {
?>
          <input name=ac_emos value=Emos title="Click here to review all available emo codes" type=submit />
<?php
}
?>
          <input name=ac_save value=Save title="Click here to save the current session" type=submit />
          <input name=ac_push value=Push title="Click here to manually update the current session" type=submit />
          <input name=ac_post value=Post title="Click here to post your message" type=submit />
        </div>
      </form>
      <div id=ac_info>
        <?php echo $ac_info; ?><br/>
        <noscript>Java Script disabled or not available!</noscript>
      </div>
    </div>
<?php
} else {
?>
    <div id=ac_push>
      <h1>Welcome to <strong>Atom Chat</strong></h1>
      <ul>
        <li><strong>What it is</strong>
          <ul>
            <li>Completely anonymous. No passwords ever.</li>
            <li>Self-contained set it and forget it.</li>
            <li>Themeable responsive cross-browser design.</li>
          </ul>
        </li>
      </ul>
      <ul>
        <li><strong>What it is <em>not</em></strong>
          <ul>
            <li>Fancy hyperbole gadget with more bells than whistles.</li>
            <li>Resources leeching dependency stricken database voodoo.</li>
            <li>Neither a pink jellyfish nor Santa on steroids.</li>
          </ul>
        </li>
      </ul>
      <ul>
        <li><strong>How it works</strong>
          <ul>
            <li>Enter prefered name and press the <strong>Login</strong> button.</li>
            <li>Names are assigned dynamically. First come, first serve.</li>
            <li>Inactive sessions are auto-closed after 30 minutes.</li>
            <li>Optional smart conversion of text to icons.</li>
          </ul>
        </li>
      </ul>
      <ul>
        <li><strong>What it does</strong>
          <ul>
            <li><em>Chat, chat,</em> <strong>Atom Chat</strong>!</li>
          </ul>
        </li>
      </ul>
    </div>
    <div id=ac_menu>
      <form action="#LOGIN" method=POST id=ac_login_form accept-charset="UTF-8">
        <div>
          <label for=ac_name>Name <small>(A-Z only)</small></label>
          <input name=ac_name id=ac_name maxlength=16 title="Type here to enter your prefered user name. Alpha characters A to Z only!"/>
          <input name=ac_login value=Login title="Click here to login" type=submit />
        </div>
      </form>
      <div id=ac_info>
        <?php echo $ac_info; ?><br/>
        <noscript>Java Script disabled or not available!</noscript>
      </div>
    </div>
<?php
}
?>
    <script>
    //** Push helper
    var ac_http = null;
    var ac_init = 0;
    var ac_link, ac_rand, ac_res, ac_div = "";

    //** Configure object
    function ac_obj() {
        if (window.ActiveXObject) {
            return new ActiveXObject("Microsoft.XMLHTTP");
        } else if (window.XMLHttpRequest) {
            return new XMLHttpRequest();
        } else {
            alert("Your browser does not support AJAX!");
            return null;
        }
    }

    //** Container state
    function ac_set() {
        if (ac_http.readyState == 4) {
            ac_res           = ac_http.responseText;
            ac_div           = document.getElementById("ac_push");
            ac_div.innerHTML = ac_res;
            ac_div.scrollTop = ac_div.scrollHeight;
        }
    }

    //** Configure timer
    function ac_time() {
        ac_http = ac_obj();
        ac_rand = Math.floor(Math.random()*10000);

        if (ac_http != null) {
            ac_link = "?"+ac_rand;
            ac_http.open("GET", ac_link, true);
            ac_http.onreadystatechange = ac_set;
            ac_http.send(null);
        }
    }

    //** Update data file -- default 2 seconds
    function ac_push() {
        ac_time();
        ac_init = setTimeout("ac_push()", 2000);
    }

    //** Output beep if supported
    function ac_beep() {
        var ac_snd = new Audio("data:audio/wav;base64,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qJrx4KOQjahZxWbcZgztj2c49nKmkId44S71j0c8eV9yDK6uPRzx5X18eDvjvQ6yKo9ZSS6l//8elePK/Lf//IInrOF/FvDoADYAGBMGb7FtErm5MXMlmPAJQVgWta7Zx2go+8xJ0UiCb8LHHdftWyLJE0QIAIsI+UbXu67dZMjmgDGCGl1H+vpF4NSDckSIkk7Vd+sxEhBQMRU8j/12UIRhzSaUdQ+rQU5kGeFxm+hb1oh6pWWmv3uvmReDl0UnvtapVaIzo1jZbf/pD6ElLqSX+rUmOQNpJFa/r+sa4e/pBlAABoAAAAA3CUgShLdGIxsY7AUABPRrgCABdDuQ5GC7DqPQCgbbJUAoRSUj+NIEig0YfyWUho1VBBBA//uQZB4ABZx5zfMakeAAAAmwAAAAF5F3P0w9GtAAACfAAAAAwLhMDmAYWMgVEG1U0FIGCBgXBXAtfMH10000EEEEEECUBYln03TTTdNBDZopopYvrTTdNa325mImNg3TTPV9q3pmY0xoO6bv3r00y+IDGid/9aaaZTGMuj9mpu9Mpio1dXrr5HERTZSmqU36A3CumzN/9Robv/Xx4v9ijkSRSNLQhAWumap82WRSBUqXStV/YcS+XVLnSS+WLDroqArFkMEsAS+eWmrUzrO0oEmE40RlMZ5+ODIkAyKAGUwZ3mVKmcamcJnMW26MRPgUw6j+LkhyHGVGYjSUUKNpuJUQoOIAyDvEyG8S5yfK6dhZc0Tx1KI/gviKL6qvvFs1+bWtaz58uUNnryq6kt5RzOCkPWlVqVX2a/EEBUdU1KrXLf40GoiiFXK///qpoiDXrOgqDR38JB0bw7SoL+ZB9o1RCkQjQ2CBYZKd/+VJxZRRZlqSkKiws0WFxUyCwsKiMy7hUVFhIaCrNQsKkTIsLivwKKigsj8XYlwt/WKi2N4d//uQRCSAAjURNIHpMZBGYiaQPSYyAAABLAAAAAAAACWAAAAApUF/Mg+0aohSIRobBAsMlO//Kk4soosy1JSFRYWaLC4qZBYWFRGZdwqKiwkNBVmoWFSJkWFxX4FFRQWR+LsS4W/rFRb/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////VEFHAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAU291bmRib3kuZGUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMjAwNGh0dHA6Ly93d3cuc291bmRib3kuZGUAAAAAAAAAACU=");
        ac_snd.play();
    }

    //** Run functions
    ac_push();
    ac_beep();
    </script>
  </body>
</html>
