<div id="navs">
	
	<a href="home.php"><span class="navitem">Home</span></a>
	<?php $logged_in = false; ?>
	<?php if($_SESSION["username"] != "guest"){
		$logged_in=true; 
		$admin = getUser($_SESSION["username"])->admin;
	}?>
	<?php if($logged_in){?>
	<a href="profile.php?uname=<?php echo $_SESSION["username"]?>"><span class="navitem">Profile</span></a>
	<a href="requests.php"><span class="navitem">Requests<?php echo "(" . count(getRequests($_SESSION['username'])) . ")"; ?></span></a>
	<!-- TODO only show admin tab if logged in user has admin rights -->
	<?php if($admin == "1"){?>
	<a href="admin.php"><span class="navitem">Admin<?php echo "(" . countUsersToBeApproved() . ")"; ?></span></a> 
	<?php }}?>
	<?php $status= ($_SESSION["username"]=="guest") ? "Log In" : "Log Out"; ?>
	<a href="login.php"><span class="navitem"><?php echo $status?></span></a>
	 
</div>
</div>