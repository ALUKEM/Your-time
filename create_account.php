<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="css.css">
		<title>Home | Your Time</title>
	</head>
	<body>
	<?php 
		$errors = array("All fields are required.","Passwords do not match.","Error with creating account. Most likely, an account with that user name already exists.","Password requirements not fulfilled.");
		$alert = FALSE;
		$ErrMsg = array();
		$VAR = 0;
		function test_input($data) {
		  $data = trim($data);
		  $data = stripslashes($data);
		  $data = htmlspecialchars($data);
		  return $data;
		}
		function create_table($user) {
			global $VAR;
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
			email NVARCHAR(100)
			)";
			if (mysqli_query($conn, $sql) === TRUE) {
				echo "Account Created! <br>";
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
			global $errors;
			global $ErrMsg;
			global $alert;
			global $VAR;
			$alert = False;
			$servername = "localhost";
			$username = "root";
			$password = "";
			$dbname = "accounts";
			$user = test_input($user);
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
			if ($user == "" or $username == "" or $userpassword == "" or $ruserpassword == "") {
				$alert = True;
				array_push($ErrMsg, $errors[0]);
			}
			if (!$alert) {
				create_table($user);
				if ($VAR == 0) {
					create_account($user, $userpassword, $email);
				} else {
					$alert = True;
					array_push($ErrMsg, $errors[2]);
				}
			}
			if ($alert == TRUE) {
				echo "<script type='text/javascript'>alert('One or more fields are invalid.');</script>";
				/*echo "<div id = error>Errors: <br></div>";
				for ($x = 0; $x <= count($ErrMsg)-1; $x++) {
					echo "<div id = error>- $ErrMsg[$x] <br></div>";
				}*/
				//if ($errTable == 'yes') {
				//	$conn->query('DROP TABLE $user');
				//}
			}
			mysqli_close($conn);
		}
		
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			validate_inputs($_POST["usr"], $_POST["pass"], $_POST["rpass"], $_POST["email"]);
		}
		?>
		<div class = "header"></div>
		<div id = "section1">
		<br><br><br><br><br><br><br><br>
		<div id = "idk">
			Can't manage your time? Let us help.
		</div>
		<br><br><br><br>
		
		<table border = 0px width = 100%>
			<tr>
				<td width = 58% align = "right">
					<div id = "frontpage"><img src="frontpage.png" style="width:550px;height:500px;"></div>
				</td>
				<td align = "left">
					<div id = "form1">
						<form target="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
							Email: <br><input type="text" name="email"><br><br>
							Username: <br><input type="text" name="usr"><br><br>
							Password: <br><input type="password" name="pass"><br><br>
							Re-enter Password: <br><input type="password" name="rpass"><br>
							<br>
							<input type="submit" value="Create Account">
							<br>
							<?php 
							if ($alert) {
								echo "<br>";
								echo "<div id = error>Errors: <br></div>";
								for ($x = 0; $x <= count($ErrMsg)-1; $x++) {
									echo "<div id = error>- $ErrMsg[$x] <br></div>";
								}
								echo "<br>";
							}
							?>
						</form>
					</div>
				</td>
			</tr>
		</table>
		<br><br><br><br>
		</div>
		<div id = "section2">
			<br><br>
			<div id = "intro">The power of Machine Learning</div>
			<br><br><br>
			<table border = 0px width = 100%>
				<tr>
					<td width = 50%;>
						<center><div id = "quote">“Machine learning will increase productivity <br>throughout the supply chain.” ~Dave Waters</div></center>
					</td>
						
					<td>
					<p>
						"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
					</p>
					</td>
				</tr>
				<tr>
					<td colspan = 2>
					<p>
						"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
						"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim"
					</p>
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
						<div id = "ending">Take back <i>Your</i> time:</div> 
					</td>
					<td>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button>Join us</button>
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
	</body>
</html>