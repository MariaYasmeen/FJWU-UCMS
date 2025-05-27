<?php
session_start();
require_once '../../includes/db.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /ucms/auth/login.php");
    exit();
}

$base_path = '/ucms';
$user_id = $_SESSION['user_id'];

// Get all complaints submitted by the student
$sql = "SELECT c.*, d.name as department_name, 
        CASE 
            WHEN c.status = 'pending' THEN 'warning'
            WHEN c.status = 'in_progress' THEN 'info'
            WHEN c.status = 'resolved' THEN 'success'
            ELSE 'secondary'
        END as status_color
        FROM complaints c
        LEFT JOIN departments d ON c.department_id = d.id
        WHERE c.student_id = ?
        ORDER BY c.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Complaints - UCMS</title>
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
            --light-bg: #f8f9fc;
            --card-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }

        body {
            background-color: var(--light-bg);
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .complaint-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .complaint-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.15);
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.85rem;
            text-transform: capitalize;
        }

        .complaint-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2e59d9;
            margin-bottom: 10px;
        }

        .complaint-meta {
            font-size: 0.9rem;
            color: var(--secondary-color);
        }

        .complaint-meta i {
            width: 20px;
            text-align: center;
            margin-right: 5px;
        }

        .complaint-description {
            color: #5a5c69;
            margin: 15px 0;
            line-height: 1.6;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
            margin-top: 20px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e3e6f0;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -34px;
            top: 0;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--primary-color);
            border: 2px solid white;
        }

        .timeline-date {
            font-size: 0.85rem;
            color: var(--secondary-color);
            margin-bottom: 5px;
        }

        .timeline-content {
            background: var(--light-bg);
            padding: 15px;
            border-radius: 10px;
            font-size: 0.95rem;
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

        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--secondary-color);
            margin-bottom: 20px;
        }

        .empty-state h4 {
            color: #5a5c69;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--secondary-color);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <?php include '../../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-gray-800">My Complaints</h3>
                <a href="new_complaint.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Submit New Complaint
                </a>
            </div>

            <?php if ($result->num_rows > 0): ?>
                <?php while ($complaint = $result->fetch_assoc()): ?>
                    <div class="complaint-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="complaint-title"><?php echo htmlspecialchars($complaint['title']); ?></h5>
                                <div class="complaint-meta">
                                    <p class="mb-1">
                                        <i class="fas fa-building"></i>
                                        <?php echo htmlspecialchars($complaint['department_name']); ?>
                                    </p>
                                    <p class="mb-0">
                                        <i class="fas fa-clock"></i>
                                        Submitted on <?php echo date('F j, Y', strtotime($complaint['created_at'])); ?>
                                    </p>
                                </div>
                            </div>
                            <span class="status-badge bg-<?php echo $complaint['status_color']; ?> text-white">
                                <?php echo str_replace('_', ' ', $complaint['status']); ?>
                            </span>
                        </div>

                        <div class="complaint-description">
                            <?php echo nl2br(htmlspecialchars($complaint['description'])); ?>
                        </div>

                        <?php if ($complaint['status'] !== 'pending'): ?>
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-date">
                                        <?php echo date('F j, Y g:i A', strtotime($complaint['updated_at'])); ?>
                                    </div>
                                    <div class="timeline-content">
                                        <?php if ($complaint['status'] === 'in_progress'): ?>
                                            <i class="fas fa-info-circle text-info me-2"></i>
                                            Your complaint is being reviewed by the department.
                                        <?php elseif ($complaint['status'] === 'resolved'): ?>
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            Your complaint has been resolved.
                                            <?php if (!empty($complaint['resolution_notes'])): ?>
                                                <div class="mt-2">
                                                    <strong>Resolution Notes:</strong><br>
                                                    <?php echo nl2br(htmlspecialchars($complaint['resolution_notes'])); ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard-list"></i>
                    <h4>No Complaints Yet</h4>
                    <p>You haven't submitted any complaints yet.</p>
                    <a href="new_complaint.php" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Submit Your First Complaint
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 