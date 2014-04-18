<?php
include 'session_start.php';
include 'helpers.php';
readUsers();
?>

<?php  echo  '<?xml version="1.0" encoding="utf-8"?>' ?>
<?php  echo "\n"?>
<?php  echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' ?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?php echo "<title> $pageTitle </title>\n" ?>
   <link href="style.css" rel="stylesheet" type="text/css" />
   <meta http-equiv="Content-Type" 
         content="text/html; charset=utf-8" />
</head>
<!-- Start of page Body -->
<body>

<div id="header">
<a href='home.php'><img src='images/default.jpg' alt='logo'/></a>
<a href='home.php'><h1>ColostateBook</h1></a>
<?php echo "<div id='pageHead'><h2> $pageTitle</h2></div>"; ?>
