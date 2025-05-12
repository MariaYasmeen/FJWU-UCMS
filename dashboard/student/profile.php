<?php
require_once '../../includes/header.php';
require_once '../../includes/db_connect.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: {$base_path}/auth/login.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_path; ?>/dashboard/student/">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_path; ?>/dashboard/student/new_complaint.php">
                            <i class="fas fa-plus-circle"></i> New Complaint
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_path; ?>/dashboard/student/my_complaints.php">
                            <i class="fas fa-list"></i> My Complaints
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo $base_path; ?>/dashboard/student/profile.php">
                            <i class="fas fa-user"></i> Profile
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">My Profile</h1>
            </div>

            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Student Profile</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-4 text-center">
                                    <img src="<?php echo $base_path; ?>/assets/images/default-avatar.png" alt="Profile Picture" class="img-fluid rounded-circle" style="max-width: 150px;">
                                </div>
                                <div class="col-md-8">
                                    <h5 class="card-title"><?php echo htmlspecialchars($user['name']); ?></h5>
                                    <p class="text-muted">Student</p>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Personal Information</h6>
                                    <table class="table">
                                        <tr>
                                            <th>Email:</th>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Registration Date:</th>
                                            <td><?php echo date('F j, Y', strtotime($user['created_at'])); ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6>Account Information</h6>
                                    <table class="table">
                                        <tr>
                                            <th>Student ID:</th>
                                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td><span class="badge bg-success">Active</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-4">
                                <a href="<?php echo $base_path; ?>/edit_profile.php" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Profile
                                </a>
                                <a href="<?php echo $base_path; ?>/change_password.php" class="btn btn-secondary">
                                    <i class="fas fa-key"></i> Change Password
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?> 