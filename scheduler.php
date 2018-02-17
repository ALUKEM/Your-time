<?php
session_start();
require("fpdf.php");
$datedifflist = array();
function priority($namelist) {
	global $datedifflist;
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "information_schema";
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
	$dbname = "information_schema";
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
	$pdf->Output();
	
}
priority($_SESSION['namelist']);
create_Schedule($_SESSION['namelist']);

?>
