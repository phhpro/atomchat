<?php
//** script folder and max characters
$ac_dir = "/chat/";
$ac_max = 256;

//** init session
session_start();

//** try to prevent caching -- optional
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

//** check log folder
if (!is_dir('log')) {
  mkdir('log');
}

//** log file, users online lock file and counter
$ac_log = "log/ac_log_" . date('Ymd') . ".html";
$ac_uol = "ac_lock.txt";
$ac_uoc = "ac_count.txt";

//** init users online counter
if (!file_exists($ac_uol)) {
  $ac_uon = 0;
} else {
  $ac_uon = file_get_contents($ac_uoc);
}

//** expire session after 30 minutes
if (isset ($_SESSION['ac_time'])) {
  $ac_dif = (1800-(time()-$_SESSION['ac_time']));

  if ($ac_dif <= 0) {
    //** update users online data file
    file_put_contents($ac_uol, str_replace($_SESSION['ac_name'] . "\n", "", file_get_contents($ac_uol)));

    //** update log file
    $ac_text  = '      <div class=ac_item><code>' . gmdate('Y-m-d H:i:s') . " Atom Chat: " . $_SESSION['ac_name'] . " left the chat</code></div>\n";
    $ac_text .= file_get_contents($ac_log);
    file_put_contents($ac_log, $ac_text);

    //** clear session
    unset ($_SESSION['ac_time']);
    unset ($_SESSION['ac_name']);

    //** update users online counter and load interface
    $ac_on_cur = file_get_contents($ac_uoc);
    $ac_on_val = $ac_on_cur;

    if ($ac_on_val <1) {
      $ac_on_cur = 0;
    } else {
      $ac_on_cur = ($ac_on_val-1);
    }

    file_put_contents($ac_uoc, $ac_on_cur);
    header("Location: #SESSION_EXPIRED");
    exit;
  }
} else {
  $_SESSION['ac_time'] = time();
}

//** login
if (isset ($_POST['ac_login'])) {
  $ac_name = htmlentities($_POST['ac_name'], ENT_QUOTES, "UTF-8");

  //** check missing name
  if ($ac_name === "") {
    header("Location: #MISSING_NAME");
    exit;
  } else {
    //** check if name is available
    if (stripos(file_get_contents($ac_uol), $ac_name) !== false) {
      header("Location: #NAME_NOT_AVAILABLE");
      exit;
    } else {
      //** init session
      $_SESSION['ac_time'] = time();
      $_SESSION['ac_name'] = $ac_name;

      //** add name to lock file
      file_put_contents($ac_uol, $ac_name . "\n", FILE_APPEND);

      //** update log file
      $ac_text  = '      <div class=ac_item><code>' . gmdate('Y-m-d H:i:s') . " Atom Chat: " . $_SESSION['ac_name'] . " entered the chat</code></div>\n";

      if (file_exists($ac_log)) {
        $ac_text .= file_get_contents($ac_log);
      }

      file_put_contents($ac_log, $ac_text);

      //** update users online counter and load interface
      $ac_on_cur = file_get_contents($ac_uoc);
      $ac_on_val = $ac_on_cur;
      $ac_on_cur = ($ac_on_val+1);
      file_put_contents($ac_uoc, $ac_on_cur);
      header("Location: #LOGIN");
      exit;
    }
  }
}

//** save data file
if (isset ($_POST['ac_save'])) {
  header('Content-type: text/html');
  header('Content-Disposition: attachment; filename="atomchat_' . $ac_log . '"');
  readfile($ac_log);
  exit;
}

