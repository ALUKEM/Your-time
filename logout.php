<?php
session_start();
$_SESSION['loggedin'] = 'logged out';
header('Location: home.php');
?>