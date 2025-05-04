<?php
session_start();
$conn = new mysqli("localhost", "root", "", "attendance_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Logout functionality
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// 👉 AJAX for table list
if (isset($_GET['fetch']) && $_GET['fetch'] == 1) {
    $sql = "SELECT gl.entry_id, gl.entry_time, gl.exit_time, u.name, u.face_image, u.role 
            FROM gatepass_logs gl
            LEFT JOIN users u ON gl.user_id = u.id
            ORDER BY gl.entry_time DESC
            LIMIT 20";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $imagePath = "uploads/" . ($row['face_image'] ?? 'default.png');
            echo "<tr>
                    <td>{$row['entry_id']}</td>
                    <td>{$row['name']}</td>
                    <td><img src='{$imagePath}' alt='Face' class='log-img'></td>
                    <td>{$row['role']}</td>
                    <td>{$row['entry_time']}</td>
                    <td>{$row['exit_time']}</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No gatepass logs available.</td></tr>";
    }

    $conn->close();
    exit();
}

// 👉 AJAX for profile info
if (isset($_GET['profile']) && $_GET['profile'] == 1) {
    $sql = "SELECT gl.entry_id, gl.entry_time, gl.exit_time, u.name, u.face_image, u.role 
            FROM gatepass_logs gl
            LEFT JOIN users u ON gl.user_id = u.id
            ORDER BY gl.entry_time DESC
            LIMIT 1";

    $result = $conn->query($sql);
    $data = $result->fetch_assoc();

    if ($data) {
        $imagePath = "uploads/" . ($data['face_image'] ?? 'default.png');
        echo "
            <div class='text-center'>
                <img src='{$imagePath}' class='profile-img mb-3'>
                <h4>{$data['name']}</h4>
                <p><strong>Role:</strong> {$data['role']}</p>
                <p><strong>Entry Time:</strong> {$data['entry_time']}</p>
                <p><strong>Exit Time:</strong> {$data['exit_time']}</p>
            </div>";
    } else {
        echo "<p class='text-center'>No recent gatepass log.</p>";
    }

    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Gatepass Logs + Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
      min-height: 100vh;
      font-family: 'Segoe UI', sans-serif;
      padding: 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
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

    .top-buttons {
      width: 100%;
      max-width: 1300px;
      display: flex;
      justify-content: space-between;
      margin-bottom: 15px;
    }

    .btn-custom {
      padding: 10px 20px;
      font-size: 16px;
      border: none;
      border-radius: 5px;
      color: white;
      background-color: #2c5364;
      transition: background 0.3s;
    }

    .btn-custom:hover {
      background-color: #203a43;
    }

    @media (max-width: 768px) {
      .container-flex {
        flex-direction: column;
      }

      .top-buttons {
        flex-direction: column;
        gap: 10px;
        align-items: stretch;
      }
    }
  </style>
</head>
<body>

<!-- Top Buttons -->
<div class="top-buttons">
  <a href="index.php" class="btn btn-custom">← Back</a>
  <form method="POST">
    <button type="submit" name="logout" class="btn btn-danger">Logout</button>
  </form>
</div>

<div class="container-flex">
  <!-- Gatepass Log Table -->
  <div class="logs-container">
    <h2 class="form-title mb-3">Live Gatepass Logs</h2>
    <div class="table-responsive">
      <table class="table table-striped table-bordered">
        <thead class="table-dark">
          <tr>
            <th>Gatepass ID</th>
            <th>Name</th>
            <th>Face</th>
            <th>Role</th>
            <th>Entry Time</th>
            <th>Exit Time</th>
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
  function fetchLogs() {
    $.get("?fetch=1", function (data) {
      $("#logs-body").html(data);
    });
  }

  function fetchProfile() {
    $.get("?profile=1", function (data) {
      $("#profile-box").html(data);
    });
  }

  setInterval(() => {
    fetchLogs();
    fetchProfile();
  }, 3000);

  fetchLogs(); // initial load
  fetchProfile(); // initial profile load
</script>
</body>
</html>