//** quit session
if (isset ($_POST['ac_quit'])) {

  //** update users online data file
  file_put_contents($ac_uol, str_replace($_SESSION['ac_name'] . "\n", "", file_get_contents($ac_uol)));

  //** update log file
  $ac_text  = '      <div class=ac_item><code>' . gmdate('Y-m-d H:i:s') . " Atom Chat: " . $_SESSION['ac_name'] . " left the chat</code></div>\n";
  $ac_text .= file_get_contents($ac_log);
  file_put_contents($ac_log, $ac_text);

  //** clear session
  unset ($_SESSION['ac_time']);
  unset ($_SESSION['ac_name']);

  //** update users online counter and load interface
  $ac_on_cur = file_get_contents($ac_uoc);
  $ac_on_val = $ac_on_cur;

  if ($ac_on_val <1) {
    $ac_on_cur = 0;
  } else {
    $ac_on_cur = ($ac_on_val-1);
  }

  file_put_contents($ac_uoc, $ac_on_cur);
  header("Location: #LOGOUT");
  exit;
}

//** manual update
if (isset ($_POST['ac_push'])) {
  header("Location: #FORCE_UPDATE");
  exit;
}

//** new entry
if (isset ($_POST['ac_post'])) {
  $ac_name = htmlentities($_POST['ac_name'], ENT_QUOTES, "UTF-8");
  $ac_text = filter_var($_POST['ac_text'], FILTER_SANITIZE_SPECIAL_CHARS);

  //** skip empty post
  if ($ac_name !== "" || $ac_text !== "") {

    if (!file_exists($ac_log)) {
      file_put_contents($ac_log, $ac_link);
    }

    //** add post to log file
    $ac_text  = '      <div id="' . gethostbyaddr($_SERVER['REMOTE_ADDR']) . '_' . gmdate('Ymd-His') . '_' . $ac_name . '" class=ac_item>' . gmdate('Y-m-d H:i:s') . " " . $ac_name . " :: " . str_replace("&#13;&#10;", "", $ac_text) . "</div>\n";
    $ac_text .= file_get_contents($ac_log);
    file_put_contents($ac_log, $ac_text);
    header("Location: #NEW_POST");
    exit;
  }
}

//** script version
$ac_ver = 20170614;
?>
<!DOCTYPE html>
<html lang=en-GB>
  <head>
    <title>Atom Chat</title>
    <meta charset=UTF-8 />
    <meta name=language content=en-GB />
    <meta name=description content="Atom Chat free PHP chat script"/>
    <meta name=keywords content="Atom Chat"/>
    <meta name=robots content="noodp, noydir"/>
    <meta name=viewport content="width=device-width, height=device-height, initial-scale=1"/>
    <link rel=icon href="ac_logo.png" type="image/png"/>
    <style>
    body {
      background-color: #ccc;
      color: #000;
      font-family: "Droid Sans", Arial, sans-serif;
      height: 1024px;
    }

    #ac_header {
      font-weight: bold;
      background-color: #666;
      color: #ccc;
      font-weight: bold;
      padding: 2px;
      position: fixed;
      top: 0;
      right: 0;
      left: 0;
    }

    #ac_push {
      padding-top: 8px;
    }

    #ac_menu {
      background-color: #666;
      color: #ccc;
      text-align: center;
      padding: 2px;
      position: fixed;
      bottom: 0;
      right: 0;
      left: 0;
    }

    #ac_logo {
      float: left;
      margin: 2px 2px 2px 4px;
    }

    #ac_logo img {
      border: 0;
      font-size: 85%;
      vertical-align: text-top;
      margin-top: -1px;
    }

    #ac_stat {
      float: right;
      font-weight: normal;
      margin: 2px 4px 2px 2px;
    }

    #ac_footer {
      font-size: small;
    }

    label {
      font-weight: bold;
    }

    input {
      font-size: 100%;
      background-color: #999;
      color: #000;
      border: 1px outset #666;
    }

    input:hover {
      background-color: #ccc;
      color: #000;
      border: 1px inset #666;
    }

    input[type=submit] {
      font-weight: bold;
      margin: 16px;
    }

    textarea {
      background-color: #999;
      color: #000;
      border: 1px solid #666;
      width: 99%;
    }

    textarea:hover {
      background-color: #ccc;
      color: #000;
      border: 1px inset #666;
    }

    .ac_item {
      border-bottom: 1px solid #666;
      padding: 8px;
    }

    .ac_item:hover {
      background-color: #999;
      color: #000;
    }

    .ac_item code {
      background-color: transparent;
      color: #333;
    }

    a {
      background-color: transparent;
      color: #ccc;
      text-decoration: none;
    }

    a:hover {
      background-color: transparent;
      color: #fff;
    }

    @media screen and (max-width: 800px) {
      body {
        font-size: 115%;
      }
    }
    </style>
  </head>
  <body>
    <div id=ac_header><span id=ac_logo><a href="http://phclaus.com/php-scripts/#AtomChat" title="Powered by Atom Chat v<?php echo $ac_ver; ?>. Click here to get a free copy."><img src="ac_logo.png" width=16 height=16 alt=""/> Atom Chat</span></a> <span id=ac_stat>Online: <span id=ac_uoc><?php echo $ac_uon; ?></span></span></div>
    <p><strong><noscript>Java Script disabled or not available!</noscript></strong></p>
