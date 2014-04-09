<!-- Start of included footer -->
<div id="footer">
	<!-- TODO link username to correct profile (only if logged in) -->
	<?php if ($_SESSION['username'] != "guest") {?>
	<p>Logged in as: <a href='profile.php?uname=<?php echo $_SESSION['username']?>'>
	<?php echo $_SESSION['username']?></a></p>
	<?php } else { ?>
	<p>You are a guest</p>
	<?php } ?>
	<?php 
	if($_SESSION['username'] == "guest"){
		echo "<p><a href='login.php'>Log In</a></p>";
	}
	else{
		echo "<p><a href='login.php'>Log Out</a></p>";
	}
	?>
</div>
</body>
</html>