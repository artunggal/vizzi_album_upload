<?php
/* Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
require 'connect-db.php';

$songtarget = "";

$conn = Connect();

if($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Escape user inputs for security
	$name = htmlspecialchars($_POST["fname"]);
	$email = htmlspecialchars($_POST["email"]);
	$twitter = htmlspecialchars($_POST["twitter"]);

	$album = htmlspecialchars($_POST["albumTitle"]);
	$artist = htmlspecialchars($_POST["artistName"]);
	$year = htmlspecialchars($_POST["year"]);
	$genre = htmlspecialchars($_POST["genre"]);

// contains non numeric characters in year
	if (!ctype_digit($year)) {
		die("Sorry, invalid year");
	}


//Image Uploading

	$arttarget = __DIR__ . "/uploads/" . basename($_FILES['imageUpload']['name']);

	$Filename=$_FILES['imageUpload']['name'];
	$type=$_FILES['imageUpload']['type'];
	$size=$_FILES['imageUpload']['size'];

	if($type != "image/jpg" && $type != "image/jpeg" && $type != "image/png") {
		die("Sorry, only JPG, JPEG and PNG files are allowed.");
	}

	if ($_FILES["fileToUpload"]["size"] > 500000) {
	    echo "Sorry, your file is too large.";
	}

	$tmpFilePath = $_FILES['imageUpload']['tmp_name'];

	if ($tmpFilePath != "") {
		//Writes the Filename to the server
		move_uploaded_file($tmpFilePath, $arttarget) or die("Sorry, there was a problem uploading your file.");
		chmod($arttarget, 0666);
	}

// SONG Uploading

// number of song files uploaded
$total = count($_FILES['songToUpload']['name']);

$albumtarget = "";

	// If the total number of songs is greater than one, it creates a path. 
	// Otherwise no path is created

if($total > 0) {

	$singertarget = __DIR__ . "/albums/" . basename($artist);
	if (is_dir($singertarget) == false) {
		mkdir($singertarget, 0666, true);
	}

	$albumtarget = __DIR__ . "/albums/" . "/$artist/" . basename($album);
	if (is_dir($albumtarget) == false) {
		mkdir($albumtarget, 0666, true);
		}
}
else {
	die("Must submit at least 1 song.");
}

// Checks if each file uploaded is a song
// Note if file size is returning 0, check Max file upload size in php.ini

for($i=0; $i<$total; $i++) {
	$songType=$_FILES['songToUpload']['type'][$i];

	if($songType != "audio/mp3" && $songType != "audio/m4a") {
	    die("Sorry, only mp3 and m4a files are allowed.");
	}
}

// Iterates through the songs and creates paths for each

for($j=0; $j<$total; $j++) {
	$songFile=$_FILES['songToUpload']['name'][$j];

	//Path for song
	$songtarget = __DIR__ . "/albums/" . "/$artist/" . "/$album/" . basename($songFile);

	$tmpSongPath = $_FILES['songToUpload']['tmp_name'][$j];

	if ($tmpSongPath != "") {
	//Writes the Filename to the server
	move_uploaded_file($tmpSongPath, $songtarget) or die("Sorry, there was a problem uploading your file.");
	chmod($songtarget, 0666);
	}

	//Get Name of Song
	$songName = htmlspecialchars($_POST['myInputs'][$j]);

	//Insert Song Info into Database
	$insert = $conn->query("INSERT INTO song_upload (song_Name, artist_Name, album_Name, genre, song_Path)
	VALUES ('$songName', '$artist', '$album', '$genre', '$songtarget')") ;

	if ($insert == true) {
	    echo "Records added successfully.";
	} else{
	    echo "ERROR: Could not execute $sql. " . mysqli_error($conn);
	}
}


	//Insert Album Info into Database
	$insert = $conn->query("INSERT INTO album_upload (name, email, twitter, album_Title, album_Artist, year, genre, album_Path, image_Path) VALUES ('$name', '$email', '$twitter', '$album', '$artist', '$year', '$genre', '$albumtarget', '$arttarget')");
	if ($insert == true) {
	    echo "Records added successfully.";
	} else{
	    echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
	}

}



 
// close connection
$conn->close();
?>