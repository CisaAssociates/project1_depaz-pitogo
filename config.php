<?php 

// Database connection
$conn = new mysqli("localhost", "u347279731_depaz_pitogo", "DepazPitogo2025", "u347279731_depaz_pitogodb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
