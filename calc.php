<?php
//load config
require_once('config.php');
//init env
ini_set("default_charset","utf-8");
ini_set("display_errors", 1);
set_time_limit(600);
//lets parse input
$drivers_license_id      = $_POST["json"];
$drivers_license_idArray = json_decode($drivers_license_id);

$conn = mysqli_connect($DB_HOSTNAME, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE, $DB_PORT);

if (mysqli_connect_errno()) {
	die("Failed to connect to MySQL: ".mysqli_connect_error());
}
$currentYear = date('Y');
$prices = [];
foreach ($drivers_license_idArray as $driver_id) {
	$selectq = "select driver_license_issue_date_yy from drivers where drive_license_number = ".$driver_id;
	$result  = mysqli_query($conn, $selectq) or die("Query err ".mysqli_error());
	$field   = mysqli_field_count($conn);
	$driver_f= false;
	while($row = mysqli_fetch_array($result)){
		for ($i = 0; $i < $field; $i++) {
			$issue_year = $row[mysqli_fetch_field_direct($result, $i)->name];
			if ($currentYear - $issue_year  < 3) {
				$price = 4000*1.8;
			} else  {
				$price = 4000;
			}
			array_push($prices, $price);
			$driver_f = true;						
		}
	}
	if ($driver_f==false){
		array_push($prices, "license not found");
	}
}

echo json_encode($prices);

//echo "5";
//return "3";
?>
