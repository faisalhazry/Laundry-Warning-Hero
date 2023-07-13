<?php

$hostname = "localhost";
$username = "root";
$password = "";
$database = "lwhero";

$conn = mysqli_connect($hostname, $username, $password, $database);


if(!$conn) {
	die ("connection failed: " . mysqli_connect_error());
}

echo "Database connection is OK <br>";


if(isset($_POST["temperature"]) && isset($_POST["humidity"])) {
	$t = $_POST["temperature"];
	$h = $_POST["humidity"];


$sql = "INSERT INTO `dht11`( `temperature`, `humidity`) VALUES (".$t.",".$h.")";

if (mysqli_query($conn, $sql)) {
	echo "New record created successfully";
} else {
	echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

}
	


?>
