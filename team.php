<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Team Umbrella Structure</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: radial-gradient(circle at top, #2d004d, #110021);
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
    }

    h1, h3, h4, h5, h6 {
      font-weight: 700;
    }

    .section-title {
      color: #c08bfa;
      border-bottom: 2px solid #c08bfa;
      display: inline-block;
      margin-bottom: 20px;
    }

    .card {
      background-color: #1c002f;
      border: 1px solid #6f42c1;
      border-radius: 20px;
      color: white;
      text-align: center;
      box-shadow: 0 0 20px rgba(192, 139, 250, 0.1);
      transition: 0.3s;
    }

    .card:hover {
      box-shadow: 0 0 25px rgba(192, 139, 250, 0.4);
      transform: scale(1.02);
    }

    .member-img {
      width: 180px; /* Set the width of the image */
      height: 180px; /* Set the height of the image */
      object-fit: cover; /* Ensures the image covers the square */
      border: 4px solid #c08bfa; /* Optional: you can keep the border or adjust it */
      margin-bottom: 20px;
    }

    .emoji {
      font-size: 1.3rem;
      margin-right: 6px;
    }

    .line-center {
      position: relative;
      text-align: center;
    }

    .line-center::after {
      content: "";
      position: absolute;
      top: 100%;
      left: 50%;
      height: 40px;
      width: 2px;
      background-color: #c08bfa;
    }

    .connector::before {
      content: "";
      position: absolute;
      top: 0;
      left: 50%;
      width: 0;
      height: 0;
      border-left: 2px solid #c08bfa;
      height: 40px;
    }

    .group-line::after {
      content: "";
      position: absolute;
      top: 40px;
      left: 50%;
      width: 60%;
      transform: translateX(-50%);
      height: 2px;
      background-color: #c08bfa;
    }

    .flex-hierarchy {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 40px;
    }

    .text-light-purple {
      color: #d6b3ff;
    }

    .container {
      max-width: 1200px;
    }

    .tooltip-icon {
      cursor: pointer;
      font-size: 1.1rem;
      color: #c08bfa;
      margin-left: 5px;
    }
  </style>
</head>
<body>

<div class="container py-5">

  <div class="text-center mb-5">
    <h1 class="text-light-purple"> Team Organizational Structure</h1>
  </div>

  <!-- Team Representatives -->
  <div class="line-center">
    <div class="card p-4 mx-auto mb-4" style="max-width: 500px;">
      <h3 class="mb-3">
        <span class="emoji">🧭</span> Team Representatives
        <span class="tooltip-icon" data-bs-toggle="tooltip"
          title="Leads the team, manages both divisions. Actively supports paperwork team and provides financial/design guidance to TechCore.">❕</span>
      </h3>
      
      <div class="row justify-content-center">
        <div class="col-6 col-md-5">
          <img src="team/pearl-Photoroom.jpg" alt="Sherie Pearl De Paz" class="member-img">
          <h6>Sherie Pearl De Paz</h6>
        </div>
        <div class="col-6 col-md-5">
          <img src="team/FORMAL_PITOGO.png" alt="Ronalyn Pitogo" class="member-img">
          <h6>Ronalyn Pitogo</h6>
        </div>
      </div>
    </div>
  </div>

  <!-- Branch Line -->
  <div class="connector group-line mb-4"></div>

  <!-- Subdivisions -->
  <div class="flex-hierarchy">

    <!-- Central Operations Division -->
    <div class="card p-4" style="min-width: 280px; max-width: 400px;">
      <h4 class="section-title">
        <span class="emoji">🔷</span> PaperTrail Division
        <span class="tooltip-icon" data-bs-toggle="tooltip" title="Handles paperwork, documentation, formal communication, and reporting.">❕</span>
      </h4>
      <p><span class="emoji">📄</span> Admin & Documentation Team</p>
      <div class="row">
        <div class="col-6 col-md-6 mb-3">
          <img src="team/ikang-Photoroom.jpg" class="member-img" alt="Erika olayvar">
          <h6>Erika olayvar</h6>
        </div>
        <div class="col-6 col-md-6 mb-3">
          <img src="team/FORMAL_BEDON.png" class="member-img" alt="Josephine Rodriguez">
          <h6>Josephine Rodriguez</h6>
        </div>
        <div class="col-12 col-md-12 mb-3">
          <img src="team/FORMAL_PINIT.png" class="member-img" alt="Gherelyn Sampinit">
          <h6>Gherelyn Sampinit</h6>
        </div>
      </div>
    </div>

    <!-- TechCore Division -->
    <div class="card p-4" style="min-width: 280px; max-width: 400px;">
      <h4 class="section-title">
        <span class="emoji">🔶</span> TechCore Division
        <span class="tooltip-icon" data-bs-toggle="tooltip" title="Builds and codes the project – both the software and the physical design.">❕</span>
      </h4>
      <p><span class="emoji">⚙️</span> Software, Wiring, and Design</p>

      <!-- Coders -->
      <h6 class="mb-3">
        💻 Coders
        <span class="tooltip-icon" data-bs-toggle="tooltip" title="Develops software, programs logic, and integrates code with hardware.">❕</span>
      </h6>
      <div class="row">
        <div class="col-6 col-md-6 mb-3">
          <img src="team/ikot-Photoroom.jpg" class="member-img" alt="Jericho Timkang ">
          <h6>Jericho Timkang </h6>
        </div>
        <div class="col-6 col-md-6 mb-3">
          <img src="team/FORMAL_MERINO.png" class="member-img" alt="Manny Merino">
          <h6>Manny Merino</h6>
        </div>
      </div>

      <!-- BuildForge Unit -->
      <h6 class="mb-3 mt-4">
        🛠️ BuildForge Unit
        <span class="tooltip-icon" data-bs-toggle="tooltip" title="Designs the physical project layout and manages all wiring & technical construction.">❕</span>
      </h6>
      <div class="row">
        <div class="col-6 col-md-6 mb-3">
          <img src="team/costorio.jpg" class="member-img" alt="Cristian Keneth B. Costorio">
          <h6>Cristian Keneth B. Costorio</h6>
        </div>
        <div class="col-6 col-md-6 mb-3">
          <img src="team/FORMAL_BERNALES.png" class="member-img" alt="Adrian Bernales">
          <h6>Adrian Bernales</h6>
        </div>
      </div>
    </div>

  </div>

</div>

<!-- Bootstrap JS and Tooltip Activation -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
</script>
</body>
</html>
