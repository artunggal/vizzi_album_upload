<?php

function Connect() {
	$servername = 'localhost';
	$username = 'tunggal';
	$password = 'V@r13nt$';
	$dbname = "Vizzi Music";

	// Create connection

	$conn = mysqli_connect($servername, $username, $password, $dbname);

	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 
	else {
		echo "Connection successful";
	}

	return $conn;
}

?>