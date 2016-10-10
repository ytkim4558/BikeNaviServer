<?php 
$link = mysqli_connect("localhost", "root", "7109789", "test");
// check connection
if($link->connect_errno) {
	die('Could not connect: ' . mysql_error());
}


$name = $_GET["name"];
$sqlt = "insert into test.data (name) values ('메롱')";
mysqli_query($link, $sqlt);
mysqli_close($link);
?>