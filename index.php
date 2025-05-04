<?php
session_start();
include('config.php');

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['user']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
      font-family: 'Segoe UI', sans-serif;
      min-height: 100vh;
      margin: 0;
      padding: 20px;
      position: relative;
      overflow-x: hidden;
    }

    .logout-btn {
      position: absolute;
      top: 20px;
      right: 20px;
      background-color: #dc3545;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      z-index: 100;
    }

    .logout-btn:hover {
      background-color: #c82333;
    }

    .slider-container {
      margin-top: 80px;
      position: relative;
    }

    .slider-nav {
      text-align: center;
      margin-bottom: 20px;
    }

    .slider-btn {
      background-color: #fff;
      border: none;
      padding: 10px 20px;
      font-size: 22px;
      border-radius: 8px;
      margin: 0 10px;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      transition: background-color 0.3s ease;
    }

    .slider-btn:hover {
      background-color: #ddd;
    }

    .slider-wrapper {
      display: flex;
      overflow-x: auto;
      scroll-behavior: smooth;
      gap: 20px;
      padding: 10px;
    }

    .slider-wrapper::-webkit-scrollbar {
      display: none;
    }

    .floating-card {
      background-color: white;
      border-radius: 16px;
      flex-shrink: 0;
      width: 280px;
      height: 420px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      overflow: hidden;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .floating-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.25);
    }

    .floating-card img {
      width: 100%;
      height: 65%;
      object-fit: cover;
      border-radius: 16px 16px 0 0;
    }

    .card-body {
      padding: 15px;
      text-align: center;
      font-weight: 700;
      font-size: 18px;
      color: #333;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 35%;
    }

    @media (max-width: 768px) {
      .floating-card {
        width: 240px;
        height: 380px;
      }

      .card-body {
        font-size: 16px;
        padding: 12px;
      }
    }

    @media (max-width: 576px) {
      .floating-card {
        width: 200px;
        height: 340px;
      }

      .card-body {
        font-size: 14px;
        padding: 10px;
      }
    }
  </style>
</head>
<body>

<!-- Logout Button -->
<form method="POST">
  <button type="submit" name="logout" class="logout-btn">Logout</button>
</form>

<!-- Slider Navigation -->
<div class="slider-nav">
  <button id="slideLeft" class="slider-btn">&larr;</button>
  <button id="slideRight" class="slider-btn">&rarr;</button>
</div>

<!-- Slider Section -->
<div class="slider-container">
  <div id="slider" class="slider-wrapper">

    <a href="register_user.php" class="text-decoration-none text-dark">
      <div class="floating-card">
        <img src="assets/register.png" alt="Register">
        <div class="card-body">Register Users</div>
      </div>
    </a>

    <a href="attendance_log.php" class="text-decoration-none text-dark">
      <div class="floating-card">
        <img src="assets/attendances.png" alt="Attendance">
        <div class="card-body">Attendance</div>
      </div>
    </a>

    <a href="gatepass_log.php" class="text-decoration-none text-dark">
      <div class="floating-card">
        <img src="assets/gatepass.png" alt="Gatepass">
        <div class="card-body">GatePass</div>
      </div>
    </a>

    <a href="mods_table.php" class="text-decoration-none text-dark">
      <div class="floating-card">
        <img src="assets/mods.png" alt="Mods">
        <div class="card-body">Mods</div>
      </div>
    </a>

    <a href="team.php" class="text-decoration-none text-dark">
      <div class="floating-card">
        <img src="assets/team.png" alt="Team">
        <div class="card-body">Team</div>
      </div>
    </a>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const slider = document.getElementById("slider");
  const btnLeft = document.getElementById("slideLeft");
  const btnRight = document.getElementById("slideRight");

  btnLeft.onclick = () => {
    slider.scrollBy({ left: -300, behavior: "smooth" });
  };
  btnRight.onclick = () => {
    slider.scrollBy({ left: 300, behavior: "smooth" });
  };
</script>

</body>
</html>
