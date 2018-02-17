<?php
session_start()
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Home | Your Time</title>
		<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,100' rel='stylesheet' type='text/css'>
		<link href="home_style.css" type="text/css" rel="stylesheet">
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
		<div class="topnav">
			<a class="active" href="#home">Home</a>
			<a href="#about">About</a>
			<a href="tasks.php">My Tasks</a>
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
		<h1>It's time. </h1>
		
		<?php
		if (!array_key_exists('user', $_SESSION)) {
			echo "<h2><a href='create_account.php'>Join us</a> and start taking back <strong>your</strong> time.</h2>
			<h3>Already have an account? <a href='login.php'>Log in!</a></h3>";
		}
		?>
	</body>
</html>
