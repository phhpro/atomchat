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
        chat.text.value = chat.text.value.substring(0, char);
    } else {
        chat.char.value = char - chat.text.value.length;
    }
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
    data = data + "?" + Math.floor(Math.random() * 10000);

    if (http != null) {
        http.open("GET", data, true);
        http.onreadystatechange = function()
        {

            if (http.readyState == 4) {
                document.getElementById("push").innerHTML
                    = http.responseText;
            }
        }

        http.send();
    }

    setTimeout('push()', rate);
}

push();
