<?php
session_start()
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Home | Your Time</title>
		<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,100' rel='stylesheet' type='text/css'>
		<link href="css.css" type="text/css" rel="stylesheet">
	</head>
	<body>
		<?php
		if (array_key_exists('loggedin', $_SESSION)) {
			if ($_SESSION['loggedin'] and $_SESSION['redirect']) {
				echo "<script type='text/javascript'>alert('Successfully logged in!');</script>";
			}
			if ($_SESSION['redirect']) {
				$_SESSION['redirect'] = FALSE;
				$_SESSION['err'] = TRUE;
				header('Location: tasks.php');
			}
			if ($_SESSION['loggedin'] === 'logged out') {
				session_unset();
				session_destroy();
				echo "<script type='text/javascript'>alert('Successfully logged out!');</script>";
			}
		}
		?>
		<a href="home.php">
			<img id="logo" src="VTHacks_logo.png" alt="Your Time" height="105px" width="120px">
		</a>
		<div class="topnav">
			<a class="active" href="home.php#home">Home</a>
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
		<h1>It's time. </h1>
		
		<?php
		if (!array_key_exists('user', $_SESSION)) {
			echo "<h2><a href='create_account.php'>Join us</a> and start taking back <strong>your</strong> time.</h2>
			<h3>Already have an account? <a href='login.php'>Log in!</a></h3>";
		}
		?>
		
		<br><br><p id="about">Welcome to <b>Your Time</b>! <b>Your Time </b> is a website created by the Purple Toasters. It generates a custom schedule based on the tasks you need to accomplish during the day. Simply make an account, add your daily tasks with a prediction of how long it will take you, and let the website make you a schedule. This schedule is updated based on user data, such as how long it takes you to complete a task, so if you usually need more time for that math homework, it takes that into account and allots you more time for it in your schedule. Allow us to manage your time.	</p>
	</body>
</html>