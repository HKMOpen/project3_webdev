<?php
$pageTitle = 'Authenticate';
include 'header.php';
include 'nav.php';

$error = "";

?>

<div class="wrapper">
<div class="left">

<?php 

if (isset($_GET['user']) && isset($_GET['key'])) {
	$username = sanitize($_GET['user']);
	$key = sanitize($_GET['key']);
	$ip = $_SERVER['REMOTE_ADDR'];
	
	$success = authenticateNewUser($username, $key, $ip);
	if ($success) {
		echo "<p>Success! Your account is now authenticated and awaits admin approval.</p>";
	}
	else {
		echo "<p>Sorry, your account could not be authenticated.</p>";
	}
	
}
else {
	echo "<p>An error occurred while trying to authenticate. Please try again.</p>";
}

?>


</div>
<?php include 'userList.php'; ?>
</div>

<?php include 'footer.php'; ?>
