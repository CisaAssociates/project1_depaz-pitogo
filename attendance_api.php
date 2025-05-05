<?php
$host = 'localhost';
$db = 'u347279731_depaz_pitogodb';
$user = 'u347279731_depaz_pitogo';
$pass = 'DepazPitogo2025';

try {
    // Create PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handle the request based on the action parameter
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    if ($action === 'get_users') {
        // Get all users for face recognition
        getUsers($pdo);
    } elseif ($action === 'get_user_by_rfid') {
        // Get user by RFID UID
        $rfid = isset($_GET['rfid']) ? $_GET['rfid'] : '';
        if ($rfid) {
            getUserByRfid($pdo, $rfid);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Missing RFID UID']);
        }
    } elseif ($action === 'insert_attendance') {
        // Insert attendance log
        $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
        $status = isset($_POST['status']) ? $_POST['status'] : '';
        if ($user_id && $status) {
            insertAttendance($pdo, $user_id, $status);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Missing user ID or status']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

// Get all users for face recognition
function getUsers($pdo)
{
    $query = "SELECT id, name, face_data FROM users";
    $stmt = $pdo->query($query);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Loop through each user and base64 encode the face data
    foreach ($users as &$user) {
        if ($user['face_data'] !== null) {
            // Encode face_data to base64
            $user['face_data'] = base64_encode($user['face_data']);
        }
    }

    echo json_encode($users);
}

// Get user by RFID UID
function getUserByRfid($pdo, $rfid)
{
    $query = "SELECT id, name FROM users WHERE rfid_tag = :rfid";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['rfid' => $rfid]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode($user);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
    }
}

// Insert attendance log
function insertAttendance($pdo, $user_id, $status)
{
    $query = "INSERT INTO attendance_logs (user_id, status) VALUES (:user_id, :status)";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $user_id, 'status' => $status]);
    echo json_encode(['status' => 'success']);
}
?>
