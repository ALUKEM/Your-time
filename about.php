<?php
session_start()
?>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="css.css">
		<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,100' rel='stylesheet' type='text/css'>
		<title>About | Your Time</title>
	</head>
	<body>
		<img id="logo" src="VTHacks_logo.png" alt="Your Time" height="105px" width="120px">
		<div class="topnav">
			<?php
				if (array_key_exists('user', $_SESSION)) {
					if ($_SESSION['loggedin']) {
						echo "<a href='home2.php'>Home</a>";
					}
				} else {
					echo "<a href='index.php'>Home</a>";
				}
			?>
			<a href="tasks.php">My Tasks</a>
			<a class = "active" href="about.php#about">About</a>
			<?php
				if (array_key_exists('user', $_SESSION)) {
					if ($_SESSION['loggedin']) {
					echo "<a href='logout.php'>Log Out</a>";
					}
				}
			?>
		</div>
		<br>
		<h1>It's time. </h1>
		<br><br><p id="about">Welcome to <b>Your Time</b>! <b>Your Time </b> is a website created by the Purple Toasters. It generates a custom schedule based on the tasks you need to accomplish during the day. Simply make an account, add your daily tasks with a prediction of how long it will take you, and let the website make you a schedule. This schedule is updated based on user data, such as how long it takes you to complete a task, so if you usually need more time for that math homework, it takes that into account and allots you more time for it in your schedule. Allow us to manage your time.	</p>

	</body>
</html>