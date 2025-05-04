<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('config.php');

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, email, password, role FROM mods WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify the hashed password
            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true); // Prevent session fixation
                $_SESSION['user'] = $user['email'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on user role
                if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'staff') {
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Access restricted to Admin and Staff only.";
                }
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "User not found.";
        }

        $stmt->close();
    } else {
        $error = "Please fill in both fields.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
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

    .card {
      background-color: #ffffff;
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      max-width: 400px;
      width: 100%;
    }

    .form-control {
      border-radius: 10px;
    }

    .btn-custom {
      background-color: #2c5364;
      color: white;
      border: none;
      border-radius: 10px;
      width: 100%;
    }

    .btn-custom:hover {
      background-color: #203a43;
    }

    .error {
      color: red;
      font-weight: 600;
      margin-bottom: 10px;
      text-align: center;
    }

    .form-title {
      text-align: center;
      margin-bottom: 20px;
      font-weight: bold;
      color: #2c5364;
    }

    @media (max-width: 768px) {
      .card {
        padding: 20px;
      }
    }
  </style>
</head>
<body>

<div class="card">
  <h3 class="form-title">Login</h3>
  <?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="mb-3">
      <input type="email" name="email" class="form-control" placeholder="Email" required>
    </div>
    <div class="mb-3">
      <input type="password" name="password" class="form-control" placeholder="Password" required>
    </div>
    <button type="submit" class="btn btn-custom">Login</button>
  </form>

  <div class="text-center mt-3">
    <a href="register.php" class="text-decoration-none" style="color: #2c5364;">Don't have an account? Register</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
