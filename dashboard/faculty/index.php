<?php
session_start();
require_once '../../includes/db.php';

// Check if user is logged in and is a faculty
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header("Location: /ucms/auth/login.php");
    exit();
}

// Get faculty's assigned complaints statistics
$faculty_id = $_SESSION['user_id'];
$stats = [
    'total' => 0,
    'pending' => 0,
    'in_progress' => 0,
    'resolved' => 0
];

$stats_sql = "SELECT status, COUNT(*) as count 
              FROM complaints 
              WHERE assigned_to = $faculty_id 
              GROUP BY status";
$stats_result = $conn->query($stats_sql);

while ($row = $stats_result->fetch_assoc()) {
    $stats[$row['status']] = $row['count'];
    $stats['total'] += $row['count'];
}

// Get recent assigned complaints
$recent_sql = "SELECT c.*, d.name as department_name, u.name as student_name 
               FROM complaints c 
               LEFT JOIN departments d ON c.department_id = d.id 
               LEFT JOIN users u ON c.student_id = u.id 
               WHERE c.assigned_to = $faculty_id 
               ORDER BY c.created_at DESC 
               LIMIT 5";
$recent_result = $conn->query($recent_sql);

// // Get recent notifications
// $notif_sql = "SELECT * FROM notifications 
//               WHERE user_id = $faculty_id 
//               ORDER BY created_at DESC 
//               LIMIT 5";
// $notif_result = $conn->query($notif_sql);

$base_path = '/ucms';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard - UCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>/assets/css/style.css">
</head>

<style>
    body {
        background-color: #f5fdf7;
        font-family: "Segoe UI", sans-serif;
    }

    h2, h5, h6 {
        color: #ffffff;
    }

    .main-content {
        padding: 20px;
    }

    /* Card Styling */
    .card {
        background-color: #a8dfb5; /* Light green */
        color: #ffffff;
        border: 1px solid #7dcf93;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: #90d9a4;
        border-bottom: 1px solid #7dcf93;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .card-title {
        margin: 0;
        color: #ffffff;
        font-weight: 600;
    }

    /* Button Styling */
    .btn-primary,
    .btn-success,
    .btn-dark {
        background-color: #4caf50;
        border-color: #4caf50;
        color: #fff;
        transition: background 0.3s ease;
    }

    .btn-primary:hover,
    .btn-success:hover,
    .btn-dark:hover {
        background-color: #43a047;
        border-color: #43a047;
    }

    .btn-sm {
        padding: 4px 10px;
        font-size: 0.85rem;
    }

    /* Statistics Cards (optional override) */
    .bg-primary,
    .bg-warning,
    .bg-info,
    .bg-success {
        background-color: #a8dfb5 !important;
        color: #ffffff !important;
        border: 1px solid #7dcf93;
    }

    /* Table */
    .table th {
        background-color: #90d9a4;
        color: #ffffff;
        border: none;
    }

    .table td {
        color: #ffffff;
        vertical-align: middle;
        background-color: #a8dfb5;
        border-top: 1px solid #bce3c9;
    }

    /* Badge Colors */
    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #fff;
    }

    .badge.bg-info {
        background-color: #17a2b8 !important;
        color: #fff;
    }

    .badge.bg-success {
        background-color: #28a745 !important;
        color: #fff;
    }

    .badge.bg-secondary {
        background-color: #6c757d !important;
        color: #fff;
    }

    /* Notification List */
    .list-group-item {
        background-color: #c1ebcf;
        color: #ffffff;
        border: none;
        margin-bottom: 5px;
        border-radius: 6px;
        transition: background 0.2s ease;
    }

    .list-group-item:hover {
        background-color: #a8dfb5;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .main-content {
            padding: 10px;
        }

        .card-title {
            font-size: 1rem;
        }

        h2 {
            font-size: 1.5rem;
        }

        .btn-sm {
            font-size: 0.8rem;
            padding: 4px 8px;
        }
    }
</style>



<body>
    <?php include '../../includes/header.php'; ?>
    <?php include '../../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <h2 class="mb-4">Faculty Dashboard</h2>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card ">
                        <div class="card-body">
                            <h5 class="card-title">Total Assigned</h5>
                            <h2><?php echo $stats['total']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card ">
                        <div class="card-body">
                            <h5 class="card-title">Pending</h5>
                            <h2><?php echo $stats['pending']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card e">
                        <div class="card-body">
                            <h5 class="card-title">In Progress</h5>
                            <h2><?php echo $stats['in_progress']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card    ">
                        <div class="card-body">
                            <h5 class="card-title">Resolved</h5>
                            <h2><?php echo $stats['resolved']; ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Recent Assigned Complaints -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Recent Assigned Complaints</h5>
                            <a href="assigned_complaints.php" style="background-color:rgb(7, 80, 64); color: #fff; padding: 10px; border-radius: 5px; cursor: pointer;" class="btn  btn-sm">View All</a>
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
                                                    <td>
                                                        <span style="background-color:rgb(7, 80, 64); color: #fff; padding: 10px; border-radius: 5px; cursor: pointer;" class="badge bg-<?php 
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
                                                        style="background-color:rgb(7, 80, 64); color: #fff; padding: 10px; border-radius: 5px; cursor: pointer;"  
                                                           class="btn btn-sm btn-primary">
                                                            View
                                                        </a>
                                                        <?php if ($complaint['status'] !== 'resolved'): ?>
                                                            <a href="respond_complaint.php?id=<?php echo $complaint['id']; ?>" 
                                                            style="background-color:rgb(7, 80, 64); color: #fff; padding: 10px; border-radius: 5px; cursor: pointer;" 
                                                               class="btn  ">
                                                                Respond
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-center">No complaints assigned yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Recent Notifications -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Recent Notifications</h5>
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
                                <p class="text-center">No new notifications.</p>
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