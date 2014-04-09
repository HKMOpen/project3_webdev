<?php

$sessionName = "ct310_project2_session";
session_name($sessionName);
session_start();

if (!isset($_SESSION['username'])) {
$_SESSION['username'] = "guest";
}

?>