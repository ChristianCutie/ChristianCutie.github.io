<?php
include_once("connection/globalConnection.php");

$con = connection();

if (isset($_POST['submit'])) {

    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['emailaddress'];
    $phone_number = $_POST['phonenumber'];
    $dob = $_POST['dob'];
    $password = $_POST['password'];
    $username = $_POST['username'];
    $usertype = "Patient";


        $fname = $con->real_escape_string($firstname);
        $lastname = $con->real_escape_string($lastname);
        $email = $con->real_escape_string($email);
        $phone_number = $con->real_escape_string($phone_number);
        $dob = $con->real_escape_string($dob);
        $username = $con->real_escape_string($username);
        $password = $con->real_escape_string($password);


        $sql_login_patient = "INSERT INTO userlogintb (User_Name, Password, User_Type) VALUES ('$username', '$password', '$usertype')";

        if ($con->query($sql_login_patient) === TRUE) {

            $get_id_from_patient = $con->insert_id;

            $sql_insert_patient = "INSERT INTO patienttb (patient_acc_id, First_Name, Last_Name, Email_address, Phone_Number, Date_Birth) VALUES 
                        ('$get_id_from_patient', '$fname', '$lastname', '$email', '$phone_number', '$dob')";

            if ($con->query($sql_insert_patient) === TRUE) {
                $con->commit();
                $showToast = true;
                $toastMessage = "Successfully Added You can back to login page and sign in your account.";
                $isSuccess = true;
            } else {
                $showToast = true;
                $toastMessage = "Error updating doctor: " . $con->error;
                $isSuccess = false;
            }
        }
    }

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telemedicine Registration</title>
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

        .registration-wrapper {
            width: 100%;
            max-width: 1100px;
            margin: auto;
            position: relative;
        }

        .registration-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .welcome-side {
            background: var(--accent-color);
            padding: 40px;
            color: white;
            position: relative;
            height: 100%;
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
            background: rgba(255,255,255,0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2rem;
        }

        .form-side {
            padding: 40px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .form-control {
            height: 45px;
            border-radius: 10px;
            border: 2px solid #eee;
            padding: 0 15px;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: none;
        }

        .btn-register {
            height: 45px;
            border-radius: 10px;
            background: var(--primary-color);
            border: none;
            font-weight: 600;
            color: white;
            width: 100%;
        }

        .btn-register:hover {
            background: var(--accent-color);
        }

        .login-link {
            color: var(--primary-color);
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
                padding: 30px;
            }
        }
    </style>
</head>

<body>
    <div class="registration-wrapper">
        <div class="registration-card">
            <div class="row g-0">
                <!-- Left Panel -->
                <div class="col-lg-4 welcome-side"  style="background-color: #1B4965;">
                    <a href="login.php" class="back-btn">
                        <i class="fas fa-arrow-left me-2"></i>
                        Back to Login
                    </a>
                    <h2 class="mt-5 mb-4">Join Our Healthcare Community</h2>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Expert Care</h6>
                            <p class="mb-0 small">Connect with qualified healthcare professionals</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-laptop-medical"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Virtual Consultations</h6>
                            <p class="mb-0 small">Get medical advice from anywhere</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Secure & Private</h6>
                            <p class="mb-0 small">Your data is protected and encrypted</p>
                        </div>
                    </div>
                </div>

                <!-- Right Panel (Form) -->
                <div class="col-lg-8 form-side">
                    <div class="px-4">
                        <h3 class="fw-bold mb-4">Create Account</h3>
                        <form method="post" action="" class="needs-validation" novalidate>
                            <!-- Personal Information -->
                            <div class="mb-4">
                                <h6 class="text-muted mb-3">Personal Information</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small">First Name</label>
                                        <input type="text" name="firstname" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Last Name</label>
                                        <input type="text" name="lastname" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="mb-4">
                                <h6 class="text-muted mb-3">Contact Information</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small">Email Address</label>
                                        <input type="email" name="emailaddress" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Phone Number</label>
                                        <input type="tel" name="phonenumber" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Details -->
                            <div class="mb-4">
                                <h6 class="text-muted mb-3">Additional Details</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small">Date of Birth</label>
                                        <input type="date" name="dob" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Credentials -->
                            <div class="mb-4">
                                <h6 class="text-muted mb-3">Account Credentials</h6>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label small">Username</label>
                                        <input type="text" name="username" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Password</label>
                                        <div class="input-group">
                                            <input type="password" name="password" class="form-control" id="password" required>
                                            <span class="input-group-text toggle-password">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Confirm Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="confirmPassword" required>
                                            <span class="input-group-text toggle-password">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="terms" required>
                                    <label class="form-check-label small" for="terms">
                                        I agree to the <a href="#" class="login-link">Terms of Service</a> and
                                        <a href="#" class="login-link">Privacy Policy</a>
                                    </label>
                                </div>
                            </div>

                            <button type="submit" name="submit" class="btn btn-register mb-3">
                                Create Account
                            </button>

                            <p class="text-center small">
                                Already have an account? <a href="login.php" class="login-link">Sign in</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Message -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="registerToast" class="toast <?php echo $showToast ? 'show' : ''; ?>" role="alert" aria-atomic="true">
            <div class="toast-header <?php echo $isSuccess ? 'bg-success text-white' : 'bg-danger text-white'; ?>">
                <strong class="me-auto"><?php echo $isSuccess ? 'Success' : 'Error'; ?></strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                <?php echo $showToast ? $toastMessage : ''; ?>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password visibility toggle
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.previousElementSibling;
                const icon = this.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        // Form validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirmPassword');

            if (password.value !== confirmPassword.value) {
                event.preventDefault();
                alert('Passwords do not match!');
            }

            form.classList.add('was-validated');
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