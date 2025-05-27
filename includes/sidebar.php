<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get user role
$role = $_SESSION['role'] ?? '';
$base_path = '/ucms';
?>

<div class="sidebar">
    <div class="sidebar-inner p-3">
        <h4 class="sidebar-title text-center mb-4">UCMS</h4>
        
        <?php if ($role === 'student'): ?>
            <!-- Student Sidebar -->
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_path; ?>/dashboard/student/index.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_path; ?>/dashboard/student/new_complaint.php">
                        <i class="fas fa-plus"></i> Submit Complaint
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_path; ?>/dashboard/student/complaints.php">
                        <i class="fas fa-list"></i> My Complaints
                    </a>
                </li>
             
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_path; ?>/dashboard/student/profile.php">
                        <i class="fas fa-user"></i> Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_path; ?>/dashboard/student/help.php">
                        <i class="fas fa-question-circle"></i> Help Center
                    </a>
                </li>
            </ul>

        <?php elseif ($role === 'faculty'): ?>
            <!-- Faculty Sidebar -->
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_path; ?>/dashboard/faculty/index.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_path; ?>/dashboard/faculty/assigned_complaints.php">
                        <i class="fas fa-tasks"></i> Assigned Complaints
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_path; ?>/dashboard/faculty/complaint_history.php">
                        <i class="fas fa-history"></i> Complaint History
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_path; ?>/dashboard/faculty/profile.php">
                        <i class="fas fa-user"></i> Profile
                    </a>
                </li>
            </ul>

        <?php elseif ($role === 'admin'): ?>
            <!-- Admin Sidebar -->
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_path; ?>/dashboard/admin/index.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_path; ?>/dashboard/admin/manage_complaints.php">
                        <i class="fas fa-tasks"></i> Manage Complaints
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_path; ?>/dashboard/admin/manage_users.php">
                        <i class="fas fa-users"></i> Manage Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_path; ?>/dashboard/admin/reports.php">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                </li>
               <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_path; ?>/dashboard/admin/profile.php">
                        <i class="fas fa-user"></i> Profile
                    </a>
                </li>
            </ul>
        <?php endif; ?>
    </div>
</div>

<style>
/* Soft UI Dark Green Sidebar */

.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 250px;
    background: #144d14; /* Dark green base */
    box-shadow:
        inset 6px 6px 10px #0a2c0a, /* inner dark shadow */
        inset -6px -6px 10px #1f6a1f; /* inner light shadow */
    border-radius: 0 15px 15px 0;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    transition: width 0.3s ease;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Inner padding */
.sidebar-inner {
    flex-grow: 1;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
}

/* Title */
.sidebar-title {
    color: #c6efc6;
    font-weight: 700;
    font-size: 1.9rem;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.6);
    user-select: none;
}

/* Navigation list */
.sidebar ul.nav {
    margin-top: 1rem;
    padding-left: 0;
    list-style: none;
    flex-grow: 1;
}

/* Nav items */
.sidebar .nav-item {
    margin-bottom: 0.75rem;
}

.sidebar .nav-link {
    padding: 1.2rem 1.5rem; /* taller buttons */
    height: 70px; /* fixed height */
    color: #1b3a2a; /* dark green text */
    background: #ffffff; /* white background */
    border-radius: 12px;

    /* Soft UI effect for white background */
    transition: all 0.3s ease;
    font-weight: 600;
    display: flex;
    align-items: center;
}

.sidebar .nav-link i {
    margin-right: 12px;
    width: 20px;
    text-align: center;
    color: #1b3a2a; /* icon color same as text */
}

.sidebar .nav-link:hover {
    background: #1b3a2a; /* dark green bg */
    color: #ffffff; /* white text */
     text-decoration: none;
}

.sidebar .nav-link.active {
    background: #145223;
    color: #e6f2e6;
    box-shadow:
        inset 3px 3px 6px #0f3a17,
        inset -3px -3px 6px #1c5f2a;
}

/* Keep the sidebar dark green background */
.sidebar {
    background-color: #143a22; /* darker green */
    box-shadow: inset 4px 4px 8px #0e2c19, inset -4px -4px 8px #1a4c2c;
}

 

/* Scrollbar styling */
.sidebar::-webkit-scrollbar {
    width: 8px;
}

.sidebar::-webkit-scrollbar-track {
    background: #144d14;
    border-radius: 10px;
}

.sidebar::-webkit-scrollbar-thumb {
    background: #3cd63c;
    border-radius: 10px;
    box-shadow:
        inset 2px 2px 3px rgba(255,255,255,0.3),
        inset -2px -2px 3px rgba(0,0,0,0.3);
}

/* Responsive - collapse sidebar on small screens */
@media (max-width: 768px) {
    .sidebar {
        width: 70px;
        border-radius: 0 10px 10px 0;
    }

    .sidebar-inner {
        padding: 1.5rem 0.5rem;
        align-items: center;
    }

    /* Hide nav text, show icons only */
    .sidebar .nav-link {
        justify-content: center;
        padding: 0.8rem 0;
        font-size: 0;
        box-shadow: none;
        background: transparent;
        color: #a6d2a6;
    }
    .sidebar .nav-link i {
        margin-right: 0;
        font-size: 1.4rem;
        filter: none;
    }
    /* Remove hover background on small */
    .sidebar .nav-link:hover,
    .sidebar .nav-link:focus {
        background: transparent;
        color: #d0f0d0;
        box-shadow: none;
    }
}

/* Adjust main content margin accordingly */
.main-content {
    margin-left: 250px;
    padding: 20px;
    transition: margin-left 0.3s ease;
}

@media (max-width: 768px) {
    .main-content {
        margin-left: 70px;
    }
}
</style>
