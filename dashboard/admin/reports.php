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

// Get date range from request
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Get complaint statistics
$stats_sql = "SELECT 
    COUNT(*) as total_complaints,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_complaints,
    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_complaints,
    SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_complaints,
    AVG(CASE WHEN status = 'resolved' THEN TIMESTAMPDIFF(HOUR, created_at, updated_at) ELSE NULL END) as avg_resolution_time
FROM complaints 
WHERE created_at BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)";

$stmt = $conn->prepare($stats_sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

// Get complaints by department
$dept_sql = "SELECT d.name, COUNT(*) as count
             FROM complaints c
             JOIN departments d ON c.department_id = d.id
             WHERE c.created_at BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)
             GROUP BY d.id, d.name
             ORDER BY count DESC";

$stmt = $conn->prepare($dept_sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$dept_stats = $stmt->get_result();

// Get complaints by category
$cat_sql = "SELECT cc.name, COUNT(*) as count
            FROM complaints c
            JOIN complaint_categories cc ON c.category_id = cc.id
            WHERE c.created_at BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)
            GROUP BY cc.id, cc.name
            ORDER BY count DESC";

$stmt = $conn->prepare($cat_sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$cat_stats = $stmt->get_result();

// Get faculty performance
$faculty_sql = "SELECT u.name, 
                COUNT(*) as total_assigned,
                SUM(CASE WHEN c.status = 'resolved' THEN 1 ELSE 0 END) as resolved,
                AVG(CASE WHEN c.status = 'resolved' THEN TIMESTAMPDIFF(HOUR, c.created_at, c.updated_at) ELSE NULL END) as avg_resolution_time
                FROM complaints c
                JOIN users u ON c.assigned_to = u.id
                WHERE c.created_at BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)
                GROUP BY u.id, u.name
                ORDER BY total_assigned DESC";

$stmt = $conn->prepare($faculty_sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$faculty_stats = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - UCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>/assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <?php include '../../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <h2 class="mb-4">Reports & Analytics</h2>

            <!-- Date Range Filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary d-block">Apply Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Overview Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Complaints</h5>
                            <h2><?php echo $stats['total_complaints']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Pending</h5>
                            <h2><?php echo $stats['pending_complaints']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">In Progress</h5>
                            <h2><?php echo $stats['in_progress_complaints']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Resolved</h5>
                            <h2><?php echo $stats['resolved_complaints']; ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Complaints by Department</h5>
                            <canvas id="departmentChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Complaints by Category</h5>
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Faculty Performance -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Faculty Performance</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Faculty Name</th>
                                    <th>Total Assigned</th>
                                    <th>Resolved</th>
                                    <th>Resolution Rate</th>
                                    <th>Avg. Resolution Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($faculty = $faculty_stats->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($faculty['name']); ?></td>
                                        <td><?php echo $faculty['total_assigned']; ?></td>
                                        <td><?php echo $faculty['resolved']; ?></td>
                                        <td><?php echo round(($faculty['resolved'] / $faculty['total_assigned']) * 100, 1); ?>%</td>
                                        <td><?php echo round($faculty['avg_resolution_time'], 1); ?> hours</td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Department Chart
        const deptCtx = document.getElementById('departmentChart').getContext('2d');
        new Chart(deptCtx, {
            type: 'bar',
            data: {
                labels: [<?php 
                    $dept_stats->data_seek(0);
                    $labels = [];
                    $data = [];
                    while ($row = $dept_stats->fetch_assoc()) {
                        $labels[] = "'" . addslashes($row['name']) . "'";
                        $data[] = $row['count'];
                    }
                    echo implode(',', $labels);
                ?>],
                datasets: [{
                    label: 'Number of Complaints',
                    data: [<?php echo implode(',', $data); ?>],
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Category Chart
        const catCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(catCtx, {
            type: 'pie',
            data: {
                labels: [<?php 
                    $cat_stats->data_seek(0);
                    $labels = [];
                    $data = [];
                    while ($row = $cat_stats->fetch_assoc()) {
                        $labels[] = "'" . addslashes($row['name']) . "'";
                        $data[] = $row['count'];
                    }
                    echo implode(',', $labels);
                ?>],
                datasets: [{
                    data: [<?php echo implode(',', $data); ?>],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)',
                        'rgba(153, 102, 255, 0.5)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>
</body>
</html> 