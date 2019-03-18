/*
 * PHP Version 5 and above
 *
 * JavaScript helper
 *
 * @category  PHP_Chat
 * @package   PHP_Atomchat
 * @author    P H Claus <phhpro@gmail.com>
 * @copyright 2015 - 2019 P H Claus
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @version   GIT: Latest
 * @link      https://github.com/phhpro/atomchat
 */


function chars(chat)
{
    if (chat.text.value.length >= char) {
        chat.txta.value = chat.text.value.substring(0, text);
    } else {
        chat.txta.value = char - chat.text.value.length;
    }
}

function emo(str)
{
    var id  = document.getElementById("text");
    var str = document.getElementById(str).value;

    if (document.selection) {
        id.focus();
        var sel = document.selection.createRange();
        sel.str = str;
        id.focus();
    } else if (id.selectionStart || id.selectionStart === 0) {
        var beg  = id.selectionStart;
        var end  = id.selectionEnd;
        var top  = id.top;
        id.value = id.value.substring(0, beg) + str +
                   id.value.substring(end, id.value.length);
        id.focus();
        id.selectionStart = beg + str.length;
        id.selectionEnd   = beg + str.length;
        id.top            = top;
    } else {
        id.value += str;
        id.focus();
    }
}

function selectText(txt)
{
    var rng = document.createRange();
        rng.selectNodeContents(txt);

    var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(rng);
}

function selectID(id)
{
    var txt = document.getElementById(id);
    selectText(txt);
}

var http = null;

function ajax()
{
    if (window.ActiveXObject) {
        return new ActiveXObject("Microsoft.XMLHTTP");
    } else if (window.XMLHttpRequest) {
        return new XMLHttpRequest();
    } else {
        alert("Your browser doesn't support AJAX!");
        return null;
    }
}

function push()
{
    http = ajax();
    data = data;

    if (http != null) {
        http.open("POST", data);

        http.onreadystatechange = function()
        {
            if (http.readyState == 4) {
                document.getElementById("push").innerHTML
                    = http.responseText;
            }
        }

        http.send();
    }

    setTimeout('push()', (1000 * rate));
}

push();
