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
$message = '';
$message_type = '';

// Get all departments
$dept_sql = "SELECT * FROM departments ORDER BY name";
$dept_result = $conn->query($dept_sql);

// Get all categories
$cat_sql = "SELECT * FROM categories ORDER BY name";
$cat_result = $conn->query($cat_sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department_id = $_POST['department_id'];
    $category_id = $_POST['category_id'];
    $subcategory_id = $_POST['subcategory_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $priority = $_POST['priority'];

    // Validate inputs
    if (empty($title) || empty($description) || empty($department_id) || empty($category_id) || empty($subcategory_id)) {
        $message = "All fields are required.";
        $message_type = "danger";
    } else {
        // Validate subcategory belongs to selected category
        $validate_sql = "SELECT id FROM subcategories WHERE id = ? AND category_id = ?";
        $validate_stmt = $conn->prepare($validate_sql);
        $validate_stmt->bind_param("ii", $subcategory_id, $category_id);
        $validate_stmt->execute();
        $validate_result = $validate_stmt->get_result();

        if ($validate_result->num_rows === 0) {
            $message = "Invalid category/subcategory combination.";
            $message_type = "danger";
        } else {
            $sql = "INSERT INTO complaints (student_id, department_id, category_id, subcategory_id, title, description, priority) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiiisss", $user_id, $department_id, $category_id, $subcategory_id, $title, $description, $priority);

            if ($stmt->execute()) {
                // Create notification for admin
                $notification_sql = "INSERT INTO notifications (user_id, title, message, type) 
                                   SELECT id, 'New Complaint', ?, 'complaint' 
                                   FROM users WHERE role = 'admin'";
                $notification_stmt = $conn->prepare($notification_sql);
                $notification_message = "New complaint submitted: " . $title;
                $notification_stmt->bind_param("s", $notification_message);
                $notification_stmt->execute();

                $message = "Complaint submitted successfully!";
                $message_type = "success";
                // Clear form data after successful submission
                $_POST = array();
            } else {
                $message = "Error submitting complaint: " . $conn->error;
                $message_type = "danger";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Complaint - UCMS</title>
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

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 20px 25px;
        }

        .card-body {
            padding: 25px;
        }

        .form-label {
            font-weight: 500;
            color: #5a5c69;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #e3e6f0;
            padding: 10px 15px;
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.1);
        }

        .btn {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .alert {
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
        }

        .priority-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .category-description {
            font-size: 0.85rem;
            color: var(--secondary-color);
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <?php include '../../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-gray-800">Submit New Complaint</h3>
                <a href="my_complaints.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to My Complaints
                </a>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department</label>
                                <select class="form-select" name="department_id" required>
                                    <option value="">Select Department</option>
                                    <?php while ($dept = $dept_result->fetch_assoc()): ?>
                                        <option value="<?php echo $dept['id']; ?>" 
                                                <?php echo (isset($_POST['department_id']) && $_POST['department_id'] == $dept['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($dept['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Priority</label>
                                <select class="form-select" name="priority" required>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category_id" id="category" required>
                                    <option value="">Select Category</option>
                                    <?php while ($cat = $cat_result->fetch_assoc()): ?>
                                        <option value="<?php echo $cat['id']; ?>"
                                                <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <div class="category-description" id="category-description"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Subcategory</label>
                                <select class="form-select" name="subcategory_id" id="subcategory" required>
                                    <option value="">Select Subcategory</option>
                                </select>
                                <div class="category-description" id="subcategory-description"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" required
                                   value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="5" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                        </div>

                        <div class="text-end">
                            <a href="my_complaints.php" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Submit Complaint
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to fetch subcategories based on selected category
        document.getElementById('category').addEventListener('change', function() {
            const categoryId = this.value;
            const subcategorySelect = document.getElementById('subcategory');
            const categoryDescription = document.getElementById('category-description');
            const subcategoryDescription = document.getElementById('subcategory-description');
            
            // Clear current options and descriptions
            subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
            categoryDescription.textContent = '';
            subcategoryDescription.textContent = '';
            
            if (categoryId) {
                // Fetch subcategories using AJAX
                fetch(`get_subcategories.php?category_id=${categoryId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(subcat => {
                            const option = document.createElement('option');
                            option.value = subcat.id;
                            option.textContent = subcat.name;
                            option.dataset.description = subcat.description;
                            subcategorySelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error:', error));
            }
        });

        // Update descriptions when selections change
        document.getElementById('category').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const description = selectedOption.dataset.description || '';
            document.getElementById('category-description').textContent = description;
        });

        document.getElementById('subcategory').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const description = selectedOption.dataset.description || '';
            document.getElementById('subcategory-description').textContent = description;
        });
    </script>
</body>
</html> 