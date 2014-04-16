<?php
$pageTitle = 'Log In / Log Out';
include 'header.php';


/*
 * TODO: Authenticate the user by salting & hashing the entered password and comparing it with the salted hashed password for that username
 */

$error = "";

if (isset ( $_POST ['username'] )) {
	if (isPendingUser ( $_POST ['username'] )) {
		$error = "Sorry, you are still a pending user. You may not login.";
	} else {
		$uname = sanitize ( $_POST ['username'] );
		$pass = "";
		if (isset ( $_POST ['enteredPassword'] ))
			$pass = $_POST ['enteredPassword'];
			// authentication
		$users = readUsers ();
		foreach ( $users as $user ) {
			if ($user->username == $uname) {
				if (saltedHash ( $pass, $uname ) == $user->passwd) {
					// authentication successful!
					$_SESSION ['username'] = $uname;
					$success = "User $uname logged in successfully!";
				} else
					$error = "Invalid password for user $uname. Try again.";
				break;
			}
		}
		if (empty ( $error ))
			$error = "User $uname does not exist.";
	}
}

if (isset ( $_POST ['logOutFlag'] )) {
	$_SESSION ['username'] = "guest";
}

include 'nav.php';

?>

<div class="wrapper">
	<div class="left">
<?php if ($_SESSION['username'] == "guest") {?>
		<h3>Log In</h3>
		<form method="post"
			action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
			<table>
				<tr>
					<td><label>Username:</label></td>
					<td><input type="text" name="username" /></td>
				</tr>
				<tr>
					<td><label>Password:</label></td>
					<td><input type="password" name="enteredPassword" /></td>
				</tr>
				<tr>
					<td><input type="submit" value="Log in" /></td>
				</tr>
			</table>
		</form>
		<p>
			New User? <a
				style="border: 1px solid black; background-color: white;"
				href="register.php">Click here to register!</a>
		</p>	
<?php
echo "<p>$error</p>";
}
else {?>
<h3>Log Out</h3>
<?php if(!empty($success)) echo "<p>$success</p>" ?>
		<form method="post"
			action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
			<input type="submit" value="Log out" /> <input type="hidden"
				name="logOutFlag" value="true" />
		</form>
		<p></p>
<?php } ?>
</div>
<?php include 'userList.php'; ?>
</div>

<?php include 'footer.php'; ?>