<?php

//-->DB-Connect

$dbhost = "localhost";
$dbname = "bachelor";
$dbuser = "bachelor_user";
$dbpass = "aKp09w~0";

$mysql = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

mysqli_query($mysql, "SET NAMES 'utf8'");
mysqli_set_charset($mysql, "UTF8");

?>