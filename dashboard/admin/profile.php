<?php
require_once '../../includes/header.php';
require_once '../../includes/db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
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
                <!-- Profile Image and Name -->
                <div class="text-center mb-4">
                    <img src="<?php echo $base_path; ?>/assets/images/default-avatar.png" alt="Profile Picture" 
                         class="img-fluid rounded-circle mb-2" style="max-width: 100px;">
                    <h6 class="mb-0"><?php echo htmlspecialchars($user['name']); ?></h6>
                    <small class="text-muted">Administrator</small>
                </div>

            
                <!-- Settings Section -->
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                    <span>Settings</span>
                </h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo $base_path; ?>/dashboard/admin/profile.php">
                            <i class="fas fa-user"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_path; ?>/dashboard/admin/account_settings.php">
                            <i class="fas fa-cog"></i> Account Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_path; ?>/dashboard/admin/change_password.php">
                            <i class="fas fa-key"></i> Change Password
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_path; ?>/dashboard/admin/upload_image.php">
                            <i class="fas fa-image"></i> Upload Image
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
             <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Administrator Profile</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-4 text-center">
                                    <img src="<?php echo $base_path; ?>/assets/images/default-avatar.png" alt="Profile Picture" class="img-fluid rounded-circle" style="max-width: 150px;">
                                </div>
                                <div class="col-md-8">
                                    <h5 class="card-title"><?php echo htmlspecialchars($user['name']); ?></h5>
                                    <p class="text-muted">Administrator</p>
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
                                            <th>Admin ID:</th>
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
                                <a href="<?php echo $base_path; ?>/dashboard/admin/account_settings.php" class="btn btn-primary">
                                    <i class="fas fa-cog"></i> Account Settings
                                </a>
                                <a href="<?php echo $base_path; ?>/dashboard/admin/change_password.php" class="btn btn-secondary">
                                    <i class="fas fa-key"></i> Change Password
                                </a>
                                <a href="<?php echo $base_path; ?>/dashboard/admin/upload_image.php" class="btn btn-info">
                                    <i class="fas fa-image"></i> Upload Image
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