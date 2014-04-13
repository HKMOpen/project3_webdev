<?php
$pageTitle='Pending Requests';
include 'header.php';
include 'nav.php';

if(isset($_POST['decision'])) $decision = $_POST['decision'];
if(isset($_POST['uname'])) $uname = $_POST['uname'];
if(!empty($decision) && !empty($uname)){
	$users = readUsers();
	foreach($users as $user){
		if($user->username == $_SESSION['username']){
			if($decision == "accept"){
				$user->friends[] = $uname;
				$message = "You and $uname are now friends! Enjoy your online friendship.";
			}
			else $message = "Declined $uname's friend request.";
			//remove $uname from pending list
			$tempArray = array("$uname");
			$user->pending = array_diff($user->pending, $tempArray);
		}
		else if($user->username == $uname){
			if($decision == "accept"){
				$user->friends[] = $_SESSION['username'];
			}
		}
	}
	writeUsers($users);
}

?>

<div class="wrapper">
	<div class="left"> 
		<h3>Pending Requests</h3>
		<?php 
		if(!empty($message)) echo "<p>$message</p>";
		$requests = getRequests($_SESSION['username']);
		if(empty($requests)) echo "<p>You have no new friend requests at this time.</p>";
		else{
			echo "<table>";
			foreach($requests as $request){ // loop through pending requests
				$requestImg = getUser($request)->pic;
				echo "<tr><td><img class=\"smallpic\" src=\"$requestImg\" alt=\"Photo of $request\"/></td>";
				echo "<td><label>$request</label></td>" ?>
				<td>
				<form method="post" action="requests.php">
					<input type="hidden" name="decision" value="accept" />
					<input type="hidden" name="uname" value=<?php echo '"'.$request.'"' ?> />
					<input type="submit" value="Accept"/>
				</form>
				</td>
				<td>
				<form method="post" action="requests.php">
					<input type="hidden" name="decision" value="decline" />
					<input type="hidden" name="uname" value=<?php echo '"'.$request.'"' ?> />
					<input type="submit" value="Decline"/>
				</form>
				</td>
				</tr>
				
		<?php } 
		echo "</table><p></p>";
		} ?>
		
	</div>
	<?php include 'userList.php'; ?>
</div>

<?php include 'footer.php'; ?>