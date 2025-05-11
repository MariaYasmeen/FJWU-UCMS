<?php
session_start();
require_once '../../includes/db.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /ucms/auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$stats = [
    'total' => 0,
    'pending' => 0,
    'in_progress' => 0,
    'resolved' => 0
];

try {
    // Get student's complaint statistics
    $stats_sql = "SELECT status, COUNT(*) as count 
                  FROM complaints 
                  WHERE student_id = ? 
                  GROUP BY status";
    
    if (!$stmt = $conn->prepare($stats_sql)) {
        throw new Exception("Error preparing stats query: " . $conn->error);
    }
    
    if (!$stmt->bind_param("i", $student_id)) {
        throw new Exception("Error binding stats parameters: " . $stmt->error);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Error executing stats query: " . $stmt->error);
    }
    
    $stats_result = $stmt->get_result();
    
    if ($stats_result) {
        while ($row = $stats_result->fetch_assoc()) {
            $stats[$row['status']] = $row['count'];
            $stats['total'] += $row['count'];
        }
    }
    
    // Get recent complaints
    $recent_sql = "SELECT c.*, d.name as department_name, u.name as faculty_name 
                   FROM complaints c 
                   LEFT JOIN departments d ON c.department_id = d.id 
                   LEFT JOIN users u ON c.assigned_to = u.id 
                   WHERE c.student_id = ? 
                   ORDER BY c.created_at DESC 
                   LIMIT 5";
    
    if (!$stmt = $conn->prepare($recent_sql)) {
        throw new Exception("Error preparing recent complaints query: " . $conn->error);
    }
    
    if (!$stmt->bind_param("i", $student_id)) {
        throw new Exception("Error binding recent complaints parameters: " . $stmt->error);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Error executing recent complaints query: " . $stmt->error);
    }
    
    $recent_result = $stmt->get_result();
    
    // Get recent notifications
    $notif_sql = "SELECT * FROM notifications 
                  WHERE user_id = ? 
                  ORDER BY created_at DESC 
                  LIMIT 5";
    
    if (!$stmt = $conn->prepare($notif_sql)) {
        throw new Exception("Error preparing notifications query: " . $conn->error);
    }
    
    if (!$stmt->bind_param("i", $student_id)) {
        throw new Exception("Error binding notifications parameters: " . $stmt->error);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Error executing notifications query: " . $stmt->error);
    }
    
    $notif_result = $stmt->get_result();
    
} catch (Exception $e) {
    // Log the error and display a user-friendly message
    error_log("Error in student dashboard: " . $e->getMessage());
    die("An error occurred while loading the dashboard. Please try again later.");
}

$base_path = '/ucms';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - UCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>/assets/css/style.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <?php include '../../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <h2 class="mb-4">Student Dashboard</h2>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Complaints</h5>
                            <h2><?php echo $stats['total']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Pending</h5>
                            <h2><?php echo $stats['pending']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">In Progress</h5>
                            <h2><?php echo $stats['in_progress']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Resolved</h5>
                            <h2><?php echo $stats['resolved']; ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Recent Complaints -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Recent Complaints</h5>
                            <a href="submit_complaint.php" class="btn btn-primary btn-sm">Submit New Complaint</a>
                        </div>
                        <div class="card-body">
                            <?php if ($recent_result && $recent_result->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Subject</th>
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
                                                        <?php if ($complaint['status'] !== 'resolved'): ?>
                                                            <a href="update_complaint.php?id=<?php echo $complaint['id']; ?>" 
                                                               class="btn btn-sm btn-info">
                                                                Update
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-center">No complaints submitted yet.</p>
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
                            <?php if ($notif_result && $notif_result->num_rows > 0): ?>
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