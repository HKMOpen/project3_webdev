<?php
$pageTitle='Admin Page';
include 'header.php';
include 'nav.php';

$username = "";
$passwd = "";
$error = "";
$admin = "0"; // 0 = no admin privileges, 1 = admin

//TODO restrictions on username

if ($_SERVER["REQUEST_METHOD"]=="POST"){
	$raw = "";
	if(isset($_POST["uname"])){
		$username = strip_tags($_POST["uname"]);
		$username = str_replace(',', '', $username);
		if(isset($_POST["pass"])){
			if(!empty($_POST["pass"])){
				$raw = $_POST["pass"];
				$passwd = saltedHash($raw, $username);
			}
		}
	}
	if(isset($_POST["admin"])) $admin = $_POST["admin"];
	
	if(strlen($raw) > 16){
		$passwd = "";
		$error = "Password can not be longer than 16 characters.";
	}
	else if(empty($username) || empty($passwd)) $error = "Could not create new user. Please complete all fields before submitting.";
	else{
		// Create New User
		$users = readUsers();
		foreach($users as $user){
			if($user->username == $username){
				$error = "User $username already exists! Failed to create new user.";
				break;
			}
		}
		if(empty($error)){
			$u = makeNewUser($username, $passwd, "", "", "", "", $admin, "images/default.jpg","", "");
			$users[count($users)] = $u;
			writeUsers($users);
			
			//write default summary to file
			$userSummaries = readUserSummaries();
			$uSumm = new UserSummary();
			$uSumm->username = $username;
			$uSumm->summary = "Edit summary and interests here.";
			$userSummaries[] = $uSumm;
			writeUserSummaries($userSummaries);
		}
	}
	
}



?>

<div class="wrapper">
	<div class="left"> 
		<h3>Create New User</h3>
		<?php
		 if($_SESSION['username'] != 'guest' && getUser($_SESSION['username'])->admin == "1"){ ?>
		<form method="post" action="admin.php">
			<table>
				<tr>
					<td><label>User Name:</label></td>
					<td><input type="text" name="uname" value=<?php echo '"'.$username.'"' ?> /></td>
				</tr>
				<tr>
					<td><label>Password:</label></td>
					<td><input type="password" name="pass"/></td>
				</tr>
				<tr>
					<td><label>Admin?</label></td>
					<td><input type="radio" name="admin" value="1"/>Yes</td>
				</tr>
				<tr>
				<td></td>
				<td><input type="radio" name="admin" checked="checked" value="0"/>No</td>
				</tr>
				<tr>
					<td><input type="submit" value="Create User"/></td>
				</tr>
				
			</table>
		</form>
		<?php 
		//echo "<p>$username $passwd $admin</p>";
		if(!empty($error)){
			echo "<p>$error</p>";
		}
		}
		else echo "<p>You do not have the admin rights required to view this page. Shame on you.</p>";
		?>
	</div>
	<?php include 'userList.php'; ?>
</div>



<?php include 'footer.php'; ?>