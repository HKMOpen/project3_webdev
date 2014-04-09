<?php
$pageTitle = 'Change Password';
include 'header.php';

include 'nav.php';

$error = "";
?>

<div class="wrapper">
<div class="left">

<?php
if (isset($_POST['changePasswordFlag'])) { 
	$oldPW = $_POST['oldPassword'];
	$newPW = $_POST['newPassword'];
	$confirmPW = $_POST['confirmPassword'];
	$ip = $_SERVER['REMOTE_ADDR'];
	
	if (!empty($oldPW) && !empty($newPW) && !empty($confirmPW)) {
		//TODO: Check that old password is correct, and make sure newPW and
		// confirmPW match before changing password, also check that IP matches the DB
		
		changePassword($_POST['username'], $newPW);
		echo "<p>Your password has been changed successfully. Please logout and log back in using your new password.</p>";
	}
	else {
		$error .= "Please make sure all fields are complete before submitting. ";
	}
	?>

<?php } else if (!empty($_GET['username']) && !empty($_GET['key'])) {?>

<!-- FORM TO CHANGE PASSWORD:
still need to check that given key matches the stored key for the user 
before showing the form -->

<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
	<table>
	<tr><td>Old password:</td><td><input type="password" name="oldPassword"/></td></tr>
	<tr><td>New password:</td><td><input type="password" name="newPassword"/></td></tr>
	<tr><td>Confirm new password:</td><td><input type="password" name="confirmPassword"/></td></tr>
	<tr><td><input type="submit" value="Change password" /></td></tr>
	</table>
	<input type="hidden" name="username" value="<?php echo $_GET['username']; ?>" />
	<input type="hidden" name="changePasswordFlag" value="true" />
</form>

<?php } else { ?>

	<p>Error trying to change password. Please try again.</p>

<?php } ?>

<?php if (!empty($error)) {
	echo "<p class=\"error\">$error</p>";
}?>

</div>
<?php include 'userList.php'; ?>
</div>
<?php include 'footer.php'; ?>
