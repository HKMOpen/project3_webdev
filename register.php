<?php
$pageTitle = 'Register';
include 'header.php';
include 'nav.php';

$error = "";
?>

<div class="wrapper">
<div class="left">

<?php 
if (isset($_POST['registerFlag'])) {
	
	if (!empty($_POST['username']) && !empty($_POST['password']) 
		&& !empty($_POST['confirmedPassword']) && !empty($_POST['email'])) {
		
		$username = strip_tags($_POST['username']);
		$email = strip_tags($_POST['email']);
		$password = $_POST['password'];
		$confirmedPW = $_POST['confirmedPassword'];
		$ip = $_POST['ip'];
		
		//TODO: add check to make sure username isn't taken
		$takenUsername = nameExists($username);
		if ($takenUsername) {
			$error .= "Sorry, the requested username is already taken. Please choose another. ";
		}
		else if ($password != $confirmedPW) {
			$error .= "The entered and confirmed passwords do not match. Please re-enter the password. ";
		}
		else {
			//username is good, password is good
			requestRegisterAuthentication($username, $email, $password, $ip);
			echo "<p>An email has been sent to you for authentication.</p>";
		}

	}
	else {
		$error .= "Please enter all information before registering. ";
	}

} else { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" >
	<table>
	<tr><td>Username: </td><td><input type="text" name="username" /></td></tr>
	<tr><td>Email: </td><td><input type="text" name="email" /></td></tr>
	<tr><td>Password:</td><td><input type="password" name="password" /></td></tr>
	<tr><td>Confirm password:</td><td><input type="password" name="confirmedPassword" /></td></tr>
	<tr><td><input type="submit" value="Register" /></td></tr>
	</table>
	<input type="hidden" name="ip" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>" />
	<input type="hidden" name="registerFlag" value="true" />
</form>
<?php } 

if (!empty($error)) {
	echo "<p class=\"error\">$error</p>";
	echo "<p><a href=\"" . htmlspecialchars($_SERVER['PHP_SELF']) . "\">Go back.</a></p>";
}

?>

</div>
<?php include 'userList.php'; ?>
</div>

<?php include 'footer.php'; ?>