<?php
if (isset ($_SESSION['ac_name'])) {
?>
    <div id=ac_push>
<?php
  //** check existing log file
  if (file_exists($ac_log)) {
    include ($ac_log);
  } else {
?>
      <p>It would seem you are the first today.</p>
      <p>Quick, chat away the first line to claim the top spot!</p>
<?php
  }
?>
    </div>
    <div id=ac_menu>
      <form action="#CHAT" method=POST id=ac_chat accept-charset=UTF-8>
        <div id=ac_char>Text <small>(<?php echo $ac_max; ?> characters maximum)</small></div>
        <div>
          <textarea name=ac_text rows=4 cols=60 maxlength=<?php echo $ac_max; ?> title="Type here to enter your message"></textarea>
        </div>
        <div>
          <input name=ac_name value="<?php echo $_SESSION['ac_name']; ?>" type=hidden />
          <input name=ac_quit value=Quit title="Click here to quit the current session" type=submit />
          <input name=ac_save value=Save title="Click here to save the current session" type=submit />
          <input name=ac_push value=Push title="Click here to manually update the current session" type=submit />
          <input name=ac_post value=Post title="Click here to post your message" type=submit />
        </div>
      </form>
    </div>
<?php
} else {
?>
    <div id=ac_push>
      <h1>Welcome to <strong>Atom Chat</strong></h1>
      <ul>
        <li><strong>What it is</strong>
          <ul>
            <li>Completely anonymous no thrills single file chat script.</li>
            <li>Self-contained set it and forget it upload and done.</li>
            <li>Never again forgotten passwords. Because there are none!</li>
            <li>Zero data tracking. Just pure fun for the chat of it.</li>
          </ul>
        </li>
      </ul>
      <ul>
        <li><strong>What it is <em>not</em></strong>
          <ul>
            <li>Hyperbole fancy gadget with more bells than whistles.</li>
            <li>Resources leeching dependency stricken database voodoo.</li>
            <li>Neither a pink jellyfish nor Santa Claus on steroids.</li>
          </ul>
        </li>
      </ul>
      <ul>
        <li><strong>How it works</strong>
          <ul>
            <li>Enter desired user name and click <strong>Login</strong> to start chatting.</li>
            <li>Names are dynamically assigned. First come, first serve.</li>
            <li>Inactive sessions are auto-closed after 30 minutes.</li>
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
      <form action="#LOGIN" method=POST id=ac_login accept-charset=UTF-8>
        <div>
          <label for=ac_name>Name</label>
          <input name=ac_name id=ac_name maxlength=16 title="Type here to enter your desired user name"/>
          <input name=ac_login value=Login title="Click here to login" type=submit />
        </div>
      </form>
    </div>
<?php
}
?>
    <script>
    //** push helper
    var ac_http = null;
    var ac_init = 0;
    var ac_link, ac_rand, ac_res, ac_div  = "";

    //** configure object
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

    //** container state
    function ac_set() {

      if (ac_http.readyState == 4) {
        ac_res           = ac_http.responseText;
        ac_div           = document.getElementById("ac_push");
        ac_div.innerHTML = ac_res;
        ac_div.scrollTop = ac_div.scrollHeight;
      }
    }

    //** configure timer
    function ac_time() {
      ac_http = ac_obj();
      ac_rand = Math.floor(Math.random()*10000);

      if (ac_http != null) {
        ac_link = "?" + ac_rand;
        ac_http.open("GET", ac_link, true);
        ac_http.onreadystatechange = ac_set;
        ac_http.send(null);
      }
    }

    //** update every 2 seconds
    function ac_push() {
      ac_time();
      ac_init = setTimeout("ac_push()", 2000);
    }

    //** output beep
    var ac_beep = (function beep() {
      var ac_snd = new Audio("data:audio/wav;base64,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qJrx4KOQjahZxWbcZgztj2c49nKmkId44S71j0c8eV9yDK6uPRzx5X18eDvjvQ6yKo9ZSS6l//8elePK/Lf//IInrOF/FvDoADYAGBMGb7FtErm5MXMlmPAJQVgWta7Zx2go+8xJ0UiCb8LHHdftWyLJE0QIAIsI+UbXu67dZMjmgDGCGl1H+vpF4NSDckSIkk7Vd+sxEhBQMRU8j/12UIRhzSaUdQ+rQU5kGeFxm+hb1oh6pWWmv3uvmReDl0UnvtapVaIzo1jZbf/pD6ElLqSX+rUmOQNpJFa/r+sa4e/pBlAABoAAAAA3CUgShLdGIxsY7AUABPRrgCABdDuQ5GC7DqPQCgbbJUAoRSUj+NIEig0YfyWUho1VBBBA//uQZB4ABZx5zfMakeAAAAmwAAAAF5F3P0w9GtAAACfAAAAAwLhMDmAYWMgVEG1U0FIGCBgXBXAtfMH10000EEEEEECUBYln03TTTdNBDZopopYvrTTdNa325mImNg3TTPV9q3pmY0xoO6bv3r00y+IDGid/9aaaZTGMuj9mpu9Mpio1dXrr5HERTZSmqU36A3CumzN/9Robv/Xx4v9ijkSRSNLQhAWumap82WRSBUqXStV/YcS+XVLnSS+WLDroqArFkMEsAS+eWmrUzrO0oEmE40RlMZ5+ODIkAyKAGUwZ3mVKmcamcJnMW26MRPgUw6j+LkhyHGVGYjSUUKNpuJUQoOIAyDvEyG8S5yfK6dhZc0Tx1KI/gviKL6qvvFs1+bWtaz58uUNnryq6kt5RzOCkPWlVqVX2a/EEBUdU1KrXLf40GoiiFXK///qpoiDXrOgqDR38JB0bw7SoL+ZB9o1RCkQjQ2CBYZKd/+VJxZRRZlqSkKiws0WFxUyCwsKiMy7hUVFhIaCrNQsKkTIsLivwKKigsj8XYlwt/WKi2N4d//uQRCSAAjURNIHpMZBGYiaQPSYyAAABLAAAAAAAACWAAAAApUF/Mg+0aohSIRobBAsMlO//Kk4soosy1JSFRYWaLC4qZBYWFRGZdwqKiwkNBVmoWFSJkWFxX4FFRQWR+LsS4W/rFRb/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////VEFHAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAU291bmRib3kuZGUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMjAwNGh0dHA6Ly93d3cuc291bmRib3kuZGUAAAAAAAAAACU=");

      return function() {
        ac_snd.play();
      }
    })();

    //** exec functions
    ac_push();
    ac_beep();
    </script>
  </body>
</html>
