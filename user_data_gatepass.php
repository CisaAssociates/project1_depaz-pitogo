<?php
include('config.php');

$logs = [];
$userInfo = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schoolid'])) {
    $schoolid = $conn->real_escape_string($_POST['schoolid']);
    $userQuery = $conn->query("SELECT * FROM users WHERE schoolid = '$schoolid' LIMIT 1");

    if ($userQuery && $userQuery->num_rows > 0) {
        $userInfo = $userQuery->fetch_assoc();
        $user_id = $userInfo['id'];

        $logQuery = $conn->query("SELECT * FROM gatepass_logs WHERE user_id = $user_id ORDER BY entry_id DESC");
        if ($logQuery) {
            while ($row = $logQuery->fetch_assoc()) {
                $logs[] = $row;
            }
        }
    }
}

// Handle AJAX request
if (isset($_GET['ajax']) && $_GET['ajax'] === '1' && isset($_GET['schoolid'])) {
    $schoolid = $conn->real_escape_string($_GET['schoolid']);
    $logs = [];

    $userQuery = $conn->query("SELECT id FROM users WHERE schoolid = '$schoolid' LIMIT 1");
    if ($userQuery && $userQuery->num_rows > 0) {
        $user = $userQuery->fetch_assoc();
        $user_id = $user['id'];

        $logQuery = $conn->query("SELECT * FROM gatepass_logs WHERE user_id = $user_id ORDER BY entry_id DESC");
        if ($logQuery) {
            while ($row = $logQuery->fetch_assoc()) {
                $logs[] = $row;
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($logs);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Gatepass Logs</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
      min-height: 100vh;
      padding: 30px;
      font-family: 'Segoe UI', sans-serif;
      color: white;
    }
    .container {
      background: #fff;
      color: #000;
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }
    .modal-header {
      background-color: #2c5364;
      color: white;
    }
  </style>
</head>
<body>

<div class="container">
  <h2 class="text-center mb-4">Gatepass Logs</h2>

  <?php if ($userInfo): ?>
    <div class="mb-4">
      <h5>User Information:</h5>
      <p><strong>Name:</strong> <?= htmlspecialchars($userInfo['name']) ?></p>
      <p><strong>School ID:</strong> <?= htmlspecialchars($userInfo['schoolid']) ?></p>
      <p><strong>Role:</strong> <?= htmlspecialchars($userInfo['role']) ?></p>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>Gatepass ID</th>
            <th>Entry Time</th>
            <th>Exit Time</th>
          </tr>
        </thead>
        <tbody id="log-body">
          <?php foreach ($logs as $log): ?>
            <tr>
              <td><?= $log['entry_id'] ?></td>
              <td><?= $log['entry_time'] ?></td>
              <td><?= $log['exit_time'] ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($logs)): ?>
            <tr><td colspan="3">No logs found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
      <div class="alert alert-danger">No user found with that School ID.</div>
    <?php endif; ?>
  <?php endif; ?>
</div>

<!-- School ID Modal -->
<div class="modal fade" id="schoolidModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Enter School ID</h5>
      </div>
      <div class="modal-body">
        <input type="text" name="schoolid" class="form-control" placeholder="Enter School ID" required>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">View Logs</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  <?php if (!isset($userInfo)): ?>
    const schoolidModal = new bootstrap.Modal(document.getElementById('schoolidModal'), {
      backdrop: 'static',
      keyboard: false
    });
    window.onload = () => schoolidModal.show();
  <?php else: ?>
    const schoolId = "<?= $userInfo['schoolid'] ?>";

    function fetchLogs() {
      fetch(`?ajax=1&schoolid=${schoolId}`)
        .then(response => response.json())
        .then(data => {
          const tbody = document.getElementById('log-body');
          tbody.innerHTML = "";

          if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="3">No logs found.</td></tr>`;
          } else {
            data.forEach(log => {
              const row = `<tr>
                <td>${log.entry_id}</td>
                <td>${log.entry_time}</td>
                <td>${log.exit_time}</td>
              </tr>`;
              tbody.innerHTML += row;
            });
          }
        })
        .catch(error => console.error("Error fetching logs:", error));
    }

    setInterval(fetchLogs, 3000); // Fetch every 3 seconds
    fetchLogs(); // Initial call
  <?php endif; ?>
</script>
</body>
</html>
