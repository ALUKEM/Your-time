<?php
session_start()
?>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="css.css">
		<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,100' rel='stylesheet' type='text/css'>
		<title>Home | Your Time</title>
	</head>
	<body>
	<a name="top"></a>
	<?php 
		$errors = array("All fields are required.","Passwords do not match.","Error with creating account. Most likely, an account with that user name already exists.","Password requirements not fulfilled.");
		$alert = FALSE;
		$ErrMsg = array();
		$VAR = 0;
		$VAR1 = 0;
		$VAR2 = 0;
		$loggedin = $wow = "";
		$_SESSION['errors'] = array();
		$errList = array();
		$_SESSION['editing'] = 'no';

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
			if (empty($errList) && $loggedin == 'yes') {
				$_SESSION['user'] = $_POST['usr'];
				$_SESSION['redirect'] = TRUE;
				header("Location: passthru.php");
			}
		}
		function test_input($data) {
		  $data = trim($data);
		  $data = stripslashes($data);
		  $data = htmlspecialchars($data);
		  return $data;
		}
		function create_table($user) {
			global $VAR, $VAR2;
			$servername = "localhost";
			$username = "root";
			$password = "";
			$dbname = "accounts";
			//Create connection_aborted
			$conn = mysqli_connect($servername, $username, $password, $dbname);
			//Check connection_aborted
			if (!$conn) {
				die("Connection failed: " . mysqli_connect_error());
			}
			
			$sql = "CREATE TABLE $user (  
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			name NVARCHAR(200) NOT NULL,
			pswd NVARCHAR(200) NOT NULL,
			email NVARCHAR(100),
			last_login datetime
			)";
			
			/*$userr = $user + "data"
			
			$sql1 = "CREATE TABLE $userr (
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			taskname NVCHAR(200) NOT NULL,
			tasktime INT NOT NULL
			)";
			*/
			if (mysqli_query($conn, $sql) === TRUE /*and mysqli_query($conn, $sql1) === TRUE) */) {
				$VAR2 = TRUE;
			} else {
				$VAR = 1;
			}
			mysqli_close($conn);
		}
		function create_account($user, $userpassword, $email) {
			$servername = "localhost";
			$username = "root";
			$password = "";
			$dbname = "accounts";
			$hasheduser = hash('ripemd160', $user);
			$hashedpswd = hash('sha512', $userpassword);
			//Create connection_aborted
			$conn = mysqli_connect($servername, $username, $password, $dbname);
			//Check connection_aborted
			if (!$conn) {
				die("Connection failed: " . mysqli_connect_error());
			}
			$sql = "INSERT INTO $user SET
			name = '$hasheduser', 
			pswd = '$hashedpswd',
			email = '$email';";
			$conn->query($sql);
			mysqli_close($conn);
		}
		function validate_inputs($user, $userpassword, $ruserpassword, $email) {
			global $errors, $VAR1;
			global $ErrMsg;
			global $alert;
			global $VAR;
			$alert = False;
			$servername = "localhost";
			$username = "root";
			$password = "";
			$dbname = "accounts";
			$user = test_input($user);
			$userpassword = test_input($userpassword);
			$hasheduser = hash('ripemd160', $user);
			//$errTable = 'no';
			//Create connection_aborted
			$conn = mysqli_connect($servername, $username, $password, $dbname);
			//Check connection_aborted
			if (!$conn) {
				die("Connection failed: " . mysqli_connect_error());
			}
			//$o = create_table($user);
			/*if ($VAR == 1) {
				$alert = True;
				array_push($ErrMsg, $errors[2]);
			}
			*/
			if ($ruserpassword !== $userpassword) {
				$alert = True;
				array_push($ErrMsg, $errors[1]);
			}
			if (strlen($userpassword) < 6) {
				$alert = True;
				array_push($ErrMsg, $errors[3]);
			}
			if ($user == "" or $username == "" or $userpassword == "" or $ruserpassword == "" or $email == "") {
				$alert = True;
				array_push($ErrMsg, $errors[0]);
			}
			if (strlen($email) > 2) {
				if (strpos($email, '@', 1) === FALSE) {
					$alert = True;
					array_push($ErrMsg, 'Invalid email');
				} else {
					if (strpos($email, '.',strpos($email, '@', 1)) === FALSE) {
						$alert = True;
						array_push($ErrMsg, 'Invalid email');
					}
				}
			}
			if (!$alert) {
				create_table($user);
				if ($VAR == 0) {
					create_account($user, $userpassword, $email);
					shell_exec("python sendemail.py $email");

				} else {
					$alert = True;
					array_push($ErrMsg, $errors[2]);
				}
			}
			if ($alert == TRUE) {
				$VAR1 = 1;
				//if ($errTable == 'yes') {
				//	$conn->query('DROP TABLE $user');
				//}
			}
			mysqli_close($conn);
		}
		
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if ($_POST['createorlogin'] == "create") {
				validate_inputs($_POST["usr"], $_POST["pass"], $_POST["rpass"], $_POST["email"]);
			} else {
				checkInputs();
			}
			
		}
		?>
		<a id = "pagetop"></a>
		<img id="logo" src="VTHacks_logo.png" alt="Your Time" height="105px" width="120px">
		<br>
		<div class="topnav">
			<a class="active" href="#CreateAccount">Home</a>
			<a href="tasks.php">My Tasks</a>
			<a href="about.php#about">About</a>
		</div>
		<div class = header>
			<div id = section1>
				<br>
				<div class = idk>
				Can't manage your time? Let us help.
			</div>
			<br><br>
			
			<table border = 0px, width = 100%;>
			<tr>
				<td width = 58% align = "right">
					<div id = "frontpage"><img src="frontpage.png" style="width:550px;height:500px;"></div>
				</td>
				<td align = "left">
					<div id = "form1">
						<form target="_self" method="POST">
							&nbsp;&nbsp;Email:<br>
							<input type="text" name="email"><br><br>
							&nbsp;&nbsp;Username:<br>
							<input type="text" name="usr"><br><br>
							&nbsp;&nbsp;Password:<br>
							<input type="password" name="pass"><br><br>
							&nbsp;&nbsp;Re-enter Password:<br>
							<input type="password" name="rpass"><br>
							<input type = "hidden" name = "createorlogin" value = "create">
							<br>
							<input type="submit" value="Create Account">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type = "button" onclick = "switchtosignin()" value = "Login">
							<br>
						</form>
						<?php
						if ($VAR2 == TRUE and $VAR1 != 1) {
							echo "<br><br>&nbsp;&nbsp;<strong>Account Created!</strong>";
						}
						if ($VAR1 == 1) {
							echo "<h4 id='error'><strong>Errors:</strong></h4>";
							for ($x = 0; $x <= count($ErrMsg)-1; $x++) {
								echo "<h4 id='error'><strong>- $ErrMsg[$x] <br></strong></h4>";
							}
						}
						?>
					</div>
					<div id = "form2">
						<form target = "_self" method = "POST">
							&nbsp;Username: <br><input type="text" name="usr"><br><br>
							&nbsp;Password: <br><input type="password" name="pass"><br><br>
							<input type = "hidden" name = "createorlogin" value = "login">
							<br>
							<input type = "submit" value = "Login">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" onclick = "switchtosignup()" value="Create Account">
							<br>
						</form>
						<?php
							if (!empty($errList)) {
								echo "<h2 id='error'><strong>Errors:</strong></h2>";
								for($x = 0; $x <= count($errList)-1; $x++) {
									echo "<h2 id='error'><strong>- $errList[$x]<br></strong></h2>";
								}
							} else {
								if ($loggedin == 'yes') {
									$_SESSION['user'] = $user = $_POST['usr'];
									$servername = "localhost";
									$username = "root";
									$password = "";
									$dbname = "accounts";
									//Create connection_aborted
									$conn = mysqli_connect($servername, $username, $password, $dbname);
									//Check connection_aborted
									if (!$conn) {
										die("Connection failed: " . mysqli_connect_error());
									}
									$sql = "SELECT email FROM accounts.$user;";
									$result = mysqli_query($conn, $sql);
									$email = mysqli_fetch_assoc($result);
									$_SESSION['email'] = $email;
									$_SESSION['redirect'] = TRUE;
									header("Location: passthru.php");
								}
							}
						?>
					</div>
				</td>
			</tr>
		</table>
		<br><br><br><br>
		</div>
		<div id = "section2">
			<br><br>
			<div id = "intro">The power of organization</div>
			
			<table border = 0px width = 100%>
				<tr>
					<td width = 50%;>
						<center><div id = "quote">“The key is not to prioritize what's on your schedule, but to schedule your priorities.” ~Steven Covey</div></center>
					</td>
						
					<td>
					<p>
						"Time management is hard. There are so many things to do, and it may be overwhelming to think about the huge stack of work waiting for you. So hard to choose when to do what! So easy to procrastinate! But with an organized schedule, time management is made simple, and this website does it all for you."
					</p>
					</td>
				</tr>
				<tr>
					<td width = 50%;>
					<center><div id = "quote">"In ovation that Xsites." - Kneesahn</center>
					</td>
				</tr>
			</table>
			<br><br>
		</div>
		<div id = "end">
		<br><br>
			<table border = 0px width = 100%>
				<tr>
					<td width = 33% align = "right">
						<div id = "ending">Take back <b>your</b> time:</div> 
					</td>
					
					<td>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button><a href="#pagetop" style = "text-decoration: none; color: white">Join us</a></button>
					</td>
					<td align = "right">
						<img src = "foot2.png" style = "width: 500px; height: 220px;" align = "right">
					</td>
					<td>
						<img src = "foot.png" style = "width: 300px; height: 220px;" align = "right">
					</td>
				</tr>
			</table>
		<br><br>
		</div>
		<script>
		function switchtosignin() {
			var x = document.getElementById("form1");
			var y = document.getElementById("form2");
			x.style.display = "none";
			y.style.display = "block";
		}
		
		function switchtosignup() {
			var x = document.getElementById("form1");
			var y = document.getElementById("form2");
			x.style.display = "block";
			y.style.display = "none";
		}
		</script>
	</body>
</html>