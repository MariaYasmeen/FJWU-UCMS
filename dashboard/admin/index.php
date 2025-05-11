<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../includes/db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /ucms/auth/login.php");
    exit();
}

// Get system-wide statistics
$stats = [
    'total_complaints' => 0,
    'pending_complaints' => 0,
    'in_progress_complaints' => 0,
    'resolved_complaints' => 0,
    'total_students' => 0,
    'total_faculty' => 0,
    'total_departments' => 0,
    'total_categories' => 0
];

// Get complaint statistics
$complaint_stats_sql = "SELECT status, COUNT(*) as count FROM complaints GROUP BY status";
$complaint_stats_result = $conn->query($complaint_stats_sql);

while ($row = $complaint_stats_result->fetch_assoc()) {
    $stats[$row['status'] . '_complaints'] = $row['count'];
    $stats['total_complaints'] += $row['count'];
}

// Get user statistics
$user_stats_sql = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
$user_stats_result = $conn->query($user_stats_sql);

while ($row = $user_stats_result->fetch_assoc()) {
    if ($row['role'] === 'student') {
        $stats['total_students'] = $row['count'];
    } elseif ($row['role'] === 'faculty') {
        $stats['total_faculty'] = $row['count'];
    }
}

// Get department and category counts
$dept_sql = "SELECT COUNT(*) as count FROM departments";
$cat_sql = "SELECT COUNT(*) as count FROM complaint_categories";

$stats['total_departments'] = $conn->query($dept_sql)->fetch_assoc()['count'];
$stats['total_categories'] = $conn->query($cat_sql)->fetch_assoc()['count'];

// Get recent complaints
$recent_sql = "SELECT c.*, d.name as department_name, 
                      u1.name as student_name, u2.name as faculty_name
               FROM complaints c 
               LEFT JOIN departments d ON c.department_id = d.id 
               LEFT JOIN users u1 ON c.student_id = u1.id 
               LEFT JOIN users u2 ON c.assigned_to = u2.id 
               ORDER BY c.created_at DESC 
               LIMIT 5";
$recent_result = $conn->query($recent_sql);

// Get recent notifications
$notif_sql = "SELECT * FROM notifications 
              WHERE type = 'system' 
              ORDER BY created_at DESC 
              LIMIT 5";
$notif_result = $conn->query($notif_sql);

