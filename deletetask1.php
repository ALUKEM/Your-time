<?php
session_start();
$_SESSION['editing'] = 'delete';
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Delete Task | Your Time</title>
		<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,100' rel='stylesheet' type='text/css'>
		<link href="css.css" type="text/css" rel="stylesheet">
	</head>
	<body>
		<a href="passthru.php">
			<img id="logo" src="VTHacks_logo.png" alt="Your Time" height="105px" width="120px">
		</a>
		<div class="topnav">
			<a href="home2.php">Home</a>
			<a class="active" href="tasks.php">My Tasks</a>
			<a href="about.php#about">About</a>
			<?php
			if (array_key_exists('user', $_SESSION)) {
				if ($_SESSION['loggedin']) {
				echo "<a href='logout.php'>Log Out</a>";
				}
			}
			?>
		</div>
		<h2>Delete Task</h2>
		<form action="tasks.php" method="POST">
			Task to delete: <input type = "text" name = "deltaskname">
			<input type="submit" value="Delete Task">
		</form>
	</body>
</html>