<?php
session_start();
include('config.php');

// 👉 Handle logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// 👉 Handle AJAX fetch
if (isset($_GET['fetch']) && $_GET['fetch'] == 1) {
    $sql = "SELECT id, username, email, role FROM mods ORDER BY id DESC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>" . htmlspecialchars($row['username']) . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                    <td>" . htmlspecialchars($row['role']) . "</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No staff found.</td></tr>";
    }
    $conn->close();
    exit();
}

// 👉 Handle Insert
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_mod'])) {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = htmlspecialchars($_POST['role']);

    $stmt = $conn->prepare("INSERT INTO mods (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $role);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Staff Management</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <style>
    body {
      background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
      min-height: 100vh;
      padding: 30px;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      justify-content: center;
      align-items: flex-start;
    }

    .table-container {
      background-color: #fff;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
      max-width: 1000px;
      width: 100%;
    }

    h2 {
      color: #2c5364;
      margin-bottom: 20px;
      text-align: center;
    }

    .btn-custom {
      background-color: #2c5364;
      color: white;
      border: none;
    }

    .btn-custom:hover {
      background-color: #203a43;
    }

    .top-buttons {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }

    .top-buttons .btn-danger {
      background-color: #c0392b;
    }

    .top-buttons .btn-danger:hover {
      background-color: #922b21;
    }
  </style>
</head>
<body>

<div class="table-container">
  <!-- 🔹 Back & Logout buttons -->
  <div class="top-buttons">
    <a href="index.php" class="btn btn-custom">← Back</a>
    <form method="POST" class="d-inline">
      <button type="submit" name="logout" class="btn btn-danger">Logout</button>
    </form>
  </div>

  <!-- 🔹 Title + Add Button -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Staff & Moderators</h2>
    <button class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#createModal">+ Create Staff</button>
  </div>

  <!-- 🔹 Table -->
  <div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
        </tr>
      </thead>
      <tbody id="mods-table-body">
        <!-- AJAX content -->
      </tbody>
    </table>
  </div>
</div>

<!-- 🔹 Create Staff Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Register New Staff</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Username</label>
          <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Role</label>
          <select name="role" class="form-select" required>
            <option value="Staff">Staff</option>
            <option value="Admin">Admin</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="add_mod" class="btn btn-custom">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
  function fetchMods() {
    $.get(window.location.href.split('?')[0] + "?fetch=1", function(data) {
      $('#mods-table-body').html(data);
    }).fail(function() {
      $('#mods-table-body').html("<tr><td colspan='4'>Error loading data.</td></tr>");
    });
  }

  fetchMods();
  setInterval(fetchMods, 5000);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
