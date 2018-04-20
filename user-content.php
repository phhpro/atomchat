<?php
/**
 * PHP Version 5 and above
 *
 * User content upload handler
 *
 * @category  PHP_Chat_Scripts
 * @package   PHP_Atom_Chat
 * @author    P H Claus <phhpro@gmail.com>
 * @copyright 2015 - 2018 P H Claus
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @version   GIT: Latest
 * @link      https://github.com/phhpro/atomchat
 */


/**
 * Last modified
 * Link file
 * Link type
 * Init error status
 */
$last = 20180420;
$file = $save . "/" . basename($_FILES['file']['name']);
$type = strtolower(pathinfo($file, PATHINFO_EXTENSION));
$fail = 1;

//** Check valid image
if (isset($_POST['upload'])) {
    $mime = getimagesize($_FILES['file']['tmp_name']);

    //** Check if file exists
    if (file_exists($file)) {
        $stat = $lang['fail_exist'];
        $fail = 0;
    }

    //** Check size -- FIXME :: Reports img but not doc ?? elseif ??
    if ($_FILES['file']['size'] >$max) {
        $stat = $lang['fail_size'];
        $fail = 0;
    }

    //** Check type
    if (in_array($type, $img)) {

        //** Check valid image and build entry
        if ($mime !== false) {
            $link = '<a href="' . $host . $file . '" ' .
                    'title="' . $lang['link_img'] . '">' .
                    '<img src="' . $host . $file . '" ' .
                    'width=' . $tnw . ' height=' . $tnh . ' ' .
                    'alt=""/></a><br/>' .
                    $lang['uc_link'] . " <span class=emo>&#x1F5BC;</span> " . basename($file) .
                    " (" . $_FILES['file']['size'] . " " .
                    $lang['bytes']. ")";
        } else {
            $stat = $lang['fail_img'];
            $fail = 0;
        }
    } elseif (
        in_array($type, $doc)
        || in_array($type, $snd)
        || in_array($type, $vid)
    ) {
        //** Link icons and build entry
        if (in_array($type, $doc)) {
            $icon = "1F4D5";
        }

        if (in_array($type, $snd)) {
            $icon = "1F50A";
        }

        if (in_array($type, $vid)) {
            $icon = "1F3A5";
        }

        $link = $lang['uc_link'] . ' <span class=emo>&#x' . $icon . ';</span> ' .
                '<a href="' . $host . $file . '" ' .
                'title="' . $lang['link_doc'] . '">' .
                basename($file) . " (" . $_FILES['file']['size'] .
                " " . $lang['bytes'] . ")</a>";
    } else {
        $stat = $lang['fail_type'];
        $fail = 0;
    }

    //** Check error status
    if ($fail === 0) {
        $stat = $lang['fail'] . " $stat";
    } else {
        //** Finalise upload, build entry, and update log
        if (move_uploaded_file($_FILES['file']['tmp_name'], $file)) {
            $text = "            <div class=item " . 'id="pid' .
                    date('_Y-m-d_H-i-s_') . $_SESSION['name'] . '">' .
                    "\n                <div class=item_head>" .
                    "<div class=item_date>" .
                    date('Y-m-d H:i:s') . "</div> " .
                    "<div class=item_name>" .
                    $_SESSION['name'] . "</div>" .
                    "</div>\n" .
                    "                <div class=item_text>" .
                    "$link</div>\n" .
                    "            </div>\n";
            $text .= file_get_contents($data);
            file_put_contents($data, $text);
            header('Location: #POST');
            exit;
        } else {
            header('Location: #INVALID_POST');
            exit;
        }
    }
}

//** Form
echo '            <form action="#CHAT" method=POST ' .
     'accept-charset="UTF-8" enctype="multipart/form-data">' . "\n" .
     "                <div>\n" .
     "                    <input type=file name=file id=file " .
     'title="' . $lang['up_select'] . '"/>' . "\n" .
     "                    <input type=submit name=upload " .
     'value="&#x27A4; ' . $lang['up'] . '" ' .
     'title="' . $lang['up_title'] . '"/>' . "\n" .
     "                    <div><small>" .
     $lang['max'] . " $max " . $lang['bytes'] . ". " .
     $lang['type_hint'] . "</small></div>\n" .
     "                </div>\n" .
     "            </form>\n";
