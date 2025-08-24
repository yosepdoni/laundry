<?php
$servername = "localhost";
$username = "root";
$password = "";
$db = "tresialaundry";

// hosting
// $servername = "localhost";
// $username = "u999595957_tresialaundry";
// $password = "Tresialaundry123#";
// $db = "u999595957_tresialaundry";

// Create connection
$conn = new mysqli($servername, $username, $password, $db);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>