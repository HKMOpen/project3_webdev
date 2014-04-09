<?php
/*
 * TODO: write newly changed profile info or summary to file
 */

?>


<?php

$pageTitle = 'User Profile';
include 'header.php';
include 'nav.php';

$remoteIPAddress = $_SERVER['REMOTE_ADDR'];
$whiteListed = whitelisted($remoteIPAddress);

$uname = "";
if (isset($_GET['uname'])) {
	$uname = $_GET['uname'];
}

function isTypeImage($fileType) {
	if (preg_match("/image\//", $fileType)) {
		return true;
	}
	else {
		return false;
	}
}

function whitelisted($ipaddress) {
	$nums = explode('.', $ipaddress);
	if ($nums[0] == "129" && $nums[1] == "82") {
		return true;
	}
	else {
		return false;
	}
}

//BEGIN REQUESTING NEW FRIEND

if (isset($_POST['addFriendFlag'])) {
	/*
	 * REQUEST
	 */
	$signedInUser = $_POST['signedInUser'];
	$requestedUser = $_POST['requestedUser'];
	$allUsers = readUsers();
	
	foreach($allUsers as $user) {
		if ($user->username == $requestedUser) {
			$user->pending .= $signedInUser . ',';
			break;
		}
	}
	writeUsers($allUsers);
	
}

//END REQUESTING NEW FRIEND

//BEGIN CHANGING NEWLY SUBMITTED USER INFORMATION
$error = "";
//change profile photo
if (isset($_POST['uploadPicFlag'])) {
	//user wants to upload a new photo
	
	if (!isset($_FILES['file'])) {
		$error = "Please choose an image to upload.";
	}
	else if (!isTypeImage($_FILES['file']['type'])) { //type is NOT an image
		$error = "Please choose an image of type 'image' to upload.";
	}
	else if ($_FILES['file']['size'] > 1048576) {
		$error = "Please choose an image that is less than 1MB in size.";
	}
	else {
		//all error tests passed, move the image into images/
		$newlyUploadedPic = "images/" . $_SESSION['username'] . "_" . $_FILES['file']['name'];
		$flag = move_uploaded_file($_FILES['file']['tmp_name'], $newlyUploadedPic);
		
		//change permissions
		$chmodSuccess = false;
		if (file_exists($newlyUploadedPic)) {
			$chmodSuccess = chmod($newlyUploadedPic, 0744);
		}
		if ($flag and $chmodSuccess) {
			echo "Image uploaded successfully!";
			
			//update users file
			$allUsers = readUsers();
			for ($i = 0; $i < count($allUsers); $i++) {
				if ($allUsers[$i]->username == $_SESSION['username']) {
					$allUsers[$i]->pic = $newlyUploadedPic;
					break;
				}
			}
			writeUsers($allUsers);
			
		}
		else {
			"ERROR: image upload was unsuccessful. Please try again.";
		}
		
	}
	
}

//change profile information
if (isset($_POST['submitNewInformationFlag'])) {
	//user submitted new information.
	//sanitize new input and rewrite the uses.tsv file
	$newName = strip_tags($_POST['newName']);
	$newGender = strip_tags($_POST['newGender']);
	$newPhoneNum = strip_tags($_POST['newPhoneNum']);
	$newEmail = strip_tags($_POST['newEmail']);

	$allUsers = readUsers();
	for ($i = 0; $i < count($allUsers); $i++) {
		if ($allUsers[$i]->username == $_SESSION['username']) {
			$allUsers[$i]->name = $newName;
			$allUsers[$i]->gender = $newGender;
			$allUsers[$i]->phone = $newPhoneNum;
			$allUsers[$i]->email = $newEmail;
			break;
		}
	}
	writeUsers($allUsers);
}

//change summary info
if(isset($_POST['submitNewSummaryFlag'])) {
	$newSummary = sanitize($_POST['newSummary']);
	
	$userSummaries = readUserSummaries();
	foreach ($userSummaries as $userSummary) {
		if ($userSummary->username == $_SESSION['username']) {
			$userSummary->summary = $newSummary;
			break;
		}
	}
	writeUserSummaries($userSummaries);
}
//END CHANGING NEWLY SUBMITTED USER INFORMATION

$user = getUser($uname);
$userSummary = getUserSummary($uname);
if (is_null($userSummary)) { $userSummary = "";}
?>

