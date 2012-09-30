<?php
// we connect to localhost at port 3307
$link = mysql_connect('127.0.0.1:1133', 'root', '');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
echo 'Connected successfully';
mysql_close($link);