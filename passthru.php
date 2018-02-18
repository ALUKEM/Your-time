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
		
	</body>
</html>