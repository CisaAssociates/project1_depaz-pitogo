<?php
session_start();

include "config.php";

if (!isset($_SESSION['user']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 👉 AJAX for log table
if (isset($_GET['fetch']) && $_GET['fetch'] == 1) {
    $sql = "SELECT al.log_id, al.timestamp, al.status, u.name, u.face_image, u.role 
            FROM attendance_logs al
            LEFT JOIN users u ON al.user_id = u.id
            ORDER BY al.timestamp DESC
            LIMIT 20";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $imagePath = "uploads/" . ($row['face_image'] ?? 'default.png');
            echo "<tr>
                    <td>{$row['log_id']}</td>
                    <td>{$row['name']}</td>
                    <td><img src='{$imagePath}' alt='Face' class='log-img'></td>
                    <td>{$row['role']}</td>
                    <td>{$row['status']}</td>
                    <td>{$row['timestamp']}</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No attendance logs available.</td></tr>";
    }

    $conn->close();
    exit();
}

// 👉 AJAX to fetch latest profile not yet displayed
if (isset($_GET['profile']) && $_GET['profile'] == 1) {
    $sql = "SELECT al.log_id, al.timestamp, al.status, u.name, u.face_image, u.role 
            FROM attendance_logs al
            LEFT JOIN users u ON al.user_id = u.id
            WHERE al.display IS NULL OR al.display = 'NA'
            ORDER BY al.timestamp DESC
            LIMIT 1";

    $result = $conn->query($sql);
    $data = $result->fetch_assoc();

    if ($data) {
        $logId = $data['log_id'];
        $imagePath = "uploads/" . ($data['face_image'] ?? 'default.png');
        echo "
            <div class='text-center' data-logid='{$logId}'>
                <img src='{$imagePath}' class='profile-img mb-3'>
                <h4>{$data['name']}</h4>
                <p><strong>Role:</strong> {$data['role']}</p>
                <p><strong>Status:</strong> {$data['status']}</p>
                <p><strong>Time:</strong> {$data['timestamp']}</p>
            </div>";
    } else {
        echo "<p class='text-center'>No recent log to display.</p>";
    }

    $conn->close();
    exit();
}

// 👉 AJAX to mark a log as displayed
if (isset($_POST['mark_displayed'])) {
    $logId = intval($_POST['log_id']);
    $conn->query("UPDATE attendance_logs SET display = 'DISPLAYED' WHERE log_id = $logId");
    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Attendance Logs + Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
      min-height: 100vh;
      font-family: 'Segoe UI', sans-serif;
      padding: 70px;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      position: relative;
    }

    .container-flex {
      display: flex;
      gap: 20px;
      width: 100%;
      max-width: 1300px;
      flex-wrap: wrap;
    }

    .logs-container, .profile-container {
      background-color: #ffffff;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      padding: 20px;
    }

    .logs-container {
      flex: 3;
      max-height: 600px;
      overflow-y: auto;
    }

    .profile-container {
      flex: 1;
      max-height: 600px;
    }

    .form-title {
      text-align: center;
      font-weight: bold;
      color: #2c5364;
    }

    .log-img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 8px;
    }

    .profile-img {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 50%;
      border: 4px solid #2c5364;
    }

    .back-btn {
      position: absolute;
      top: 20px;
      left: 20px;
      background-color: #007bff;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      font-size: 16px;
      text-decoration: none;
    }

    .back-btn:hover {
      background-color: #0056b3;
    }

    .logout-btn {
      position: absolute;
      top: 20px;
      right: 20px;
    }

    @media (max-width: 768px) {
      .container-flex {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>

<!-- Back Button -->
<a href="index.php" class="back-btn">Back</a>

<!-- Logout Button -->
<form method="POST" class="logout-btn">
  <button type="submit" name="logout" class="btn btn-danger">Logout</button>
</form>

<div class="container-flex">
  <!-- Attendance Log Table -->
  <div class="logs-container">
    <h2 class="form-title mb-3">Live Attendance Logs</h2>
    <div class="table-responsive">
      <table class="table table-striped table-bordered">
        <thead class="table-dark">
          <tr>
            <th>Log ID</th>
            <th>Name</th>
            <th>Face</th>
            <th>Role</th>
            <th>Status</th>
            <th>Timestamp</th>
          </tr>
        </thead>
        <tbody id="logs-body">
          <!-- AJAX-filled rows -->
        </tbody>
      </table>
    </div>
  </div>

  <!-- Profile Section -->
  <div class="profile-container">
    <h2 class="form-title mb-3">Latest Profile</h2>
    <div id="profile-box">
      <!-- AJAX-filled profile -->
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
  let lastDisplayedId = null;

  function fetchLogs() {
    $.get("?fetch=1", function (data) {
      $("#logs-body").html(data);
    });
  }

  function fetchProfile() {
    $.get("?profile=1", function (data) {
      $("#profile-box").html(data);

      const logDiv = document.querySelector("#profile-box div[data-logid]");
      if (logDiv) {
        const currentLogId = logDiv.getAttribute("data-logid");
        if (lastDisplayedId !== currentLogId) {
          lastDisplayedId = currentLogId;

          // Delay 4 seconds before marking as DISPLAYED
          setTimeout(() => {
            $.post("", { mark_displayed: 1, log_id: currentLogId });
          }, 10000);
        }
      }
    });
  }

  setInterval(() => {
    fetchLogs();
    fetchProfile();
  }, 3000);

  fetchLogs();    // initial load
  fetchProfile(); // initial profile load
</script>
</body>
</html>
