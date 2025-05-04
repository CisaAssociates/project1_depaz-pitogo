<?php
session_start();
$conn = new mysqli("localhost", "root", "", "attendance_system");

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header("Location: login.php");
    exit();
}

// Logout logic
if (isset($_POST['logout'])) {
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    header("Location: login.php"); // Redirect to login page
    exit();
}

// Connection check
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle AJAX fetch request for live logs
if (isset($_GET['fetch']) && $_GET['fetch'] == 1) {
    $sql = "SELECT id, rfid_tag, schoolid, name, face_image, role FROM users ORDER BY id DESC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $imagePath = "uploads/" . htmlspecialchars($row['face_image']);
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>" . ($row['rfid_tag'] ?? 'Waiting...') . "</td>
                    <td>{$row['schoolid']}</td>
                    <td>{$row['name']}</td>
                    <td><img src='{$imagePath}' alt='Face' style='width: 60px; height: 60px; object-fit: cover; border-radius: 8px;'></td>
                    <td>{$row['role']}</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No users yet.</td></tr>";
    }
    $conn->close();
    exit();
}

// Handle form submission for face registration
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $schoolid = htmlspecialchars($_POST['schoolid']);
    $name = htmlspecialchars($_POST['name']);
    $role = htmlspecialchars($_POST['role']);
    $faceBlob = null;
    $imageFilename = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['image']['tmp_name'];
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $originalName = time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($tmpName, $uploadDir . $originalName);
        $faceBlob = file_get_contents($uploadDir . $originalName);
        $imageFilename = $originalName;
    }

    // Remove users without RFID
    $conn->query("DELETE FROM users WHERE rfid_tag IS NULL");

    $stmt = $conn->prepare("INSERT INTO users (schoolid, name, role, face_data, face_image) VALUES (?, ?, ?, ?, ?)");
    $null = NULL;
    $stmt->bind_param("sssbs", $schoolid, $name, $role, $null, $imageFilename);
    $stmt->send_long_data(3, $faceBlob);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Face Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', sans-serif;
      padding: 20px;
    }

    .container-flex {
      display: flex;
      flex-direction: row;
      gap: 30px;
      width: 100%;
      max-width: 1200px;
      flex-wrap: wrap;
    }

    .form-container {
      background-color: #ffffff;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      max-width: 400px;
      width: 100%;
      flex: 1;
    }

    .logs-container {
      background-color: #ffffff;
      padding: 20px;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      flex: 2;
      overflow: auto;
      max-height: 500px;
    }

    .form-title {
      text-align: center;
      margin-bottom: 20px;
      font-weight: bold;
      color: #2c5364;
    }

    .btn-custom {
      background-color: #2c5364;
      color: white;
      border: none;
    }

    .btn-custom:hover {
      background-color: #203a43;
    }

    /* Back Button */
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
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .back-btn:hover {
      background-color: #0056b3;
    }

    @media (max-width: 768px) {
      .container-flex {
        flex-direction: column;
      }

      .logs-container {
        max-height: none;
      }
    }
  </style>
</head>
<body>

  <!-- Back Button (Positioned on the left side) -->
  <a href="index.php" class="back-btn">Back</a>

  <!-- Logout Button -->
  <form method="POST" style="position: absolute; top: 20px; right: 20px;">
    <button type="submit" name="logout" class="btn btn-danger">Logout</button>
  </form>

  <div class="container-flex">
    <div class="form-container">
      <h2 class="form-title">Register Your Face</h2>
      <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label for="name" class="form-label">Full Name</label>
          <input type="text" name="name" class="form-control" id="name" required>
          <label for="schoolid" class="form-label">School ID</label>
          <input type="text" name="schoolid" class="form-control" id="schoolid" required>
        </div>

        <div class="mb-3">
          <label for="role" class="form-label">Select Role</label>
          <select name="role" class="form-select" id="role" required>
            <option value="">Choose...</option>
            <option value="Student">Student</option>
            <option value="Staff">Staff</option>
            <option value="Visitor">Visitor</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="image" class="form-label">Upload Face Image</label>
          <input type="file" name="image" class="form-control" id="image" accept="image/*" required>
        </div>

        <div class="d-grid">
          <button type="submit" class="btn btn-custom">Register</button>
        </div>
      </form>
    </div>

    <!-- Live Logs Section -->
    <div class="logs-container">
      <h2 class="form-title">Live User Logs</h2>
      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>RFID Tag</th>
              <th>School ID</th>
              <th>Name</th>
              <th>Face</th>
              <th>Role</th>
            </tr>
          </thead>
          <tbody id="logs-body">
            <!-- Loaded via AJAX -->
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script>
    function fetchLogs() {
      $.ajax({
        url: window.location.pathname + "?fetch=1",
        method: "GET",
        success: function (data) {
          $("#logs-body").html(data);
        },
        error: function () {
          $("#logs-body").html("<tr><td colspan='6'>Unable to load logs.</td></tr>");
        }
      });
    }

    setInterval(fetchLogs, 5000);
    fetchLogs(); // Load immediately on page load
  </script>
</body>
</html>
