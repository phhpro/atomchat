/*
 * PHP Version 5 and above
 *
 * Javascript helper
 *
 * @category  PHP_Chat
 * @package   PHP_Atomchat
 * @author    P H Claus <phhpro@gmail.com>
 * @copyright 2015 - 2019 P H Claus
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @version   GIT: Latest
 * @link      https://github.com/phhpro/atomchat
 */


/*
 ***********************************************************************
 *                                                   BEGIN USER CONFIG *
 ***********************************************************************
 */


// Post delay and refresh rate -- recommended 2000 or more
var rate = 2000;

// Maximum chars per post -- must match "$char" in config.php
var char = 1024;


/*
 ***********************************************************************
 *                                                     END USER CONFIG *
 ***********************************************************************
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
        return new ActiveXObject('Microsoft.XMLHTTP');
    } else if (window.XMLHttpRequest) {
        return new XMLHttpRequest();
    } else {
        alert('Your browser doesn\'t support AJAX!');
        return null;
    }
}

function stat()
{
    if (http.readyState == 4) {
        document.getElementById('push').innerHTML = http.responseText;
    }
}

function wait()
{
    http = ajax();

    if (http != null) {
        http.open('GET', '?' + Math.floor(Math.random() * 10000), true);
        http.onreadystatechange = stat;
        http.send();
    }
}

function push()
{
    wait();
    setTimeout('push()', rate);
}

push();
