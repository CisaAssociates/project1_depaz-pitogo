<?php
$host = 'localhost';
$db = 'u347279731_depaz_pitogodb';
$user = 'u347279731_depaz_pitogo';
$pass = 'DepazPitogo2025';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handle the request based on the action parameter
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    if ($action === 'get_users') {
        getUsers($pdo);
    } elseif ($action === 'get_user_by_rfid') {
        $rfid = isset($_GET['rfid']) ? $_GET['rfid'] : '';
        if ($rfid) {
            getUserByRfid($pdo, $rfid);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Missing RFID UID']);
        }
    } elseif ($action === 'insert_gatepass') {
        $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
        if ($user_id) {
            insertGatepass($pdo, $user_id);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Missing user ID']);
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

    foreach ($users as &$user) {
        if ($user['face_data'] !== null) {
            $user['face_data'] = base64_encode($user['face_data']);
        }
    }

    echo json_encode($users);
}

// Get user by RFID
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

// Insert gatepass log (entry/exit)
function insertGatepass($pdo, $user_id)
{
    $today = date('Y-m-d');
    $query = "SELECT * FROM gatepass_logs WHERE user_id = :user_id AND DATE(entry_time) = :today AND exit_time IS NULL";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $user_id, 'today' => $today]);

    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($existing) {
        // Update exit time
        $update_query = "UPDATE gatepass_logs SET exit_time = NOW() WHERE entry_id = :entry_id";
        $update_stmt = $pdo->prepare($update_query);
        $update_stmt->execute(['entry_id' => $existing['entry_id']]);
        echo json_encode(['status' => 'success', 'message' => 'Exit logged']);
    } else {
        // Insert entry
        $insert_query = "INSERT INTO gatepass_logs (user_id) VALUES (:user_id)";
        $insert_stmt = $pdo->prepare($insert_query);
        $insert_stmt->execute(['user_id' => $user_id]);
        echo json_encode(['status' => 'success', 'message' => 'Entry logged']);
    }
}
?>
