<?php  
   $pageTitle='Home';
   include 'header.php';
   include 'nav.php';
?>

<div class="wrapper">
	<div class="left"> 
		<h3>Welcome!</h3>
   		<p>Welcome to our site! It has personalized user profiles and navigation through a common navigation bar, to maintain simplicity. Each user has personally modifiable interests and self-summary, with a corresponding text file stored on the server. The site is designed
in small units for easy modification and sharing elements between pages. </p>
	</div>
	<?php include 'userList.php'; ?>
</div>

<?php include 'footer.php'; ?>