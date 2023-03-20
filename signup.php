<?php
$servername = "localhost";
$username = "root";
$email = "";
$password = "";
$dbname = "wpl";
$user = $_POST["user"];
$email = $_POST["email"];
$password = $_POST["pass"];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO signup (username, email, password)
VALUES ('".$user."','".$email."','".$password."')";

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