<?php 
$mysqli = new mysqli_connect("43.224.34.5", "root", "ab7109789", "test");
// check connection
if($mysqli->connect_errno) {
	die('Could not connect: ' . $mysqli->error);
}


$name = $_GET["name"];
$sqlt = "insert into test.data (name) values ('메롱')";
mysqli_query($mysqli, $sqlt);
mysqli_close($mysqli);
?>