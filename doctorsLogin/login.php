<?php

include_once("connection/globalConnection.php");

$con = connection();

$showToast = false;
$toastMessage = '';
$isSuccess = true;
session_start();


if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM userlogintb WHERE User_Name = '$username' AND Password = '$password'";
    $result = $con->query($query);


    if ($result && $result->num_rows == 1) {
        $rows = $result->fetch_assoc();

        if ($rows['Status'] !== 'Active') {

            $showToast = true;
            $toastMessage = "Your account is not active. Please contact support.";
            $isSuccess = false;

        } else {

            if ($rows['User_Type'] === 'Super Admin') {

                $_SESSION['user_id'] = $rows['id'];
                header("Location: /doctorsLogin/admin/dashboard.php");
                exit();

            } else if ($rows['User_Type'] === 'Doctor') {

                $_SESSION['user_id'] = $rows['id'];
                header("Location: /doctorsLogin/doctor/dashboard.php");
                exit();

            } else if ($rows['User_Type'] === 'Patient') {

                $_SESSION['user_id'] = $rows['id'];
                header("Location: /doctorsLogin/patient/dashboard.php");
                exit();

            } else if ($rows['User_Type'] === 'Staff') {

                $_SESSION['user_id'] = $rows['id'];
                header("Location: /doctorsLogin/staff/dashboard.php");
                exit();
            }
        }
    } else {

        $showToast = true;
        $toastMessage = "Invalid username or password";
        $isSuccess = false;
        // $con ->connect_error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telemedicine Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2D85C8;
            --secondary-color: #62B6CB;
            --accent-color: #1B4965;
            --light-color: #F7FBFC;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 1000px;
            margin: auto;
            position: relative;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .welcome-side {
            background: var(--accent-color);
            padding: 40px;
            color: white;
            position: relative;
            min-height: 600px;
        }

        .welcome-side h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 30px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            opacity: 0.9;
        }

        .feature-icon {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2rem;
        }

        .login-side {
            padding: 40px;
        }

        .form-control {
            height: 50px;
            border-radius: 10px;
            border: 2px solid #eee;
            padding: 0 15px;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: none;
        }

        .input-group-text {
            border: 2px solid #eee;
            border-left: none;
            background: white;
            cursor: pointer;
        }

        .btn-login {
            height: 50px;
            border-radius: 10px;
            background: var(--primary-color);
            border: none;
            font-weight: 600;
            color: white;
            width: 100%;
            margin-top: 20px;
        }

        .btn-login:hover {
            background: var(--accent-color);
        }

        .forgot-link {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .signup-link {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 600;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            opacity: 0.8;
            transition: 0.3s;
        }

        .back-btn:hover {
            opacity: 1;
            color: white;
        }

        @media (max-width: 768px) {
            .welcome-side {
                min-height: auto;
                padding: 30px;
            }
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="row g-0">
                <div class="col-lg-6 welcome-side">
                    <a href="index.php" class="back-btn">
                        <i class="fas fa-arrow-left me-2"></i>
                        Back to Home
                    </a>
                    <h2>Welcome to<br>Telemedicine</h2>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Expert Doctors</h6>
                            <p class="mb-0 small">Access to qualified healthcare professionals</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">24/7 Availability</h6>
                            <p class="mb-0 small">Healthcare support anytime, anywhere</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Secure Platform</h6>
                            <p class="mb-0 small">Your health data is protected</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 login-side">
                    <div class="p-4 p-md-5">
                        <h3 class="fw-bold mb-4">Sign In</h3>
                        <form action="" method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                            <div class="mb-4">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <span class="input-group-text" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="text-end mb-4">
                                <a href="forgot-password.php" class="forgot-link">Forgot Password?</a>
                            </div>

                            <button type="submit" name="login" class="btn btn-login">
                                Sign In
                            </button>

                            <p class="text-center mt-4">
                                Don't have an account?
                                <a href="registration-form.php" class="signup-link">Sign up</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Message -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="loginToast" class="toast <?php echo $showToast ? 'show' : ''; ?>" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header <?php echo $isSuccess ? 'bg-success text-white' : 'bg-danger text-white'; ?>">
                <strong class="me-auto"><?php echo $isSuccess ? 'Success' : 'Invalid'; ?></strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <?php echo $showToast ? $toastMessage : ''; ?>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Toast initialization
        document.addEventListener('DOMContentLoaded', function() {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            var toastList = toastElList.map(function(toastEl) {
                return new bootstrap.Toast(toastEl, {
                    autohide: true,
                    delay: 5000
                });
            });

            <?php if ($showToast): ?>
                toastList[0].show();
            <?php endif; ?>
        });
    </script>
</body>

</html>