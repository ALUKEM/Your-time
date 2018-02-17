<?php
session_start()
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Login | Your Time</title>
		<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,100' rel='stylesheet' type='text/css'>
		<link href="home_style.css" type="text/css" rel="stylesheet">
	</head>
	<body>
		<?php
		$loggedin = $wow = "";
		$_SESSION['errors'] = array();
		$errList = array();
		$_SESSION['editing'] = 'no';
		function test_input($data) {
		  $data = trim($data);
		  $data = stripslashes($data);
		  $data = htmlspecialchars($data);
		  return $data;
		}
		function checkInputs() {
			global $errList, $loggedin;
			$servername = "localhost";
			$username = "root";
			$password = "";
			$dbname = "accounts";
			$user = $_POST['usr'];
			$userpassword = $_POST['pass'];
			$user = test_input($user);
			$userpassword = test_input($userpassword);
			
			//Create connection_aborted
			$conn = mysqli_connect($servername, $username, $password, $dbname);

			//Check connection_aborted
			if (!$conn) {
				die("Connection failed: " . mysqli_connect_error());
			}
			
			if (strlen($user) < 1) {
				array_push($errList, 'Please enter a user name');
			}
			if (strlen($userpassword) < 1) {
				array_push($errList, 'Please enter a password');
			}
			// $hasheduser = hash('ripemd160', $user);
			$hashedpswd = hash('sha512', $userpassword);
			$sql = "SELECT * FROM information_schema.TABLES WHERE TABLE_NAME='$user' AND TABLE_SCHEMA='accounts';";
			$result = mysqli_query($conn, $sql);
			if ($result == FALSE) {
				$userFound = 'no';
			} else {
				$wow = mysqli_fetch_assoc($result);
				$userFound = $wow['TABLE_NAME'];
			}
			if ($userFound === $user) {
				$sql = "SELECT * FROM accounts.$user;";
				$result = mysqli_query($conn, $sql);
				if ($result == FALSE) {
					$passwordFound = 'nope';
				} else{
					$wow = mysqli_fetch_assoc($result);
					$passwordFound = $wow['pswd'];
				}
				if ($passwordFound === $hashedpswd) {
					$loggedin = 'yes';
					$_SESSION['loggedin'] = TRUE;
					$time = date('Y-m-d H:i:s');
					$sql = "INSERT INTO accounts.$user SET
					last_login = $time;";
				} else {
					array_push($errList, 'Incorrect user name or password');
				}
			} else {
				if ($user != "") {
					array_push($errList, 'Incorrect user name or password');
				}
			}
			mysqli_close($conn);
			
		}
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			checkInputs();
		}
		?>
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
				echo "<a href='create_account.php'>Create Account</a>";
			}
			?>
		</div>
		<h1>Log in!</h1>
		<?php
		if (!empty($errList)) {
			echo "<h2 id='error'><strong>Errors:</strong></h2>";
			for($x = 0; $x <= count($errList)-1; $x++) {
				echo "<h2 id='error'><strong>- $errList[$x]<br></strong></h2>";
			}
		} else {
			if ($loggedin == 'yes') {
				$_SESSION['user'] = $_POST['usr'];
				$_SESSION['redirect'] = TRUE;
				header("Location: home.php");
			}
		}
		?>
		<form target="_self" method="POST">
			<h5>Username: </h5><input type="text" name="usr"><br>
			<h5>Password: </h5><input type="password" name="pass"><br>
			<input type="submit" value="Log in">
		</form>
	</body>
</html>