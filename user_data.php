<?php
include('config.php');

$attendanceData = [];
$user = null;
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["schoolid"])) {
    $schoolid = $conn->real_escape_string($_POST["schoolid"]);

    $userResult = $conn->query("SELECT * FROM users WHERE schoolid = '$schoolid'");
    if ($userResult && $userResult->num_rows > 0) {
        $user = $userResult->fetch_assoc();
        $user_id = $user["id"];

        $logsResult = $conn->query("SELECT * FROM attendance_logs WHERE user_id = $user_id ORDER BY timestamp DESC");
        if ($logsResult) {
            while ($row = $logsResult->fetch_assoc()) {
                $attendanceData[] = $row;
            }
        }
    } else {
        $error = "School ID not found.";
    }
}

// AJAX Handler
if (isset($_GET['ajax']) && $_GET['ajax'] == '1' && isset($_GET['schoolid'])) {
    $schoolid = $conn->real_escape_string($_GET["schoolid"]);
    $logs = [];

    $userResult = $conn->query("SELECT id FROM users WHERE schoolid = '$schoolid'");
    if ($userResult && $userResult->num_rows > 0) {
        $user_id = $userResult->fetch_assoc()["id"];
        $logsResult = $conn->query("SELECT * FROM attendance_logs WHERE user_id = $user_id ORDER BY timestamp DESC");

        if ($logsResult) {
            while ($row = $logsResult->fetch_assoc()) {
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
    <title>Student Attendance Lookup</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
            color: #fff;
        }

        .container-box {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            color: #000;
            max-width: 1000px;
            margin: auto;
        }

        .btn-custom {
            background-color: #2c5364;
            color: white;
        }

        .btn-custom:hover {
            background-color: #203a43;
        }

        .modal-header {
            background-color: #2c5364;
            color: #fff;
        }

        .table-striped>tbody>tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>

<div class="container-box mt-5">
    <h3 class="text-center mb-4">Student Attendance Details</h3>

    <?php if ($user): ?>
        <h5 class="mb-3">Attendance Logs for School ID: <strong><?= htmlspecialchars($user["schoolid"]) ?></strong></h5>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Date & Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="attendance-body">
                    <?php foreach ($attendanceData as $log): ?>
                        <tr>
                            <td><?= date("F j, Y - h:i A", strtotime($log['timestamp'])) ?></td>
                            <td><?= htmlspecialchars($log['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($attendanceData)): ?>
                        <tr><td colspan="2">No attendance records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php elseif (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
</div>

<!-- Modal -->
<div class="modal fade" id="schoolIdModal" tabindex="-1" aria-labelledby="schoolIdModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="schoolIdModalLabel">Enter School ID</h5>
            </div>
            <div class="modal-body">
                <input type="text" name="schoolid" class="form-control" placeholder="e.g. 2023001234" required>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-custom">View Records</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
<?php if (!$user): ?>
    const schoolIdModal = new bootstrap.Modal(document.getElementById('schoolIdModal'), {
        backdrop: 'static',
        keyboard: false
    });
    schoolIdModal.show();
<?php else: ?>
    const schoolid = "<?= $user['schoolid'] ?>";

    function fetchAttendance() {
        fetch(`?ajax=1&schoolid=${schoolid}`)
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('attendance-body');
                tbody.innerHTML = "";

                if (data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="2">No attendance records found.</td></tr>`;
                } else {
                    data.forEach(row => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${new Date(row.timestamp).toLocaleString()}</td>
                                <td>${row.status}</td>
                            </tr>
                        `;
                    });
                }
            })
            .catch(err => console.error("Error loading attendance logs:", err));
    }

    setInterval(fetchAttendance, 3000); // auto-refresh every 3 seconds
    fetchAttendance(); // initial call
<?php endif; ?>
</script>
</body>
</html>
