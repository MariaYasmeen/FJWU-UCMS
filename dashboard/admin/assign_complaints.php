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

$base_path = '/ucms';

// Handle complaint assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_complaint'])) {
    $complaint_id = $_POST['complaint_id'];
    $faculty_id = $_POST['faculty_id'];
    
    $update_sql = "UPDATE complaints SET assigned_to = ?, status = 'in_progress', updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ii", $faculty_id, $complaint_id);
    
    if ($stmt->execute()) {
        // Create notification for faculty
        $notif_sql = "INSERT INTO notifications (user_id, type, message, related_id) VALUES (?, 'assignment', 'You have been assigned a new complaint', ?)";
        $notif_stmt = $conn->prepare($notif_sql);
        $notif_stmt->bind_param("ii", $faculty_id, $complaint_id);
        $notif_stmt->execute();
        
        $_SESSION['success'] = "Complaint assigned successfully!";
    } else {
        $_SESSION['error'] = "Error assigning complaint: " . $conn->error;
    }
    
    header("Location: assign_complaints.php");
    exit();
}

// Get unassigned complaints
$complaints_sql = "SELECT c.*, d.name as department_name, u.name as student_name 
                  FROM complaints c 
                  LEFT JOIN departments d ON c.department_id = d.id 
                  LEFT JOIN users u ON c.student_id = u.id 
                  WHERE c.assigned_to IS NULL AND c.status = 'pending'
                  ORDER BY c.created_at DESC";
$complaints_result = $conn->query($complaints_sql);

// Get available faculty members
$faculty_sql = "SELECT id, name, department_id FROM users WHERE role = 'faculty' ORDER BY name";
$faculty_result = $conn->query($faculty_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Complaints - UCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>/assets/css/style.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <?php include '../../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <h2 class="mb-4">Assign Complaints</h2>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Student</th>
                                    <th>Department</th>
                                    <th>Subject</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($complaints_result && $complaints_result->num_rows > 0): ?>
                                    <?php while ($complaint = $complaints_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $complaint['id']; ?></td>
                                            <td><?php echo htmlspecialchars($complaint['student_name']); ?></td>
                                            <td><?php echo htmlspecialchars($complaint['department_name']); ?></td>
                                            <td><?php echo htmlspecialchars($complaint['subject']); ?></td>
                                            <td><?php echo date('M d, Y H:i', strtotime($complaint['created_at'])); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignModal<?php echo $complaint['id']; ?>">
                                                    Assign
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Assign Modal -->
                                        <div class="modal fade" id="assignModal<?php echo $complaint['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Assign Complaint #<?php echo $complaint['id']; ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="" method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="complaint_id" value="<?php echo $complaint['id']; ?>">
                                                            <div class="mb-3">
                                                                <label class="form-label">Select Faculty Member</label>
                                                                <select name="faculty_id" class="form-select" required>
                                                                    <option value="">Choose faculty member...</option>
                                                                    <?php 
                                                                    $faculty_result->data_seek(0);
                                                                    while ($faculty = $faculty_result->fetch_assoc()): 
                                                                    ?>
                                                                        <option value="<?php echo $faculty['id']; ?>">
                                                                            <?php echo htmlspecialchars($faculty['name']); ?>
                                                                        </option>
                                                                    <?php endwhile; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" name="assign_complaint" class="btn btn-primary">Assign</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No unassigned complaints found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo $base_path; ?>/assets/js/main.js"></script>
</body>
</html> 