<?php
session_start();
require("fpdf.php");
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
		if ($hour[$x] > 23 ) {
			$hour[$x] = $hour[$x]-24;
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
			$_SESSION['starttime'] = "$hour[$c]" . ":" . "$minute[$c]";
			$_SESSION['endtime'] = "$hour[$d]" . ":" . "$minute[$d]";
		}
		
		$scheduletxt = $scheduletxt . $combinedarray1[$x] . "  |  " . $h . ":" . $m . " ~ " . $h1 . ":" . $m1 . "  |  Priority: " . $x_value . "\n";
		$c++;
	}
	//pdf generation
	$pdf = new FPDF('p', 'in', 'Letter');
	$pdf->SetTitle('YourTimeSchedule');
	$pdf->AddFont('Roboto', '','Roboto-Regular.php');
	$pdf->AddFont('Roboto', 'b','Roboto-Black.php');
	$pdf->SetFont('Roboto','b',20);
	$pdf->SetMargins(1,1.2);
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(TRUE);
	$pdf->Cell(0, .35, "Priorities:");
	$pdf->SetFont('Roboto','',20);
	$pdf->SetX(1);
	$pdf->MultiCell(0, .35, "\n$prioritytxt");
	$pdf->AddPage();
	$pdf->SetFont('Roboto','b',20);
	$pdf->Cell(0, .35, "Schedule:");
	//add official schedule
	$pdf->SetFont('Roboto','',20);
	$pdf->SetX(1);
	$pdf->MultiCell(0, .35, "\n$scheduletxt");
	$pdf->Output();
	
}
priority($_SESSION['namelist']);
create_Schedule($_SESSION['namelist']);

?>
