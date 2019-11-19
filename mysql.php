<?php

//-->DB-Connect

$dbhost = "localhost";
$dbname = "homepage";
$dbuser = "web563";
$dbpass = "lCgz62*1";

$mysql = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

mysqli_query($mysql, "SET NAMES 'utf8'");
mysqli_set_charset($mysql, "UTF8");

?>