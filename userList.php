<div class="users">

	<?php if (count(getFriends($_SESSION['username'])) != 0) {?>
		<div id="friends">
		<h3>Friends:</h3>
		<?php
		foreach (getFriends($_SESSION['username']) as $friend) {
			$uname = $friend->username;
			$picture = $friend->pic;
			?>
			<div class="user">
				<?php echo '<a href="profile.php?uname='.$uname.'">
				<img class="thumbnails" src="'.$picture.'" alt="user1" /></a>'; ?>
				<?php echo $uname ?>
			</div>
		<?php
		}
		?>
		</div>
	<?php } ?>
	
	<div id="friends">
	<h3>Current Users:</h3>
	<?php
	$users = readUsers();
	foreach($users as $u){
		$uname = $u->username;
		$picture = $u->pic;
	?>
	<?php if(!in_array($u, getFriends($_SESSION['username']))){?>
		<div class="user">
			<?php echo '<a href="profile.php?uname='.$uname.'"><img class="thumbnails" src="'.$picture.'" alt="user1" /></a>'; ?>
			<?php echo $uname ?>
		</div>
	<?php
	}
	}
	?>
	</div>
</div>