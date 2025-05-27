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
        :root {
            --primary-color: #1B4D3E;
            --secondary-color: #2E7D32;
            --accent-color: #4CAF50;
            --light-bg: #F5F5F5;
            --dark-bg: #1B4D3E;
            --text-light: #FFFFFF;
            --text-dark: #333333;
            --card-shadow: 0 0.15rem 1.75rem 0 rgba(27, 77, 62, 0.1);
        }

        body {
            background: linear-gradient(135deg, #f8f9fc 0%, #e3e6f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

          .login-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }


        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            display: flex;
            min-height: 600px;
        }

        .register-image {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .register-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(rgba(27, 77, 62, 0.9), rgba(27, 77, 62, 0.9)),
                        url('../assets/images/fjwu.jpg') center/cover;
        }

        .register-image h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            position: relative;
        }

        .register-image p {
            font-size: 1.1rem;
            text-align: center;
            position: relative;
            max-width: 400px;
        }

        .login-form {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .form-title {
            font-size: 2rem;
            color: var(--dark-bg);
            margin-bottom: 30px;
            font-weight: 700;
        }

        .form-floating {
            margin-bottom: 20px;
        }

        .form-floating > .form-control {
            padding: 1rem 0.75rem;
            height: calc(3.5rem + 2px);
            line-height: 1.25;
            border-radius: 10px;
            border: 1px solid #e3e6f0;
        }

        .form-floating > label {
            padding: 1rem 0.75rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(27, 77, 62, 0.1);
        }

        .btn-register {
            background: var(--primary-color);
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: var(--text-dark);
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .alert {
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 20px;
        }

        @media (max-width: 992px) {
            .register-card {
                flex-direction: column;
            }

            .register-image {
                padding: 30px;
                min-height: 300px;
            }

            .register-form {
                padding: 30px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="register-card">
            <div class="register-image">
                <h2>Join FJWU</h2>
                <p>Create your account to start submitting and tracking complaints</p>
            </div>
            <div class="login-form">
                <h2 class="form-title">Register</h2>
                
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="register.php">
                   <div class="form-floating mb-3"> 
                    <input type="text" name="name" class="form-control" placeholder="Name" required>
                    </div>

              <div class="form-floating mb-3"> 
                    <input type="text" name="name" class="form-control" placeholder="Name" required>
                </div>
               <div class="form-floating mb-3"> 
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
              <div class="form-floating mb-3"> 
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <div class="form-floating mb-3"> 
                    <select name="role" class="form-select" required>
                        <option value="">Select Role</option>
                        <option value="student">Student</option>
                        <option value="faculty">Faculty</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-register w-100">
                        <i class="fas fa-user-plus me-2"></i>Register
                    </button>
            </form>

             <div class="login-link">
                    Already have an account? <a href="login.php">Login here</a>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>