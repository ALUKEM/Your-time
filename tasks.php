<?php
session_start()
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>My Tasks | Your Time</title>
		<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,100' rel='stylesheet' type='text/css'>
		<link href="css.css" type="text/css" rel="stylesheet">
	</head>
	<body>
		<?php
		$noTasks = 'no';
		$tasknamesli = array();
		$VAR1 = FALSE;
		$timelist = array();
		//require('fpdf.php');
		if (key_exists('err', $_SESSION)) {
			if ($_SESSION['err']) {
				$_SESSION['errors'] = array();
				$_SESSION['err'] = FALSE;
			}
		}
		$_SESSION['namelist'] = array();
		$desclist = $namelist = $datelist = array();
		$number = array();
		
		function donothing() {
				;
		}
		
		function killtask($taskname) {
			$servername = "localhost";
			$username = "root";
			$password = "";
			$dbname = "tasks";
			$user = $_SESSION["user"];
			
			$conn = mysqli_connect($servername, $username, $password, $dbname);
			
			if (!$conn) {
				die("Connection failed: " . mysqli_connect_error());
			}
			
			$tablename = "tasks" . $user;$tablename = "tasks" . $user;
			$sql = "DELETE FROM tasks.$tablename WHERE taskname = '$taskname';";
			$result = mysqli_query($conn, $sql);
			$tablename1 = $user . $taskname;
			$sql = "DROP TABLE $tablename1;";
			$result = mysqli_query($conn, $sql);
			mysqli_close($conn);
		}
		function killtask1($taskname) {
			$servername = "localhost";
			$username = "root";
			$password = "";
			$dbname = "tasks";
			$user = $_SESSION["user"];
			
			$conn = mysqli_connect($servername, $username, $password, $dbname);
			
			if (!$conn) {
				die("Connection failed: " . mysqli_connect_error());
			}
			
			$tablename = "tasks" . $user;$tablename = "tasks" . $user;
			$sql = "DELETE FROM tasks.$tablename WHERE taskname = '$taskname';";
			$result = mysqli_query($conn, $sql);
			mysqli_close($conn);
		}
		
		function checktask($taskname) {
			global $tasknamesli;
			$inside = false;
			$servername = "localhost";
			$username = "root";
			$password = "";
			$dbname = "tasks";
			$user = $_SESSION["user"];

			$conn = mysqli_connect($servername, $username, $password, $dbname);
			
			if (!$conn) {
				die("Connection failed ". mysqli_connect_error());
			}
			
			$tablename = "tasks" . $user;
			
			$sql = "SELECT taskname FROM tasks.$tablename;";
			
			$result = mysqli_query($conn, $sql);
			while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
				array_push($tasknamesli, $row['taskname']);
			}
			
			foreach($tasknamesli as $x) {
				if ($x === $taskname) {
					$inside = true;
				}
			}
			
			return $inside;
			
			mysqli_close($conn);
	
		}
	
		function edit_task($taskname, $taskdesc, $duedate, $taskid) {
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
			$sql = "SELECT taskname FROM tasks.$tablename WHERE taskname='$taskid';";
			$result = mysqli_query($conn, $sql);
			if ($result === FALSE) {
				array_push($_SESSION['errors'], 'Task does not exist');
			} else {
				$sql = "DELETE FROM tasks.$tablename WHERE taskname='$taskid';";
				$result = mysqli_query($conn, $sql);
				create_task($taskname, $taskdesc, $duedate);
			}
		}
		
		function create_task($taskname, $taskdesc, $duedate, $time) {
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
				
				$time = trim($time, 'hour');
				$time = trim($time, 'hours');
				$time = trim($time, 'hr');
				$time = trim($time, 'hrs');
				$time = trim($time, ' ');
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
				if (str_replace(" ", "", $taskname) !== $taskname) {
					array_push($_SESSION['errors'], 'A task name cannot contain spaces');
				}
				$time = $time + 0.0;
				$taskname1 = strtolower($taskname);
				$tablename1 = $user . $taskname1;
				$sql = "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME='$tablename1';";
				$result = mysqli_query($conn, $sql);
				$autodate = FALSE;
				if ($result !== FALSE) {
					$sql = "SELECT * FROM tasks.$tablename1;";
					$result = mysqli_query($conn, $sql);
					if ($result === FALSE) {
						if ($time == 0 or $time == "" ) {
							array_push($_SESSION['errors'], 'ESTFs are required');
						}
					} else {
						$autodate = TRUE;
					}
				} else {
					if ($time == 0 or $time == "" ) {
						array_push($_SESSION['errors'], 'ESTFs are required');
					}
				}
				if (count($_SESSION['errors']) == 0) {
					$sql = "INSERT INTO $tablename SET
					taskname = '$taskname',
					taskdesc = '$taskdesc',
					duedate = '$duedate';";
					$result = mysqli_query($conn, $sql);
					$tablename1 = $user . $taskname;
					$sql = "CREATE TABLE $tablename1 (  
					id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
					timetaken DOUBLE NOT NULL,
					date DATE NOT NULL
					)";
					$result = mysqli_query($conn, $sql);
					$currentdate = date("Y-m-d");
					if (!$autodate) {
						$sql = "INSERT INTO $tablename1 SET
						timetaken = '$time',
						date = '$currentdate';";
					} else {
						$sql = "SELECT timetaken FROM tasks.$tablename1 WHERE id=(SELECT max(id) FROM tasks.$tablename);";
						$result = mysqli_query($conn, $sql);
						$time1 = mysqli_fetch_assoc($result);
						$sql = "INSERT INTO $tablename1 SET
						timetaken = '$time1',
						date = '$currentdate';";
					}
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
			global $datelist, $namelist, $desclist, $timelist;
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
			if (count($namelist) > 0) {	
				for($x=0; $x <= count($namelist) - 1; $x++) {
					$tablename1 = $user . $namelist[$x];
					$sql = "SELECT * FROM tasks.$tablename1 WHERE id=(SELECT max(id) FROM tasks.$tablename1);";
					$listprep = mysqli_query($conn, $sql);
					while ($row = mysqli_fetch_array($listprep, MYSQL_ASSOC)) {
						array_push($timelist, $row['timetaken']);
					}
				}
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
		<a href="home.php">
			<img id="logo" src="VTHacks_logo.png" alt="Your Time" height="105px" width="120px">
		</a>
		<div class="topnav">
			<a href="home.php#home">Home</a>
			<a href="home.php#about">About</a>
			<a class="active" href="tasks.php">My Tasks</a>
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
		<?php
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if ($_SESSION['editing'] == 'no') { 
				$_SESSION["editing"] = "no";
				create_task($_POST["tskname"], $_POST["tskdesc"], $_POST["duedate"], $_POST["time"]);
				checkTasks($_SESSION['user']);
				$_SESSION['err'] = TRUE;
				
				
			}
			if ($_SESSION['editing'] == 'yes') {
				$_SESSION["editing"] = "no";
				edit_task($_POST["tskname"], $_POST["tskdesc"], $_POST["duedate"],$_POST["idnum"]);
				checkTasks($_SESSION['user']);
				
			}
			if ($_SESSION['editing'] == 'delete') {
				$_SESSION["editing"] = "no";
				if (checktask($_POST['deltaskname'])) {
					killtask($_POST['deltaskname']);
				} else {
					array_push($_SESSION["errors"], "Task does not exist");
				}	
			}
			if ($_SESSION['editing'] == 'deleteonly') {
				$_SESSION["editing"] = "no";
				if (checktask($_POST['deltaskname'])) {
					killtask1($_POST['deltaskname']);
				} else {
					array_push($_SESSION["errors"], "Task does not exist");
				}	
			}
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
							<th colspan="1" style="font-family: \'Roboto\'; font-size: 20pt; color: #0099ff; margin-top: 5px;">Time to Finish (hours)</th>
						</tr>';
				showTasks($_SESSION['user']);
				$_SESSION['namelist'] = $namelist;
				if (count($namelist) > 0) {	
					$user = $_SESSION['user'];
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
					for($x=0; $x <= count($namelist) - 1; $x++) {
						$tablename1 = $user . $namelist[$x];
						$sql = "SELECT * FROM tasks.$tablename1 WHERE id=(SELECT max(id) FROM tasks.$tablename1);";
						$listprep = mysqli_query($conn, $sql);
						while ($row = mysqli_fetch_array($listprep, MYSQL_ASSOC)) {
							array_push($timelist, $row['timetaken']);
						}
					}
				}
				for ($x = 0; $x <= count($namelist) - 1; $x++) {
					array_push($number, $x + 1);
					$actualnumber = $number[$x];
					$name = $namelist[$x];
					$desc = $desclist[$x];
					$time = $timelist[$x];
					if (strlen($desc)-1 > 14) {
						$wordStart = strpos($desc, ' ', 10);
						if ($wordStart < 20 and $wordStart > 13) {
							$desc = substr($desc, 0, $wordStart) . '...';
						} else {
							$desc = substr($desc, 0, 20);
						}
					}
					$date = $datelist[$x];
					$time = $timelist[$x];
					echo "	<tr>
								<th colspan='1' style='font-family: \"Roboto\"; font-size: 20pt; color: #B6B6B6; margin-top: 5px;'>$actualnumber</th>
								<th colspan='1' style='font-family: \"Roboto\"; font-size: 20pt; color: white; margin-top: 5px;'>$name</th>
								<th colspan='1' style='font-family: \"Roboto\"; font-size: 20pt; color: white; margin-top: 5px; overflow: hidden; width: 18%;'>$desc</th>
								<th colspan='1' style='font-family: \"Roboto\"; font-size: 20pt; color: white; margin-top: 5px;'>$date</th>
								<th colspan='1' style='font-family: \"Roboto\"; font-size: 20pt; color: white; margin-top: 5px;'>$time</th>
								
							</tr>";
				}
				echo '</table>';
			}
			echo '<!-- Trigger/Open The Modal -->
			<button id="myBtn">Create Task</button>
			<div style="margin-left: 15px;">
				<h2>Options:</h2>
				<!--<form action="edittask.php" id="taskOptions">
					<input type="submit" value="Edit Tasks" />
				</form>-->
				<form action="deletetask.php" id = "taskOptions">
					<input type = "submit" value = "Delete task">
				</form>
				<form action="deletetask1.php" id = "taskOptions">
					<input type = "submit" value = "Delete task AND saved info">
				</form>
				<form target="_blank" action="scheduler.php" id = "taskOptions">
					<input type = "submit" value = "Generate Schedule">
				</form>
				<form action="finish.php" id = "taskOptions">
					<input type = "submit" value = "Commence Schedule">
				</form>
			</div>
			<!-- Create Task Modal -->
			<div id="myModal" class="modal">

				<!-- Modal content -->
				<div class="modal-content">
					<div class="modal-header">
						<span class="close">&times;</span>
						<h2>Create Task</h2>
					</div>
					<div class="modal-body">
						<form target="_self" method="POST">
							<h4>*Task Name: (max: 50 chars NO SPACES) </h4><input id="taskcreation" type="text" name="tskname"><br>
							<h4> Task Description: (max: 300 chars) </h4><input id="taskcreation" type="text" name="tskdesc"><br>
							<h4>*Due Date: </h4><input id="taskcreation" type="date" name="duedate"><br>
							<h4>*Estimated Time to Finish: (in hours)</h4><input id="taskcreation" type="text" name="time"><br>
							<p style="font-size: 12pt; font-family: \'Roboto\';"> *-Required, but ESTF may be omitted if saved data still exists.<p>
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
