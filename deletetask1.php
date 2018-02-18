<?php
session_start();
$_SESSION['editing'] = 'delete';
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Delete Task | Your Time</title>
		<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,100' rel='stylesheet' type='text/css'>
		<link href="home_style.css" type="text/css" rel="stylesheet">
	</head>
	<body>
		<a href="home.php">
			<img id="logo" src="VTHacks_logo.png" alt="Your Time" height="105px" width="120px">
		</a>
		<div class="topnav">
			<a href="home.php#home">Home</a>
			<a href="home.php#about">About</a>
			<a href="tasks.php">My Tasks</a>
			<?php
			if (array_key_exists('user', $_SESSION)) {
				if ($_SESSION['loggedin']) {
				echo "<a href='logout.php'>Log Out</a>";
				}
			} else {
				echo "<a href='create_account2.php'>Create Account</a>";
			}
			?>
		</div>
		<h2>Edit Task</h2>
		<form action="tasks.php" method="POST">
			Task to delete: <input type = "text" name = "deltaskname">
			<input type="submit" value="Delete Task">
		</form>
	</body>
</html>