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
			$_SESSION['starttime'] = $hourstart + $minutestart;
			$text = "$hour[$d]" . ":" . "$minute[$d]";
			$time = explode(":", $text);
			$hourend = $time[0] * 60 * 60 * 1000;
			$minuteend = $time[1] * 60 * 1000;
			if ($daychange == 1) {
				$hourend = $hourend + 24 * 60 * 60 * 1000;
			}
			$_SESSION['endtime'] = $hourend + $minuteend;
		}
		
		$scheduletxt = $scheduletxt . $combinedarray1[$x] . "  |  " . $h . ":" . $m . " ~ " . $h1 . ":" . $m1 . "  |  Priority: " . $x_value . "\n";
		$c++;
	}
	
}
priority($_SESSION['namelist']);
create_Schedule($_SESSION['namelist']);
$_SESSION['milliseconds'] = $_SESSION['endtime'] - $_SESSION['starttime'];
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Task Commenced | Your Time</title>
		<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,100' rel='stylesheet' type='text/css'>
		<link href="css.css" type="text/css" rel="stylesheet">
	</head>
	<body>
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
	<div id="butt0"><button class="btn" onclick="window.location='fin.php';">Finish Task!</button></div>
	</body>
</html>