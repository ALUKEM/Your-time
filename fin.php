<?php
session_start();
$datedifflist = array();
function priority($namelist) {
	global $datedifflist;
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
	$_SESSION['priorityList'] = array();
	$tablename = "tasks" . $user;
	$datelist = array();
	$sql = "SELECT duedate FROM tasks.$tablename;";
	$result = mysqli_query($conn, $sql);
	while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
		array_push($datelist, $row['duedate']);
	}
	for($x = 0; $x <= count($namelist) - 1; $x++) {
		$date = date_create($datelist[$x]);
		$currentdate = date_create(date("Y-m-d"));
		$diff = date_diff($currentdate, $date, TRUE);
		array_push($datedifflist, $diff->format("%a"));
	}
}
function create_Schedule($namelist) {
	global $datedifflist;
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
	$prioritytxt = "";
	for($x=0; $x <= count($namelist)-1; $x++) {
		$prioritytxt = $prioritytxt . $namelist[$x] . " - " . $datedifflist[$x] . "\n";
	}
	$timelist = array();
	for($x=0; $x <= count($namelist)-1; $x++) {
		$tablename = $user . $namelist[$x];
		$sql = "SELECT * FROM tasks.$tablename WHERE id=(SELECT max(id) FROM tasks.$tablename);";
		$result = mysqli_query($conn, $sql);
		while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
			array_push($timelist, $row['timetaken']);
		}
	}
	date_default_timezone_set('EST');
	$currenthour = date("H") + 0;
	$currentminute = date("i") + 0;
	$_SESSION['timefinish'] = $currenthour*60*60000 + $currentminute*60000;
	$combinedarray = $weirdarray = array(); // combining the time taken with the priority
	for($x=0; $x <= count($namelist)-1; $x++) {
		$y = $datedifflist[$x];
		$z = $timelist[$x];
		$a = $namelist[$x];
		$combinedarray["$z"] = $y;
		$combinedarray1["$z"] = $a;
		$weirdarray["$z"] = $z;
	}
	asort($combinedarray);
	asort($combinedarray1);
	asort($weirdarray);
	$timelist = array_keys($weirdarray);
	$combinedarray1wkeys = array_keys($combinedarray1);
	$hour = array();
	$minute = array();
	$hour[0] = $currenthour;
	$minute[0] = $currentminute;
	for($x=1; $x <= count($namelist); $x++) {
		$y = $x - 1;
		$hour[$y] = $hour[$y] + 0;
		$minute[$y] = $minute[$y] + 0;
		$hour[$x] = floor($timelist[$y]) + $hour[$y];
		$minute[$x] = ($timelist[$y]-floor($timelist[$y]))*60+$minute[$y];
		if ($minute[$x] > 59) {
			$minute[$x] = $minute[$x] - 60;
			$hour[$x] = $hour[$x] + 1;
		}
		$daychange = 0;
		if ($hour[$x] > 23 ) {
			$hour[$x] = $hour[$x]-24;
			$daychange = 1;
		}
	}
	
	
	$scheduletxt = "";
	$c = 0;
	foreach($combinedarray as $x=>$x_value) {
		$d = $c + 1;
		if ($hour[$c] < 10) {
			$h = "0" . "$hour[$c]";
		} else {
			$h = "$hour[$c]";
		}
		if ($minute[$c] < 10) {
			$m = "0" . "$minute[$c]";
		} else {
			$m ="$minute[$c]";
		}
		if ($hour[$d] < 10) {
			$h1 = "0" . "$hour[$d]";
		} else {
			$h1 = "$hour[$d]";
		}
		if ($minute[$d] < 10) {
			$m1 = "0" . "$minute[$d]";
		} else {
			$m1 ="$minute[$d]";
		}
		
		if ($c == 0) {
			$text = "$hour[$c]" . ":" . "$minute[$c]";
			$time = explode(":", $text);
			$hourstart = $time[0] * 60 * 60 * 1000;
			$minutestart = $time[1] * 60 * 1000;
			$text = "$hour[$d]" . ":" . "$minute[$d]";
			$time = explode(":", $text);
			$hourend = $time[0] * 60 * 60 * 1000;
			$minuteend = $time[1] * 60 * 1000;
			if ($daychange == 1) {
				$hourend = $hourend + 24 * 60 * 60 * 1000;
			}
		}
		if ($c == 0) {
			$_SESSION['firsttask'] = $combinedarray1[$x];
		}
		$scheduletxt = $scheduletxt . $combinedarray1[$x] . "  |  " . $h . ":" . $m . " ~ " . $h1 . ":" . $m1 . "  |  Priority: " . $x_value . "\n";
		$c++;
	}
	
}
priority($_SESSION['namelist']);
create_Schedule($_SESSION['namelist']);
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Task Finish | Your Time</title>
		<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,100' rel='stylesheet' type='text/css'>
		<link href="css.css" type="text/css" rel="stylesheet">
	</head>
	<body>
	<a href="home2.php">
		<img id="logo" src="VTHacks_logo.png" alt="Your Time" height="105px" width="120px">
	</a>
	<div class="topnav">
		<a href="home2.php#home">Home</a>
		<a class="active" href="tasks.php">My Tasks</a>
		<a href="about.php#about">About</a>
		<?php
		if (array_key_exists('user', $_SESSION)) {
			if ($_SESSION['loggedin']) {
			echo "<a href='logout.php'>Log Out</a>";
			}
		}
		?>
	</div>
	<?php
	$task = $_SESSION['timefinish'] - $_SESSION['starttime'];
	$timedif = $_SESSION['timefinish'] - $_SESSION['endtime'];
	$timedif = ($timedif / 60000) / 60;
	$task = $task / 60000;
	$task = $task / 60;
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
	$tablename1 = $_SESSION['user'] . $_SESSION['firsttask'];
	$sql = "SELECT timetaken FROM tasks.$tablename1 WHERE id=(SELECT max(id) FROM tasks.$tablename1);";
	$result = mysqli_query($conn, $sql);
	$time1 = mysqli_fetch_assoc($result);
	$time1 = $time1['timetaken'];
	$_SESSION['timeoftask'] = $newtime = ($task + $time1)/2;
	$currentdate = date("Y-m-d");
	$sql = "INSERT INTO tasks.$tablename1 SET
	timetaken = $newtime,
	date = '$currentdate';";
	$result = mysqli_query($conn, $sql);
	$taskname = $_SESSION['firsttask'];
	unset($_SESSION['namelist']["$taskname"]);
	$tablename = 'tasks' . $_SESSION['user'];
	$sql = "DELETE FROM tasks.$tablename WHERE taskname = '$taskname';";
	$result = mysqli_query($conn, $sql);
	
	?>
	<h1>Analysis:</h1>
	<?php
	$roundedtask = round($task, 2);
	$roundedtaskmin = $roundedtask * 60;
	$roundeddif = round($timedif, 2);
	$roundeddifmin = $roundeddif * 60;
	echo "<ul>
				<li style='font-size: 20px;'>You took about $roundedtask hours ($roundedtaskmin minutes).</li>
				<li style='font-size: 20px;'>This is about $roundeddif hours ($roundeddifmin minutes) more than the target time. Your target time will be adjusted.</li>
				<li style='font-size: 20px;'>Your task will be deleted but your time data will be saved under that task name. To retrieve this data, just create a task with the same name but without an ESTF.</li>
		</ul>";
	?>
	</body>
</html>
