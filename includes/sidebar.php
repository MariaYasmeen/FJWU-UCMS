<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get user role
$role = $_SESSION['role'] ?? '';
$base_path = '/ucms';
?>

<div class="sidebar bg-dark text-white" style="min-height: 100vh; width: 250px; position: fixed; left: 0; top: 0;">
    <div class="p-3">
        <h4 class="text-center mb-4">UCMS</h4>
        
        <?php if ($role === 'student'): ?>
            <!-- Student Sidebar -->
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base_path; ?>/dashboard/student/index.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base_path; ?>/dashboard/student/new_complaint.php">
                        <i class="fas fa-plus"></i> Submit Complaint
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base_path; ?>/dashboard/student/complaints.php">
                        <i class="fas fa-list"></i> My Complaints
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base_path; ?>/dashboard/student/messages.php">
                        <i class="fas fa-envelope"></i> Messages
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base_path; ?>/dashboard/student/notifications.php">
                        <i class="fas fa-bell"></i> Notifications
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base_path; ?>/dashboard/student/profile.php">
                        <i class="fas fa-user"></i> Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base_path; ?>/dashboard/student/help.php">
                        <i class="fas fa-question-circle"></i> Help Center
                    </a>
                </li>
            </ul>

        <?php elseif ($role === 'faculty'): ?>
            <!-- Faculty Sidebar -->
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base_path; ?>/dashboard/faculty/index.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base_path; ?>/dashboard/faculty/assigned_complaints.php">
                        <i class="fas fa-tasks"></i> Assigned Complaints
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base_path; ?>/dashboard/faculty/complaint_history.php">
                        <i class="fas fa-history"></i> Complaint History
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base_path; ?>/dashboard/faculty/notifications.php">
                        <i class="fas fa-bell"></i> Notifications
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base_path; ?>/dashboard/faculty/profile.php">
                        <i class="fas fa-user"></i> Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base_path; ?>/dashboard/faculty/help.php">
                        <i class="fas fa-question-circle"></i> Help Center
                    </a>
                </li>
            </ul>

        <?php elseif ($role === 'admin'): ?>
            <!-- Admin Sidebar -->
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base_path; ?>/dashboard/admin/index.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base_path; ?>/dashboard/admin/manage_complaints.php">
                        <i class="fas fa-tasks"></i> Manage Complaints
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base_path; ?>/dashboard/admin/assign_complaints.php">
                        <i class="fas fa-user-plus"></i> Assign Complaints
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base_path; ?>/dashboard/admin/manage_users.php">
                        <i class="fas fa-users"></i> Manage Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base_path; ?>/dashboard/admin/categories.php">
                        <i class="fas fa-tags"></i> Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base_path; ?>/dashboard/admin/reports.php">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base_path; ?>/dashboard/admin/settings.php">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base_path; ?>/dashboard/admin/announcements.php">
                        <i class="fas fa-bullhorn"></i> Announcements
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base_path; ?>/dashboard/admin/profile.php">
                        <i class="fas fa-user"></i> Profile
                    </a>
                </li>
            </ul>
        <?php endif; ?>
    </div>
</div>

<style>
.sidebar {
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
}

.sidebar .nav-link {
    padding: 0.8rem 1rem;
    color: rgba(255,255,255,0.8);
    transition: all 0.3s;
}

.sidebar .nav-link:hover {
    color: #fff;
    background: rgba(255,255,255,0.1);
}

.sidebar .nav-link i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

/* Main content adjustment */
.main-content {
    margin-left: 250px;
    padding: 20px;
}
</style> 