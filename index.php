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
<style>
  :root { 
    --secondary-color: #40916c;
    --accent-color: #52b788;
    --light-color:rgb(57, 68, 60);
    --text-color: #1a1a1a;
  }

  body {
    background-color: var(--light-color);
    color: var(--text-color);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  .navbar {
    background-color: var(--primary-color) !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  }

  .navbar-brand, .nav-link, .dropdown-item {
    color: #ffffff !important;
    font-weight: 500;
  }

  .nav-link:hover, .dropdown-item:hover {
    color: var(--accent-color) !important;
  }

  .hero {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 60px 20px;
    text-align: center;
    border-bottom-left-radius: 30px;
    border-bottom-right-radius: 30px;
  }

  .hero h1 {
    font-size: 2.8rem;
    margin-bottom: 15px;
  }

  .hero p {
    font-size: 1.2rem;
  }

  .card {
    background-color: white;
    border: none;
    border-radius: 20px;
    padding: 20px;
    transition: box-shadow 0.3s ease;
  }

  .card:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
  }

  .btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
  }

  .btn-primary:hover {
    background-color: var(--secondary-color);
    border-color: var(--secondary-color);
  }

  .btn-outline-primary {
    color: var(--primary-color);
    border-color: var(--primary-color);
  }

  .btn-outline-primary:hover {
    background-color: var(--primary-color);
    color: white;
  }

  .btn-success {
    background-color: var(--accent-color);
    border-color: var(--accent-color);
  }

  .btn-success:hover {
    background-color: #40916c;
    border-color: #40916c;
  }

  .alert-warning {
    border-radius: 10px;
  }

    :root {
            --primary-color: #1B4D3E;
            --secondary-color: #2E7D32;
            --accent-color: #4CAF50;
            --light-bg: #F5F5F5;
            --dark-bg: #1B4D3E;
            --text-light: #FFFFFF;
            --text-dark: #333333;
            --card-shadow: 0 0.15rem 1.75rem 0 rgba(27, 77, 62, 0.1);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
        }

        /* Navbar Styles */
        .navbar {
            background-color: var(--primary-color);
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            color: var(--text-light) !important;
            font-weight: 600;
        }

        .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }

        .nav-link {
            color: var(--text-light) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--accent-color) !important;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(27, 77, 62, 0.9), rgba(27, 77, 62, 0.9)),
                        url('assets/images/fjwu.jpg') center/cover;
            color: var(--text-light);
            padding: 100px 0;
            text-align: center;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        /* Features Section */
        .features-section {
            padding: 80px 0;
            background-color: var(--light-bg);
        }

        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--dark-bg);
        }

        .feature-text {
            color: var(--text-dark);
            opacity: 0.8;
        }

        /* CTA Section */
        .cta-section {
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 80px 0;
            text-align: center;
        }

        .cta-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .cta-text {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .btn-cta {
            background-color: var(--accent-color);
            color: var(--text-light);
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-cta:hover {
            background-color: #43A047;
            transform: translateY(-2px);
            color: var(--text-light);
        }

        /* Footer */
        .footer {
            background-color: var(--dark-bg);
            color: var(--text-light);
            padding: 40px 0;
        }

        .footer-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .footer-text {
            opacity: 0.8;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
       
            text-decoration: none; 
            transition: all 0.3s ease;
        }
 

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.2rem;
            }
        }
        
  @media (max-width: 768px) {
    .hero h1 {
      font-size: 2rem;
    }

    .hero p {
      font-size: 1rem;
    }

    .btn-lg {
      font-size: 1rem;
      padding: 10px 20px;
    }

    .card {
      padding: 15px;
    }
  }
</style>

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

      <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">Welcome to FJWU Complaint Management System</h1>
        
        </div>
    </section>
  <!-- Hero Section -->
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8 text-center">
        <h1 class="display-4 mb-4">Your voice matters. Submit, track, and resolve complaints efficiently.</h1>
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

   <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-comment-alt"></i>
                        </div>
                        <h3 class="feature-title">Easy Complaint Submission</h3>
                        <p class="feature-text">Submit your complaints easily through our user-friendly interface. Choose from various categories and provide detailed information.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h3 class="feature-title">Track Progress</h3>
                        <p class="feature-text">Monitor the status of your complaints in real-time. Get updates on the progress and resolution of your issues.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="feature-title">Secure Platform</h3>
                        <p class="feature-text">Your data is protected with advanced security measures. We ensure confidentiality and privacy of all submissions.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" id="about">
        <div class="container">
            <h2 class="cta-title">Ready to Make a Difference?</h2>
            <p class="cta-text">Join our community and help us improve the university experience for everyone.</p>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="auth/register.php" class="btn btn-cta">Register Now</a>
            <?php else: ?>
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
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="footer-title">About FJWU</h3>
                    <p class="footer-text">Fatima Jinnah Women University is committed to providing quality education and maintaining a conducive learning environment for all students.</p>
                </div>
                <div class="col-md-3">
                    <h3 class="footer-title">Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#features">Features</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h3 class="footer-title">Contact Us</h3>
                    <ul class="footer-links">
                        <li><i class="fas fa-phone me-2"></i> +92 51 1234567</li>
                        <li><i class="fas fa-envelope me-2"></i> info@fjwu.edu.pk</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i> Rawalpindi, Pakistan</li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>