<?php if (!is_null($user)) {?>

<div class="wrapper">

	<div class="left">
<?php echo "<h3>$uname</h3>"; ?>

<!-- display Add Friend button only when the user isn't guest
      and isn't already friends -->
<?php if ($_SESSION['username'] != "guest" && $_SESSION['username'] != $uname 
		&& !isFriend($_SESSION['username'], $uname) 
		&& !isPending($_SESSION['username'], $uname)) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?uname=$uname"; ?>">
	<input type="submit" value="Add friend!" />
	<input type="hidden" name="addFriendFlag" value="true" />
	<input type="hidden" name="signedInUser" value="<?php echo $_SESSION['username']; ?>" />
	<input type="hidden" name="requestedUser" value="<?php echo $uname; ?>" />
</form>
<?php } ?>

<img class="prof_pic" src="<?php echo $user->pic; ?>" alt="Photo of <?php echo $user->name?>" />
<?php if (!empty($error)) { echo "<p class=\"error\">" . $error . "</p>";}?>

<?php
/*
 * The following code is for uploading a new profile picture. TODO: once the user uploads a new picture, handle it
 */
?>

<?php if (isset ( $_POST ['editPicFlag'] )) { ?>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?uname=$uname"; ?>" enctype="multipart/form-data">
			<input type="file" name="file" /> <br /> 
			<input type="submit" value="Upload" /> 
			<input type="hidden" name="uploadPicFlag" value="true" />
	</form>
<?php } else if ($_SESSION['username'] == $uname && $whiteListed) {?>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?uname=$uname"; ?>">
		<input type="submit" value="Upload new profile picture" /> 
		<input type="hidden" name="editPicFlag" value="true" />
	</form>
<?php } ?>

<div id="profile_info">
	<h3>Profile information:</h3>
	<p>Name: <?php echo ' ' . $user->name ?></p>
	<p>Gender: <?php echo ' ' . $user->gender ?></p>
<?php if ($_SESSION['username'] != "guest") {?>
	<p>Mobile phone #: <?php echo ' ' . $user->phone ?></p>
	<p>Email address: <?php echo ' ' . $user->email ?></p>
<?php } ?>

<?php
if (isset ( $_POST ['editInformationFlag'] )) { ?>
<form method="post"
				action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?uname=$uname"; ?>">
				<table>
					<tr>
						<td><label>Name:</label></td>
						<td><input type="text" name="newName" value="<?php echo $user->name ?>" /></td>
					</tr>
					<tr>
					<td><label>Gender:</label></td>
						<td><input type="radio" name="newGender" checked="checked" value="Male"/>Male</td>
					</tr>
					<tr>
						<td></td>
						<td><input type="radio" name="newGender" value="Female"/>Female</td>
					</tr>
					<tr>
						<td><label>Mobile phone #:</label></td>
						<td><input type="text" name="newPhoneNum"
							value="<?php echo $user->phone ?>" /></td>
					</tr>
					<tr>
						<td><label>Email address:</label></td>
						<td><input type="text" name="newEmail"
							value="<?php echo $user->email ?>" /></td>
					</tr>
					<tr>
						<td><input type="submit" value="Update Information"/></td>
					</tr>
				</table>
				<input type="hidden" name="submitNewInformationFlag" value="true" />
			</form>

<?php } else if ($_SESSION['username'] == $uname && $whiteListed) { ?>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?uname=$uname"; ?>">
		<input type="submit" value="Edit profile information" /> 
		<input type="hidden" name="editInformationFlag" value="true" />
	</form>
<?php } ?>

<?php if ($_SESSION['username'] != "guest") { ?>
	<h3>Summary and Interests:</h3>
	<p><?php echo $userSummary; ?></p>
<?php } ?>

<?php
if (isset ( $_POST ['editSummaryFlag'] )) { ?>

	<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?uname=$uname"; ?>">
		<textarea name="newSummary" rows="6" cols="30"><?php echo $userSummary; ?></textarea>
		<input type="submit" value="Update Summary"/> 
		<input type="hidden" name="submitNewSummaryFlag" value="true" />
	</form>

<?php } else if ($_SESSION['username'] == $uname && $whiteListed) {?>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?uname=$uname"; ?>">
		<input type="submit" value="Edit summary and interests" /> 
		<input type="hidden" name="editSummaryFlag" value="true" />
	</form>
<?php } ?>

</div>
</div>

<?php include 'userList.php'; ?>

</div>
<?php } else {
	//$user is NULL (the GET variable in the URL returned a NULL user)?>
	
	<p>ERROR: No user profile was specified.</p>
	
<?php } ?>

<?php include 'footer.php'; ?>