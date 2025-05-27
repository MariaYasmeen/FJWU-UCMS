<?php
session_start();
require_once '../../includes/db.php';

// Check if user is logged in and is a faculty
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header("Location: /ucms/auth/login.php");
    exit();
}

$faculty_id = $_SESSION['user_id'];
$base_path = '/ucms';

// Check if complaint ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: assigned_complaints.php");
    exit();
}

$complaint_id = intval($_GET['id']);

// Get complaint details
$sql = "SELECT c.*, u.name as student_name, d.name as department_name 
        FROM complaints c 
        JOIN users u ON c.student_id = u.id 
        JOIN departments d ON c.department_id = d.id 
        WHERE c.id = ? AND c.assigned_to = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $complaint_id, $faculty_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: assigned_complaints.php");
    exit();
}

$complaint = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Complaint - UCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Complaint Details</h3>
        <a href="assigned_complaints.php" class="btn btn-secondary">Back to List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($complaint['subject']); ?></h5>
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <p><strong>Student:</strong> <?php echo htmlspecialchars($complaint['student_name']); ?></p>
                    <p><strong>Department:</strong> <?php echo htmlspecialchars($complaint['department_name']); ?></p>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-<?php 
                            echo $complaint['status'] === 'pending' ? 'warning' : 
                                ($complaint['status'] === 'in_progress' ? 'info' : 'success'); 
                        ?>">
                            <?php echo ucfirst($complaint['status']); ?>
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Submitted:</strong> <?php echo date('M d, Y', strtotime($complaint['created_at'])); ?></p>
                    <p><strong>Last Updated:</strong> <?php echo date('M d, Y', strtotime($complaint['updated_at'])); ?></p>
                </div>
            </div>

            <div class="mt-3">
                <h6>Description:</h6>
                <p class="border p-3 rounded"><?php echo nl2br(htmlspecialchars($complaint['description'])); ?></p>
            </div>

            <?php if ($complaint['status'] !== 'resolved'): ?>
                <div class="mt-3">
                    <form action="update_status.php" method="POST">
                        <input type="hidden" name="complaint_id" value="<?php echo $complaint_id; ?>">
                        <div class="mb-3">
                            <label class="form-label">Update Status</label>
                            <select name="status" class="form-select">
                                <option value="pending" <?php echo $complaint['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="in_progress" <?php echo $complaint['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="resolved" <?php echo $complaint['status'] === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Add Comment</label>
                            <textarea name="comment" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 