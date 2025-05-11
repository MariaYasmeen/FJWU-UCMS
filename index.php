<?php
session_start();
$base_path = '/ucms';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>University Complaint Management System</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="logo">SpeakUp FJWU</div>
    <div class="nav-links">
      <a href="index.php">Home</a>
      <a href="#">About</a>

      <div class="dropdown">
        <a href="#" onclick="toggleDepartments()">Departments</a>
        <div class="dropdown-menu" id="deptMenu">
          <a href="#">IT Support</a>
          <a href="#">Admin Office</a>
          <a href="#">Finance</a>
          <a href="#">Library</a>
          <a href="#">Hostel</a>
          <a href="#">Transport</a>
          <a href="#">Examination Cell</a>
          <a href="#">Academic Affairs</a>
        </div>
      </div>
    </div>

    <!-- User Dropdown -->
    <div class="user-dropdown">
      <img src="assets/images/user-icon.jpg" alt="User Icon" onclick="toggleUserMenu()" />
      <div class="dropdown-menu" id="userMenu">
        <?php if (isset($_SESSION['user'])): ?>
          <a href="#">My Profile</a>
          <a href="#">Settings</a>
          <a href="logout.php">Logout</a>
        <?php else: ?>
          <a href="auth/login.php">Login</a>
          <a href="auth/register.php">Sign Up</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero">
    <h1>University Complaint Management System</h1>
    <p>Helping FJWU students register and track their complaints efficiently.</p>
  </section>

  <!-- JavaScript for dropdown toggles -->
  <script>
    function toggleUserMenu() {
      const menu = document.getElementById("userMenu");
      menu.style.display = menu.style.display === "flex" ? "none" : "flex";
    }

    function toggleDepartments() {
      const menu = document.getElementById("deptMenu");
      menu.style.display = menu.style.display === "flex" ? "none" : "flex";
    }

    window.onclick = function (e) {
      if (!e.target.matches('.user-dropdown img')) {
        document.getElementById("userMenu").style.display = "none";
      }
      if (!e.target.matches('.dropdown > a')) {
        document.getElementById("deptMenu").style.display = "none";
      }
    }
  </script>

  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8 text-center">
        <h1 class="display-4 mb-4">Welcome to UCMS</h1>
        <p class="lead mb-4">University Complaint Management System</p>
        
        <?php if (!isset($_SESSION['user_id'])): ?>
          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="card-title mb-4">Get Started</h5>
              <p class="card-text mb-4">Submit and track your complaints easily with our system.</p>
              <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                <a href="<?php echo $base_path; ?>/auth/login.php" class="btn btn-primary btn-lg px-4 me-md-2">Login</a>
                <a href="<?php echo $base_path; ?>/auth/register.php" class="btn btn-outline-primary btn-lg px-4">Register</a>
              </div>
            </div>
          </div>
        <?php else: ?>
          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="card-title mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h5>
              <p class="card-text mb-4">You are logged in as <?php echo ucfirst($_SESSION['role']); ?>.</p>
              <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                <a href="<?php echo $base_path; ?>/dashboard/<?php echo $_SESSION['role']; ?>/index.php" class="btn btn-primary btn-lg px-4 me-md-2">
                  <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                </a>
                <?php if ($_SESSION['role'] === 'student'): ?>
                  <a href="<?php echo $base_path; ?>/dashboard/student/new_complaint.php" class="btn btn-success btn-lg px-4">
                    <i class="fas fa-plus"></i> Submit New Complaint
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
