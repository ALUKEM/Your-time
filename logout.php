<html>
	<head>
		<title>logout</title>
	</head>
	<body onload = "alert('Successfully logged out');">
		<?php
		session_start();
		session_unset();
		session_destroy();
		header('Location: index.php');
		?>
	</body>
</html>