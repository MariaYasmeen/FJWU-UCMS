<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../includes/db.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /ucms/auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
// Fetch student info
$user_sql = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch roll number (if you have a roll_number field in users, otherwise set to empty)
$roll_number = isset($_SESSION['roll_number']) ? $_SESSION['roll_number'] : '';

// Fetch departments
$departments = $conn->query("SELECT id, name FROM departments ORDER BY name");

// Fetch categories
$categories = $conn->query("SELECT id, name FROM complaint_categories ORDER BY name");

// Handle form submission
$success = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = $user['name'];
    $roll_number = $_POST['roll_number'] ?? '';
    $email = $user['email'];
    $department_id = $_POST['department_id'];
    $subject = $_POST['subject'];
    $category_id = $_POST['category_id'];
    $subcategory_id = $_POST['subcategory_id'] ?? null;
    $description = $_POST['description'];
    $urgency_level = $_POST['urgency_level'];
    $date_of_incident = $_POST['date_of_incident'];
    $location_of_incident = $_POST['location_of_incident'];
    $anonymous_submission = isset($_POST['anonymous_submission']) ? 1 : 0;
    $attachment_path = null;

    // Handle file upload
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../uploads/complaints/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $filename = uniqid() . '_' . basename($_FILES['attachment']['name']);
        $target_file = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file)) {
            $attachment_path = '/ucms/uploads/complaints/' . $filename;
        }
    }

    $sql = "INSERT INTO complaints (student_id, roll_number, department_id, category_id, subcategory_id, subject, description, urgency_level, attachment_path, date_of_incident, location_of_incident, anonymous_submission) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issiiissssss", $student_id, $roll_number, $department_id, $category_id, $subcategory_id, $subject, $description, $urgency_level, $attachment_path, $date_of_incident, $location_of_incident, $anonymous_submission);
    if ($stmt->execute()) {
        $success = true;
    } else {
        $error = "Failed to submit complaint. Please try again.";
    }
}

$base_path = '/ucms';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit New Complaint - UCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_path; ?>/assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<style>
  /* General body and container */
  body {
    background-color: #f7faf6;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #2f4f32;
    margin: 0;
    padding: 0;
  }
  
  .main-content {
    padding: 3rem 4rem !important; 
    margin: auto;
  }
  
  h3 {
    font-weight: 700;
    font-size: 2rem;
    color: #2e7d32; /* dark green */
    margin-bottom: 1.5rem;
    text-align: center;
    padding-bottom: 40px;
  }
  
  /* Card-like white background with soft shadow */
  .container {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(46, 125, 50, 0.15);
    padding: 2rem 2.5rem;
  }
  
  /* Form labels */
  label.form-label {
    color: #3a5d22;
    font-weight: 600;
    margin-bottom: 0.4rem;
    display: block;
  }
  
  /* Inputs and selects */
  input.form-control,
  select.form-select,
  textarea.form-control {
    background-color: #f2f8f2;
    border: 1.8px solid #a4d4a5;
    border-radius: 10px;
    padding: 0.6rem 0.75rem;
    color: #2f4f32;
    font-size: 1rem;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    width: 100%;
    box-sizing: border-box;
  }
  
  input.form-control:focus,
  select.form-select:focus,
  textarea.form-control:focus {
    border-color: #4caf50;
    box-shadow: 0 0 8px 0 rgba(76, 175, 80, 0.4);
    outline: none;
    background-color: #e8f5e9;
  }
  
  /* Readonly inputs style */
  input[readonly] {
    background-color: #dcedc8;
    color: #33691e;
    font-weight: 600;
  }
  
  /* Textarea */
  textarea.form-control {
    resize: vertical;
    min-height: 100px;
  }
  
  /* Row spacing */
  .row.mb-3 {
    margin-bottom: 1.5rem;
  }
  
  /* Checkbox style */
  .form-check-label {
    color: #3a5d22;
    font-weight: 600;
  }
  .form-check-input {
    border-radius: 4px;
    border: 1.5px solid #a4d4a5;
    width: 18px;
    height: 18px;
    margin-top: 0.3rem;
    cursor: pointer;
    transition: border-color 0.3s ease;
  }
  .form-check-input:checked {
    background-color: #4caf50;
    border-color: #388e3c;
  }
  
  /* Button */
  button.btn-primary {
    background-color: #4caf50;
    border: none;
    padding: 0.65rem 1.5rem;
    font-size: 1.1rem;
    font-weight: 700;
    border-radius: 10px;
    color: white;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(76, 175, 80, 0.35);
    transition: background-color 0.3s ease;
    width: 100%;
    max-width: 260px;
    display: block;
    margin: 1rem auto 0;
  }
  button.btn-primary:hover,
  button.btn-primary:focus {
    background-color: #388e3c;
    box-shadow: 0 6px 14px rgba(56, 142, 60, 0.6);
    outline: none;
  }
  
  /* Alerts */
  .alert-success {
    background-color: #dcedc8;
    color: #33691e;
    border-radius: 10px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.8rem;
    font-weight: 600;
    border: 1.5px solid #aed581;
  } 
  
  .alert-danger {
    background-color: #f8d7da;
    color: #842029;
    border-radius: 10px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.8rem;
    font-weight: 600;
    border: 1.5px solid #f5c2c7;
  }
  
  /* Responsive adjustments */
  @media (max-width: 768px) {
    .container {
      padding: 1.5rem 1.8rem;
    }
    button.btn-primary {
      max-width: 100%;
    }
  }
