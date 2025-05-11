<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $conn->real_escape_string($_POST['role']);

    // Check if email already exists
    $check_sql = "SELECT id FROM users WHERE email = '$email'";
    $result = $conn->query($check_sql);
    
    if ($result->num_rows > 0) {
        $error = "Email already exists";
    } else {
        // Insert new user
        $sql = "INSERT INTO users (name, email, password, role) 
                VALUES ('$name', '$email', '$password', '$role')";
        
        if ($conn->query($sql)) {
            // Get the new user's ID
            $user_id = $conn->insert_id;
            
            // Set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;
            $_SESSION['name'] = $name;
            
            // Redirect to homepage
            header("Location: ../index.php");
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .register-form {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; 
        }

        .card {
            max-width: 500px;
            width: 100%;
            padding: 2rem;
            border-radius: 15px;
            background-color: #ffffff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #495057;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            padding: 10px;
            border: 1px solid #dee2e6;
            transition: border-color 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0069d9;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }

        button[type="submit"] {
            width: 100%;
            background-color: #0069d9;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .text-center {
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            .card {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="register-form">
        <div class="card">
            <h2>User Registration</h2>
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="register.php">
                <div class="form-group">
                    <input type="text" name="name" class="form-control" placeholder="Name" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <select name="role" class="form-select" required>
                        <option value="">Select Role</option>
                        <option value="student">Student</option>
                        <option value="faculty">Faculty</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <button type="submit" name="register">Register</button>
                </div>
            </form>

            <div class="text-center">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
