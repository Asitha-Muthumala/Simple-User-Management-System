<?php

//parameters for database connection
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'userdb';

//Database connection
$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if (mysqli_connect_error()) {
    die('Database connection faild '. mysqli_connect_error()); //terminate processes
}

?>