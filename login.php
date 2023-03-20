<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wpl";
$name = $_POST["fname"];
$email = $_POST["lname"];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO login (username, password)
VALUES ('".$name."','".$email."')";

if ($conn->query($sql) === TRUE) {
  // echo "New record created successfully";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

header("Location: index.html");
die();


// echo $_POST["fname"]; 
// echo $_POST["lname"];
?>