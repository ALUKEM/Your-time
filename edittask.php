<?php
session_start();
$_SESSION['editing'] = 'yes';
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Edit Task | Your Time</title>
		<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,100' rel='stylesheet' type='text/css'>
		<link href="home_style.css" type="text/css" rel="stylesheet">
	</head>
	<body>
		<div class="topnav">
			<a href="home.php#home">Home</a>
			<a href="home.php#about">About</a>
			<a class="active" href="edittask.php">My Tasks</a>
			<?php
			if (array_key_exists('user', $_SESSION)) {
				if ($_SESSION['loggedin']) {
				echo "<a href='logout.php'>Log Out</a>";
				}
			} else {
				echo "<a href='create_account.php'>Create Account</a>";
			}
			?>
		</div>
		<h2>Edit Task</h2>
		<form action="tasks.php" method="POST">
			<h4> Original Task Name: </h4><input type="text" name="idnum"><br>
			<h4> Task Name: (max: 50 chars) </h4><input type="text" name="tskname"><br>
			<h4> Task Description: (max: 300 chars) </h4><input type="text" name="tskdesc"><br>
			<h4>Date: </h4><input type="date" name="duedate"><br>
			<input id="taskcreation1" type="submit" value="Edit Task">
		</form>
	</body>
</html>