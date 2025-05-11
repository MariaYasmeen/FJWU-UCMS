<?php
require_once '../../includes/header.php';
require_once '../../includes/db_connect.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: {$base_path}/auth/login.php");
    exit();
}

// Check if complaint ID is provided
if (!isset($_GET['id'])) {
    header("Location: {$base_path}/dashboard/student/my_complaints.php");
    exit();
}

$complaint_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch complaint details
$stmt = $conn->prepare("
    SELECT c.*, d.name as department_name, s.name as status_name, u.name as user_name
    FROM complaints c 
    LEFT JOIN departments d ON c.department_id = d.id 
    LEFT JOIN complaint_status s ON c.status_id = s.id 
    LEFT JOIN users u ON c.user_id = u.id
    WHERE c.id = ? AND c.user_id = ?
");
$stmt->bind_param("ii", $complaint_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: {$base_path}/dashboard/student/my_complaints.php");
    exit();
}

$complaint = $result->fetch_assoc();

// Fetch complaint responses
$stmt = $conn->prepare("
    SELECT r.*, u.name as responder_name, u.role as responder_role
    FROM complaint_responses r
    LEFT JOIN users u ON r.user_id = u.id
    WHERE r.complaint_id = ?
    ORDER BY r.created_at ASC
");
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$responses = $stmt->get_result();
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Complaint Details</h5>
                    <a href="<?php echo $base_path; ?>/dashboard/student/my_complaints.php" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Complaints
                    </a>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h4><?php echo htmlspecialchars($complaint['subject']); ?></h4>
                        <div class="text-muted mb-3">
                            <small>
                                Submitted by <?php echo htmlspecialchars($complaint['user_name']); ?> on 
                                <?php echo date('F j, Y g:i A', strtotime($complaint['created_at'])); ?>
                            </small>
                        </div>
                        <div class="mb-3">
                            <span class="badge bg-primary"><?php echo htmlspecialchars($complaint['department_name']); ?></span>
                            <?php
                            $status_class = '';
                            switch ($complaint['status_id']) {
                                case 1: $status_class = 'bg-warning'; break;
                                case 2: $status_class = 'bg-info'; break;
                                case 3: $status_class = 'bg-success'; break;
                                case 4: $status_class = 'bg-danger'; break;
                            }
                            ?>
                            <span class="badge <?php echo $status_class; ?>">
                                <?php echo htmlspecialchars($complaint['status_name']); ?>
                            </span>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <?php echo nl2br(htmlspecialchars($complaint['description'])); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Responses Section -->
                    <h5 class="mb-3">Responses</h5>
                    <?php if ($responses->num_rows > 0): ?>
                        <?php while ($response = $responses->fetch_assoc()): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong><?php echo htmlspecialchars($response['responder_name']); ?></strong>
                                        <small class="text-muted">
                                            <?php echo date('M d, Y g:i A', strtotime($response['created_at'])); ?>
                                        </small>
                                    </div>
                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($response['message'])); ?></p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            No responses yet.
                        </div>
                    <?php endif; ?>

                    <!-- Add Response Form -->
                    <?php if ($complaint['status_id'] != 3 && $complaint['status_id'] != 4): ?>
                        <form action="<?php echo $base_path; ?>/dashboard/student/add_response.php" method="POST" class="mt-4">
                            <input type="hidden" name="complaint_id" value="<?php echo $complaint_id; ?>">
                            <div class="mb-3">
                                <label for="message" class="form-label">Add Response</label>
                                <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Send Response
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?> 