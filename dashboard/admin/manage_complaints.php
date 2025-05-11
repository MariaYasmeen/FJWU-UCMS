<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../includes/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /ucms/auth/login.php");
    exit();
}
$base_path = '/ucms';
// Fetch all complaints
$complaints = $conn->query("SELECT c.*, d.name as department_name, cat.name as category_name, subcat.name as subcategory_name, u.name as student_name, f.name as faculty_name FROM complaints c LEFT JOIN departments d ON c.department_id = d.id LEFT JOIN complaint_categories cat ON c.category_id = cat.id LEFT JOIN complaint_subcategories subcat ON c.subcategory_id = subcat.id LEFT JOIN users u ON c.student_id = u.id LEFT JOIN users f ON c.assigned_to = f.id ORDER BY c.created_at DESC");
// Fetch all faculty for assignment dropdown
$faculty = $conn->query("SELECT id, name FROM users WHERE role='faculty' ORDER BY name");
$faculty_options = [];
while ($f = $faculty->fetch_assoc()) {
    $faculty_options[] = $f;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Complaints - UCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>
<div class="main-content">
    <div class="container mt-4">
        <h3>Manage Complaints</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Student</th>
                        <th>Department</th>
                        <th>Category</th>
                        <th>Subcategory</th>
                        <th>Assigned Faculty</th>
                        <th>Status</th>
                        <th>Comment</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($c = $complaints->fetch_assoc()): ?>
                    <tr data-id="<?php echo $c['id']; ?>">
                        <td><?php echo $c['id']; ?></td>
                        <td><?php echo htmlspecialchars($c['subject']); ?></td>
                        <td><?php echo htmlspecialchars($c['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($c['department_name']); ?></td>
                        <td><?php echo htmlspecialchars($c['category_name']); ?></td>
                        <td><?php echo htmlspecialchars($c['subcategory_name']); ?></td>
                        <td>
                            <select class="form-select form-select-sm assign-faculty">
                                <option value="">Unassigned</option>
                                <?php foreach ($faculty_options as $f): ?>
                                    <option value="<?php echo $f['id']; ?>" <?php if ($c['assigned_to'] == $f['id']) echo 'selected'; ?>><?php echo htmlspecialchars($f['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <select class="form-select form-select-sm update-status">
                                <option value="pending" <?php if ($c['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                <option value="in_progress" <?php if ($c['status'] == 'in_progress') echo 'selected'; ?>>In Progress</option>
                                <option value="resolved" <?php if ($c['status'] == 'resolved') echo 'selected'; ?>>Resolved</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm admin-comment" value="<?php echo isset($c['admin_comment']) ? htmlspecialchars($c['admin_comment']) : ''; ?>" placeholder="Add comment...">
                            <button class="btn btn-sm btn-primary mt-1 save-comment">Save</button>
                        </td>
                        <td><?php echo $c['created_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
$(function() {
    // Assign faculty
    $('.assign-faculty').change(function() {
        var row = $(this).closest('tr');
        var complaint_id = row.data('id');
        var faculty_id = $(this).val();
        $.post('admin_complaint_action.php', {action: 'assign', complaint_id: complaint_id, faculty_id: faculty_id}, function(resp) {
            alert(resp);
        });
    });
    // Update status
    $('.update-status').change(function() {
        var row = $(this).closest('tr');
        var complaint_id = row.data('id');
        var status = $(this).val();
        $.post('admin_complaint_action.php', {action: 'status', complaint_id: complaint_id, status: status}, function(resp) {
            alert(resp);
        });
    });
    // Save comment
    $('.save-comment').click(function() {
        var row = $(this).closest('tr');
        var complaint_id = row.data('id');
        var comment = row.find('.admin-comment').val();
        $.post('admin_complaint_action.php', {action: 'comment', complaint_id: complaint_id, comment: comment}, function(resp) {
            alert(resp);
        });
    });
});
</script>
</body>
</html> 