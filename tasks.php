<?php
session_start()
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>My Tasks | Your Time</title>
		<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,100' rel='stylesheet' type='text/css'>
		<link href="home_style.css" type="text/css" rel="stylesheet">
	</head>
	<body>
		<?php
		$noTasks = 'no';
		$VAR1 = FALSE;
		if (key_exists('err', $_SESSION)) {
			if ($_SESSION['err']) {
				$_SESSION['errors'] = array();
				$_SESSION['err'] = FALSE;
			}
		}
		$desclist = $namelist = $datelist = array();
		$number = array();
		function create_task($taskname, $taskdesc, $duedate) {
			$servername = "localhost";
			$username = "root";
			$password = "";
			$dbname = "tasks";
			$user = $_SESSION['user'];
			//Create connection_aborted
			$conn = mysqli_connect($servername, $username, $password, $dbname);

			//Check connection_aborted
			if (!$conn) {
				die("Connection failed: " . mysqli_connect_error());
			}
			$tablename = 'tasks' . $user;
			$sql = "SELECT * FROM information_schema WHERE TABLE_NAME='$tablename' and TABLE_SCHEMA='tasks';";
			$result = mysqli_query($conn, $sql);
			if ($result === TRUE) {
				$sql = "SELECT * FROM tasks.$tablename WHERE taskname='$taskname';";
				if (mysqli_query($conn, $sql) === TRUE) {
					array_push($_SESSION['errors'], 'Task with that name already exists.');
				}
			} else {
				$sql = "CREATE TABLE $tablename (  
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				taskname NVARCHAR(50) NOT NULL,
				taskdesc NVARCHAR(300),
				duedate DATE NOT NULL
				)";
				$result = mysqli_query($conn, $sql);
				
				$sql = "SELECT * FROM tasks.$tablename";
				
				$result = mysqli_query($conn, $sql);
				while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
					if ($row['taskname'] == $taskname) {
						array_push($_SESSION['errors'], 'A task with that name already exists');
					}
				}
				if (strlen(ltrim(rtrim($taskname))) == 0) {
					array_push($_SESSION['errors'], 'Task names are required');
				}
				
				$actualdate = "";
				
				if (gettype(date_create_from_format('Y-m-d', $duedate)) !== "boolean") {
					$actualdate = date_format(date_create_from_format('Y-m-d', $duedate), 'F j, Y');
				} else {
					$actualdate == FALSE;
				}
				
				if ($duedate == ' ' or $actualdate == FALSE) {
					array_push($_SESSION['errors'], 'Due Dates are required');
				}
				
				if (count($_SESSION['errors']) == 0) {
					$sql = "INSERT INTO $tablename SET
					taskname = '$taskname',
					taskdesc = '$taskdesc',
					duedate = '$duedate';";
					$result = mysqli_query($conn, $sql);
				} else {
					$_SESSION['errShown'] = FALSE;
				}
			}
			mysqli_close($conn);
		}
		function test_input($data) {
		  $data = trim($data);
		  $data = stripslashes($data);
		  $data = htmlspecialchars($data);
		  return $data;
		}
		function showTasks($user) {
			global $datelist, $namelist, $desclist;
			$servername = "localhost";
			$username = "root";
			$password = "";
			$dbname = "tasks";
			//Create connection_aborted
			$conn = mysqli_connect($servername, $username, $password, $dbname);

			//Check connection_aborted
			if (!$conn) {
				die("Connection failed: " . mysqli_connect_error());
			}
			$tablename = 'tasks' . $user;
			$sql = "SELECT taskname FROM tasks.$tablename;";
			$listprep = mysqli_query($conn, $sql);
			while ($row = mysqli_fetch_array($listprep, MYSQL_ASSOC)) {
				array_push($namelist, $row['taskname']);  
			}
			$sql = "SELECT taskdesc FROM tasks.$tablename;";
			$listprep = mysqli_query($conn, $sql);
			while ($row = mysqli_fetch_array($listprep, MYSQL_ASSOC)) {
				if (key_exists('taskdesc', $row)) {
						array_push($desclist, $row['taskdesc']);
				} else {
					array_push($desclist, "");
				}					
			}
			$sql = "SELECT duedate FROM tasks.$tablename;";
			$listprep = mysqli_query($conn, $sql);
			while ($row = mysqli_fetch_array($listprep, MYSQL_ASSOC)) {
				$actualdate = date_format(date_create_from_format('Y-m-d', $row['duedate']), 'F j, Y');
				array_push($datelist, $actualdate);  
			}
		}
		function checkTasks($user) {
			global $noTasks;
			$servername = "localhost";
			$username = "root";
			$password = "";
			$dbname = "information_schema";
			//Create connection_aborted
			$conn = mysqli_connect($servername, $username, $password, $dbname);

			//Check connection_aborted
			if (!$conn) {
				die("Connection failed: " . mysqli_connect_error());
			}
			$var = 'tasks' . $user;
			$sql = "SELECT * FROM information_schema.TABLES WHERE TABLE_NAME='$var' and TABLE_SCHEMA='tasks';";
			$result = mysqli_query($conn, $sql);
			if ($result === FALSE) {
				$noTasks = 'yes';
			} else {
				$sql = "SELECT * FROM tasks.$var;";
				$result = mysqli_query($conn, $sql);
				if ($result === FALSE) {
					$noTasks = 'yes';
				}
			}
			$_SESSION['user'] = $user;
		}
		if (array_key_exists('user', $_SESSION)) {
			if ($_SESSION['loggedin']) {
			checkTasks($_SESSION['user']);
			}
		} else {
			$VAR1 = TRUE;
		}
		?>
		<div class="topnav">
			<a href="home.php#home">Home</a>
			<a href="home.php#about">About</a>
			<a class="active" href="tasks.php#tasks">My Tasks</a>
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
		<?php
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			create_task($_POST["tskname"], $_POST["tskdesc"], $_POST["duedate"]);
			checkTasks($_SESSION['user']);
			$_SESSION['err'] = TRUE;
		}
		if ($VAR1) {
			echo "<h3>You need to <a href='create_account.php'>create an account</a> first!</h3>";
			echo "<h3>Already have an account? <a href='login.php'>Log in!</a></h3>";
		} else {
			if ($noTasks == 'yes') {
				echo "<h1><strong>You have no tasks. Create one now!</strong></h1>";
			} else {
				$errors = $_SESSION['errors'];
				if (!empty($errors)) {
					echo "<h2 id='error'><strong>Errors:</strong></h2>";
					for($x = 0; $x <= count($errors)-1; $x++) {
						echo "<h2 id='error'><strong>- $errors[$x]<br></strong></h2>";
					}
					$_SESSION['err'] = TRUE;
				}
				echo '<table style="width: 100%; margin: 10px;">';
				echo '	<tr>
							<th colspan="1" style="font-family: \'Roboto\'; font-size: 20pt; color: #DFF123; margin-top: 5px;">Task #</th>
							<th colspan="1" style="font-family: \'Roboto\'; font-size: 20pt; color: #0099ff; margin-top: 5px;">Task Name</th>
							<th colspan="1" style="font-family: \'Roboto\'; font-size: 20pt; color: #0099ff; margin-top: 5px;">Task Description</th>
							<th colspan="1" style="font-family: \'Roboto\'; font-size: 20pt; color: #0099ff; margin-top: 5px;">Due By</th>
							<th colspan="3" style="font-family: \'Roboto\'; font-size: 20pt; color: #0099ff; margin-top: 5px;">Options<th>
						</tr>';
				showTasks($_SESSION['user']);
				for ($x = 0; $x <= count($namelist) - 1; $x++) {
					array_push($number, $x + 1);
					$actualnumber = $number[$x];
					$name = $namelist[$x];
					$desc = $desclist[$x];
					if (strlen($desc)-1 > 14) {
						$wordStart = strpos($desc, ' ', 10);
						if ($wordStart < 20 and $wordStart > 13) {
							$desc = substr($desc, 0, $wordStart) . '...';
						} else {
							$desc = substr($desc, 0, 20);
						}
					}
					$date = $datelist[$x];
					echo "	<tr>
								<th colspan='1' style='font-family: \"Roboto\"; font-size: 20pt; color: #B6B6B6; margin-top: 5px;'>$actualnumber</th>
								<th colspan='1' style='font-family: \"Roboto\"; font-size: 20pt; color: white; margin-top: 5px;'>$name</th>
								<th colspan='1' style='font-family: \"Roboto\"; font-size: 20pt; color: white; margin-top: 5px; overflow: hidden; width: 18%;'>$desc</th>
								<th colspan='1' style='font-family: \"Roboto\"; font-size: 20pt; color: white; margin-top: 5px;'>$date</th>
								<th> <button style='margin-right: 10px; margin-top: 5px; font-size: 18pt;' id='edit'>More Info</button> <th>
								<th> <button style='margin-right: 10px; margin-top: 5px; font-size: 18pt;' id='edit'>Edit</button> <th>
								<th> <button style='margin-right: 10px; margin-top: 5px; font-size: 18pt;' id='del'>Delete</button> <th>
								
							</tr>";
				}
				echo '</table>';
			}
			echo '<!-- Trigger/Open The Modal -->
			<button style="margin-left: 20px; margin-top: 5px;" id="myBtn">Create Task</button>

			<!-- The Modal -->
			<div id="myModal" class="modal">

				<!-- Modal content -->
				<div class="modal-content">
					<div class="modal-header">
						<span class="close">&times;</span>
						<h2>Create Task</h2>
					</div>
					<div class="modal-body">
						<form target="_self" method="POST">
							<h4> Task Name: (max: 50 chars) </h4><input id="taskcreation" type="text" name="tskname"><br>
							<h4> Task Description: (max: 300 chars) </h4><input id="taskcreation" type="text" name="tskdesc"><br>
							<h4>Date: </h4><input id="taskcreation" type="date" name="duedate"><br>
							<input id="taskcreation1" type="submit" value="Create Task">
						</form>
					</div>
				</div>
			<script>
			// Get the modal
			var modal = document.getElementById("myModal");

			// Get the button that opens the modal
			var btn = document.getElementById("myBtn");

			// Get the <span> element that closes the modal
			var span = document.getElementsByClassName("close")[0];

			// When the user clicks on the button, open the modal 
			btn.onclick = function() {
				modal.style.display = "block";
			}

			// When the user clicks on <span> (x), close the modal
			span.onclick = function() {
				modal.style.display = "none";
			}

			// When the user clicks anywhere outside of the modal, close it
			window.onclick = function(event) {
				if (event.target == modal) {
					modal.style.display = "none";
				}
			}
			</script>
			</div>';
		}
		?>
	</body>
</html>