</style>

<body>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>
<div class="main-content">
    <div class=" mt-4">
        <h3 class="headingcss">Student Complaint Form</h3>

        <?php if ($success): ?>
            <div class="alert alert-success">Complaint submitted successfully!</div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data" id="complaintForm">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Student Name</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Roll Number</label>
                    <input type="text" class="form-control" name="roll_number" value="<?php echo htmlspecialchars($roll_number); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Department</label>
                    <select class="form-select" name="department_id" required>
                        <option value="">Select Department</option>
                        <?php while ($d = $departments->fetch_assoc()): ?>
                            <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Complaint Title</label>
                    <input type="text" class="form-control" name="subject" required maxlength="255">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Complaint Category</label>
                    <select class="form-select" name="category_id" id="categorySelect" required>
                        <option value="">Select Category</option>
                        <?php while ($c = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Complaint Sub-Category</label>
                    <select class="form-select" name="subcategory_id" id="subcategorySelect" required>
                        <option value="">Select Sub-Category</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Urgency Level</label>
                    <select class="form-select" name="urgency_level" required>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Complaint Description</label>
                <textarea class="form-control" name="description" rows="4" required></textarea>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Date of Incident</label>
                    <input type="date" class="form-control" name="date_of_incident" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Location of Incident</label>
                    <input type="text" class="form-control" name="location_of_incident" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Upload Attachment(s)</label>
                    <input type="file" class="form-control" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.gif">
                </div>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="anonymous_submission" id="anonymousCheck">
                <label class="form-check-label" for="anonymousCheck">
                    Submit Anonymously (your name will be hidden from faculty)
                </label>
            </div>
            <button type="submit" class="btn btn-primary">Submit Complaint</button>
        </form>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#categorySelect').on('change', function() {
        var categoryId = $(this).val();
        $('#subcategorySelect').html('<option value="">Loading...</option>');
        if (categoryId) {
            $.ajax({
                url: 'subcategory_ajax.php',
                type: 'GET',
                data: { category_id: categoryId },
                success: function(data) {
                    $('#subcategorySelect').html(data);
                },
                error: function() {
                    $('#subcategorySelect').html('<option value="">Error loading subcategories</option>');
                }
            });
        } else {
            $('#subcategorySelect').html('<option value="">Select Sub-Category</option>');
        }
    });
});
</script>
</body>
</html> 