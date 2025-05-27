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

// Get complaint history
$sql = "SELECT c.*, u.name as student_name, d.name as department_name,
        fc.status as faculty_status, fc.comment as faculty_comment, fc.created_at as action_date
        FROM complaints c 
        JOIN users u ON c.student_id = u.id 
        JOIN departments d ON c.department_id = d.id 
        JOIN faculty_complaints fc ON c.id = fc.complaint_id
        WHERE fc.faculty_id = ? 
        ORDER BY fc.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint History - UCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
        }

        body {
            background-color: #f8f9fc;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e3e6f0;
            border-radius: 15px 15px 0 0 !important;
        }

        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline-item {
            position: relative;
            padding-left: 40px;
            margin-bottom: 30px;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: -30px;
            width: 2px;
            background: #e3e6f0;
        }

        .timeline-item:last-child:before {
            display: none;
        }

        .timeline-marker {
            position: absolute;
            left: 0;
            top: 0;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--primary-color);
            border: 4px solid #fff;
            box-shadow: 0 0 0 2px var(--primary-color);
        }

        .timeline-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }

        .badge {
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 500;
        }

        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead th {
            background: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
            color: var(--secondary-color);
            font-weight: 600;
        }

        .btn {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 10px;
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
    </style>
</head>
<body>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="text-gray-800">Complaint History</h3>
            <a href="assigned_complaints.php" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Assigned Complaints
            </a>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="timeline">
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="mb-0"><?php echo htmlspecialchars($row['subject']); ?></h5>
                                                <span class="badge bg-<?php 
                                                    echo $row['faculty_status'] === 'pending' ? 'warning' : 
                                                        ($row['faculty_status'] === 'in_progress' ? 'info' : 'success'); 
                                                ?>">
                                                    <?php echo ucfirst($row['faculty_status']); ?>
                                                </span>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <p class="mb-1"><strong>Student:</strong> <?php echo htmlspecialchars($row['student_name']); ?></p>
                                                    <p class="mb-1"><strong>Department:</strong> <?php echo htmlspecialchars($row['department_name']); ?></p>
                                                </div>
                                                <div class="col-md-6 text-md-end">
                                                    <p class="mb-1"><strong>Date:</strong> <?php echo date('M d, Y H:i', strtotime($row['action_date'])); ?></p>
                                                </div>
                                            </div>
                                            <?php if ($row['faculty_comment']): ?>
                                                <div class="mt-3">
                                                    <p class="mb-1"><strong>Your Comment:</strong></p>
                                                    <p class="mb-0 p-3 bg-light rounded"><?php echo nl2br(htmlspecialchars($row['faculty_comment'])); ?></p>
                                                </div>
                                            <?php endif; ?>
                                            <div class="mt-3">
                                                <a href="view_complaint.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i>View Details
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>No complaint history found.
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 