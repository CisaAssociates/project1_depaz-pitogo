<?php
// Set content type to JSON
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// MySQL connection settings
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'attendance_system';

// Connect to MySQL database
function get_db_connection() {
    global $host, $user, $password, $dbname;
    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
    }

    return $conn;
}

// Function to register RFID
function register_rfid($uid) {
    $conn = get_db_connection();

    // Log the received UID for debugging
    file_put_contents('php://stderr', "Registering UID: $uid\n");

    // Check if UID already exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE rfid_tag = ?");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return ["status" => "fail", "message" => "UID already registered."];
    }

    // Get the latest user without an RFID tag
    $stmt = $conn->prepare("SELECT id FROM users WHERE rfid_tag IS NULL ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        return ["status" => "fail", "message" => "No unassigned users available."];
    }

    $user_id = $row['id'];

    // Log the user ID to check the result
    file_put_contents('php://stderr', "User ID to update: $user_id\n");

    // Update the user's RFID tag
    $stmt = $conn->prepare("UPDATE users SET rfid_tag = ? WHERE id = ?");
    $stmt->bind_param("si", $uid, $user_id);
    if ($stmt->execute()) {
        return ["status" => "success", "message" => "UID {$uid} assigned to user ID {$user_id}."];
    } else {
        return ["status" => "error", "message" => "Failed to update user RFID tag."];
    }
}

// API route for registering RFID
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get the data from Flask (sent as a query parameter)
    if (isset($_GET['uid'])) {
        $uid = $_GET['uid'];

        // Register the RFID
        $response = register_rfid($uid);

        // Return the response as JSON
        echo json_encode($response);
    } else {
        echo json_encode(["status" => "fail", "message" => "UID not provided"]);
    }
} else {
    echo json_encode(["status" => "fail", "message" => "Invalid request method. Use GET."]);
}
?>
