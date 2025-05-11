<?php
require_once '../../includes/header.php';
require_once '../../includes/db_connect.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: {$base_path}/auth/login.php");
    exit();
}

// Fetch user's complaints
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT c.*, d.name as department_name, s.name as status_name 
    FROM complaints c 
    LEFT JOIN departments d ON c.department_id = d.id 
    LEFT JOIN complaint_status s ON c.status_id = s.id 
    WHERE c.user_id = ? 
    ORDER BY c.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Complaints</h5>
                    <a href="<?php echo $base_path; ?>/dashboard/student/new_complaint.php" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> New Complaint
                    </a>
                </div>
                <div class="card-body">
                    <?php if ($result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Subject</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Submitted Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($complaint = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td>#<?php echo $complaint['id']; ?></td>
                                            <td><?php echo htmlspecialchars($complaint['subject']); ?></td>
                                            <td><?php echo htmlspecialchars($complaint['department_name']); ?></td>
                                            <td>
                                                <?php
                                                $status_class = '';
                                                switch ($complaint['status_id']) {
                                                    case 1: // Pending
                                                        $status_class = 'bg-warning';
                                                        break;
                                                    case 2: // In Progress
                                                        $status_class = 'bg-info';
                                                        break;
                                                    case 3: // Resolved
                                                        $status_class = 'bg-success';
                                                        break;
                                                    case 4: // Rejected
                                                        $status_class = 'bg-danger';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?php echo $status_class; ?>">
                                                    <?php echo htmlspecialchars($complaint['status_name']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($complaint['created_at'])); ?></td>
                                            <td>
                                                <a href="<?php echo $base_path; ?>/dashboard/student/view_complaint.php?id=<?php echo $complaint['id']; ?>" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <?php if ($complaint['status_id'] == 1): // Only show edit for pending complaints ?>
                                                    <a href="<?php echo $base_path; ?>/dashboard/student/edit_complaint.php?id=<?php echo $complaint['id']; ?>" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            You haven't submitted any complaints yet.
                            <a href="<?php echo $base_path; ?>/dashboard/student/new_complaint.php" class="alert-link">Submit your first complaint</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?> 