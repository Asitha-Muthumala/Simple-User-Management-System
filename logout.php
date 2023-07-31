<?php

session_start();
$_SESSION = array(); //clear session variables
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-86400, '/'); //set empty cookie
}
session_destroy(); //stop session

header('Location: index.php?logout=yes');

?>