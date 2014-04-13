<?php
$pageTitle='Admin Page';
include 'header.php';
include 'nav.php';
?>

<?php 
$approvedMessage = "";
//handle user approval and disapproval form submissions

//APPROVAL
if (isset($_POST['approveUserFlag'])) {
	$username = $_POST['username'];
	approveNewUser($username);
	$approvedMessage = "$username has been approved.";
}

//DISAPPROVAL
if (isset($_POST['disapproveUserFlag'])) {
	$username = $_POST['username'];
	disapproveNewUser($username);
	$approvedMessage = "$username has been disapproved.";
}

?>

<div class="wrapper">
	<div class="left"> 
		<h3>Approve New Users</h3>

		<?php
		if($_SESSION['username'] != 'guest' && getUser($_SESSION['username'])->admin == "1"){ 

		if (!empty($approvedMessage)) {
			echo "<p>$approvedMessage</p>";
		}
		
		$unapprovedUsers = getAllUsersToBeApproved();
		echo "<table id=\"approveUsers\">";
		foreach ($unapprovedUsers as $userToApprove) { ?>
			
			<tr>
				<td><?php echo $userToApprove; ?></td>
				<td>
					<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
						<input type="submit" value="Approve"/>
						<input type="hidden" name="username" value="<?php echo $userToApprove; ?>" />
						<input type="hidden" name="approveUserFlag" value="true" />
					</form>
				</td>
				<td>
					<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
						<input type="submit" value="Disapprove"/>
						<input type="hidden" name="username" value="<?php echo $userToApprove; ?>" />
						<input type="hidden" name="disapproveUserFlag" value="true" />
					</form>
				</td>
			</tr>
			
		<?php
		}
		echo "</table>";
		
		?>
		<?php
		} else echo "<p>You do not have the admin rights required to view this page. Shame on you.</p>";
		?>
	</div>
	<?php include 'userList.php'; ?>
</div>



<?php include 'footer.php'; ?>