$base_path = '/ucms';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - UCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>/assets/css/style.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <?php include '../../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <h2 class="mb-4">Admin Dashboard</h2>

            <!-- Statistics Cards as Buttons -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <button type="button" class="btn btn-secondary w-100 text-start stat-btn mb-2" data-detail="users">
                        <div class="card-body p-2">
                            <h5 class="card-title mb-1">Registered Users</h5>
                            <h2 class="mb-0"><?php
                                $user_count_sql = "SELECT COUNT(*) as total FROM users";
                                $user_count_result = $conn->query($user_count_sql);
                                $user_count = $user_count_result ? $user_count_result->fetch_assoc()['total'] : 0;
                                echo $user_count;
                            ?></h2>
                        </div>
                    </button>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary w-100 text-start stat-btn mb-2" data-detail="students">
                        <div class="card-body p-2">
                            <h5 class="card-title mb-1">Total Students</h5>
                            <h2 class="mb-0"><?php echo $stats['total_students']; ?></h2>
                        </div>
                    </button>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-info w-100 text-start stat-btn mb-2" data-detail="faculty">
                        <div class="card-body p-2">
                            <h5 class="card-title mb-1">Total Faculty</h5>
                            <h2 class="mb-0"><?php echo $stats['total_faculty']; ?></h2>
                        </div>
                    </button>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-dark w-100 text-start stat-btn mb-2" data-detail="departments">
                        <div class="card-body p-2">
                            <h5 class="card-title mb-1">Departments</h5>
                            <h2 class="mb-0"><?php echo $stats['total_departments']; ?></h2>
                        </div>
                    </button>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-warning w-100 text-start stat-btn mb-2" data-detail="categories">
                        <div class="card-body p-2">
                            <h5 class="card-title mb-1">Categories</h5>
                            <h2 class="mb-0"><?php echo $stats['total_categories']; ?></h2>
                        </div>
                    </button>
                </div>
                <div class="col-md-2">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Resolved</h5>
                            <h2><?php echo $stats['resolved_complaints']; ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details Container (appears below buttons) -->
            <div id="details-container" class="mb-4" style="display:none;"></div>

            <!-- Hidden Details (for JS to inject) -->
            <div id="students-detail" class="d-none">
                <h4 class="mb-3">All Students</h4>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Registered At</th></tr></thead>
                                <tbody>
                                <?php
                                $students = $conn->query("SELECT id, name, email, created_at FROM users WHERE role='student' ORDER BY created_at DESC");
                                if ($students && $students->num_rows > 0) {
                                    while ($s = $students->fetch_assoc()) {
                                        echo '<tr><td>'.$s['id'].'</td><td>'.htmlspecialchars($s['name']).'</td><td>'.htmlspecialchars($s['email']).'</td><td>'.$s['created_at'].'</td></tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan=4 class=\'text-center\'>No students found.</td></tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div id="faculty-detail" class="d-none">
                <h4 class="mb-3">All Faculty</h4>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Registered At</th></tr></thead>
                                <tbody>
                                <?php
                                $faculty = $conn->query("SELECT id, name, email, created_at FROM users WHERE role='faculty' ORDER BY created_at DESC");
                                if ($faculty && $faculty->num_rows > 0) {
                                    while ($f = $faculty->fetch_assoc()) {
                                        echo '<tr><td>'.$f['id'].'</td><td>'.htmlspecialchars($f['name']).'</td><td>'.htmlspecialchars($f['email']).'</td><td>'.$f['created_at'].'</td></tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan=4 class=\'text-center\'>No faculty found.</td></tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div id="departments-detail" class="d-none">
                <h4 class="mb-3">Departments</h4>
                <div class="card">
                    <div class="card-body">
                        <ul class="list-group">
                        <?php
                        $departments = $conn->query("SELECT name FROM departments ORDER BY name");
                        if ($departments && $departments->num_rows > 0) {
                            while ($d = $departments->fetch_assoc()) {
                                echo '<li class="list-group-item">'.htmlspecialchars($d['name']).'</li>';
                            }
                        } else {
                            echo '<li class="list-group-item text-center">No departments found.</li>';
                        }
                        ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div id="categories-detail" class="d-none">
                <h4 class="mb-3">Categories</h4>
                <div class="card">
                    <div class="card-body">
                        <ul class="list-group">
                        <?php
                        $categories = $conn->query("SELECT name FROM complaint_categories ORDER BY name");
                        if ($categories && $categories->num_rows > 0) {
                            while ($c = $categories->fetch_assoc()) {
                                echo '<li class="list-group-item">'.htmlspecialchars($c['name']).'</li>';
                            }
                        } else {
                            echo '<li class="list-group-item text-center">No categories found.</li>';
                        }
                        ?>
                        </ul>
                    </div>
                </div>
            </div>

            <style>
            .stat-btn {
                border-radius: 0.5rem;
                box-shadow: 0 0.1rem 0.3rem rgba(0,0,0,0.08);
                transition: box-shadow 0.2s, background 0.2s;
                padding: 0;
            }
            .stat-btn:focus, .stat-btn:active {
                outline: 2px solid #333;
                box-shadow: 0 0 0.5rem #333;
            }
            .stat-btn .card-body { pointer-events: none; }
            </style>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const buttons = document.querySelectorAll('.stat-btn');
                const detailsContainer = document.getElementById('details-container');
                const detailIds = ['students', 'faculty', 'departments', 'categories'];
                buttons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const detail = btn.getAttribute('data-detail');
                        if (detailIds.includes(detail)) {
                            // Hide all
                            detailIds.forEach(id => {
                                document.getElementById(id+'-detail').classList.add('d-none');
                            });
                            // Show selected
                            const selected = document.getElementById(detail+'-detail');
                            if (selected) {
                                detailsContainer.innerHTML = selected.innerHTML;
                                detailsContainer.style.display = 'block';
                                detailsContainer.scrollIntoView({behavior: 'smooth'});
                            }
                        } else {
                            detailsContainer.style.display = 'none';
                        }
                    });
                });
            });
            </script>

            <!-- Registered Users Table -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Registered Users</h5>
                </div>
                <div class="card-body">
                    <?php
                    $users_sql = "SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC";
                    $users_result = $conn->query($users_sql);
                    ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Registered At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($users_result && $users_result->num_rows > 0): ?>
                                    <?php while ($user = $users_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $user['id']; ?></td>
                                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo ucfirst($user['role']); ?></td>
                                            <td><?php echo $user['created_at']; ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center">No users found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Recent Complaints -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Recent Complaints</h5>
                            <a href="manage_complaints.php" class="btn btn-primary btn-sm">Manage All</a>
                        </div>
                        <div class="card-body">
                            <?php if ($recent_result->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Subject</th>
                                                <th>Student</th>
                                                <th>Department</th>
                                                <th>Assigned To</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($complaint = $recent_result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($complaint['subject']); ?></td>
                                                    <td><?php echo htmlspecialchars($complaint['student_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($complaint['department_name']); ?></td>
                                                    <td><?php echo $complaint['faculty_name'] ? htmlspecialchars($complaint['faculty_name']) : 'Not Assigned'; ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php 
                                                            echo $complaint['status'] === 'pending' ? 'warning' : 
                                                                ($complaint['status'] === 'in_progress' ? 'info' : 
                                                                ($complaint['status'] === 'resolved' ? 'success' : 'secondary')); 
                                                        ?>">
                                                            <?php echo ucfirst($complaint['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('M d, Y', strtotime($complaint['created_at'])); ?></td>
                                                    <td>
                                                        <a href="view_complaint.php?id=<?php echo $complaint['id']; ?>" 
                                                           class="btn btn-sm btn-primary">
                                                            View
                                                        </a>
                                                        <a href="assign_complaint.php?id=<?php echo $complaint['id']; ?>" 
                                                           class="btn btn-sm btn-success">
                                                            Assign
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-center">No complaints in the system.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- System Notifications -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">System Notifications</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($notif_result->num_rows > 0): ?>
                                <div class="list-group">
                                    <?php while ($notif = $notif_result->fetch_assoc()): ?>
                                        <a href="#" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($notif['title']); ?></h6>
                                                <small><?php echo date('M d, Y', strtotime($notif['created_at'])); ?></small>
                                            </div>
                                            <p class="mb-1"><?php echo htmlspecialchars($notif['message']); ?></p>
                                        </a>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-center">No system notifications.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 