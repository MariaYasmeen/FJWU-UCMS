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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand" href="<?php echo $base_path; ?>/index.php">SpeakUp FJWU</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="<?php echo $base_path; ?>/index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">About</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="departmentsDropdown" role="button" data-bs-toggle="dropdown">
              Departments
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">IT Support</a></li>
              <li><a class="dropdown-item" href="#">Admin Office</a></li>
              <li><a class="dropdown-item" href="#">Finance</a></li>
              <li><a class="dropdown-item" href="#">Library</a></li>
              <li><a class="dropdown-item" href="#">Hostel</a></li>
              <li><a class="dropdown-item" href="#">Transport</a></li>
              <li><a class="dropdown-item" href="#">Examination Cell</a></li>
              <li><a class="dropdown-item" href="#">Academic Affairs</a></li>
            </ul>
          </li>
          <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $base_path; ?>/dashboard/<?php echo $_SESSION['role']; ?>/index.php">Dashboard</a>
            </li>
          <?php endif; ?>
        </ul>
        <ul class="navbar-nav">
          <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                <?php echo htmlspecialchars($_SESSION['name']); ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="<?php echo $base_path; ?>/profile.php">Profile</a></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?php echo $base_path; ?>/auth/logout.php">Logout</a></li>
              </ul>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $base_path; ?>/auth/login.php">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $base_path; ?>/auth/register.php">Register</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero">
    <h1>University Complaint Management System</h1>
    <p>Helping FJWU students register and track their complaints efficiently.</p>
  </section>

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
                <?php 
                  $dashboard_path = $base_path . '/dashboard/' . $_SESSION['role'] . '/index.php';
                  if (file_exists($_SERVER['DOCUMENT_ROOT'] . $dashboard_path)) {
                ?>
                  <a href="<?php echo $dashboard_path; ?>" class="btn btn-primary btn-lg px-4 me-md-2">
                    <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                  </a>
                <?php } else { ?>
                  <div class="alert alert-warning">
                    Dashboard not available for your role (<?php echo htmlspecialchars($_SESSION['role']); ?>)
                  </div>
                <?php } ?>
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
