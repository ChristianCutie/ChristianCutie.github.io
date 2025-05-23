<?php
include("includes/header.php");
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
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f0f9fc;
            overflow-x: hidden;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        .wave-bg {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 40%;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%2377d8e8' fill-opacity='0.2' d='M0,192L48,176C96,160,192,128,288,133.3C384,139,480,181,576,181.3C672,181,768,139,864,144C960,149,1056,203,1152,202.7C1248,203,1344,149,1392,122.7L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
            background-size: cover;
            background-repeat: no-repeat;
            z-index: -1;
        }

        .registration-container {
            max-width: 900px;
            margin: 40px auto;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .left-panel {
            background-color: #22a6ce;
            color: white;
            padding: 40px 30px;
            position: relative;
            height: 100%;
            min-height: 500px;
        }

        .left-panel h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 40px;
            margin-top: 20px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .feature-icon {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .back-btn {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .back-btn:hover {
            color: rgba(255, 255, 255, 0.8);
        }

        .right-panel {
            padding: 40px;
        }

        .user-type {
            display: flex;
            justify-content: center;
            margin-bottom: 25px;
        }

        .user-option {
            margin: 0 15px;
            text-align: center;
            opacity: 0.5;
            transition: all 0.3s;
            cursor: pointer;
        }

        .user-option.active {
            opacity: 1;
        }

        .user-icon {
            background-color: #f0f9fc;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
        }

        .user-option.active .user-icon {
            background-color: #e1f7fa;
            border: 2px solid #22a6ce;
        }



        .form-label {
            font-size: 14px;
            color: #666;
        }

        .sign-up-btn {
            background-color: #20c997;
            border: none;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            margin-top: 10px;
        }

        .sign-up-btn:hover {
            background-color: #18ae84;
        }

        .login-link {
            color: #22a6ce;
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 767px) {
            .left-panel {
                min-height: auto;
                padding: 30px 20px;
            }

            .registration-container {
                margin: 20px;
            }
        }

        .wave-accent {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 120px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%2319849d' fill-opacity='0.4' d='M0,224L60,240C120,256,240,288,360,272C480,256,600,192,720,186.7C840,181,960,235,1080,256C1200,277,1320,267,1380,261.3L1440,256L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
            background-size: cover;
            z-index: 0;
        }
    </style>
</head>

<body>
    <div class="wave-bg"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="registration-container">
                    <div class="row g-0">
                        <!-- Left Panel -->
                        <div class="col-md-5 left-panel">
                            <a href="login.php" class="back-btn mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
                                </svg>
                                <span class="ms-2">Back</span>
                            </a>

                            <h1>Expert advice from top doctors</h1>

                            <div class="feature-item">
                                <div class="feature-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-star" viewBox="0 0 16 16">
                                        <path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.565.565 0 0 0-.163-.505L1.71 6.745l4.052-.576a.525.525 0 0 0 .393-.288L8 2.223l1.847 3.658a.525.525 0 0 0 .393.288l4.052.575-2.906 2.77a.565.565 0 0 0-.163.506l.694 3.957-3.686-1.894a.503.503 0 0 0-.461 0z" />
                                    </svg>
                                </div>
                                <div>Expert advice from top doctors</div>
                            </div>

                            <div class="feature-item">
                                <div class="feature-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                                        <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z" />
                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z" />
                                    </svg>
                                </div>
                                <div>Available 24/7 on any device</div>
                            </div>

                            <div class="feature-item">
                                <div class="feature-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-shield-check" viewBox="0 0 16 16">
                                        <path d="M5.338 1.59a61.44 61.44 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.725 10.725 0 0 0 2.287 2.233c.346.244.652.42.893.533.12.057.218.095.293.118a.55.55 0 0 0 .101.025.615.615 0 0 0 .1-.025c.076-.023.174-.061.294-.118.24-.113.547-.29.893-.533a10.726 10.726 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.775 11.775 0 0 1-2.517 2.453 7.159 7.159 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7.158 7.158 0 0 1-1.048-.625 11.777 11.777 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 62.456 62.456 0 0 1 5.072.56z" />
                                        <path d="M10.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0z" />
                                    </svg>
                                </div>
                                <div>Private questions answered within 24 hrs</div>
                            </div>

                            <div class="wave-accent"></div>
                        </div>

                        <!-- Right Panel -->
                        <div class="col-md-7 right-panel">
                            <!-- <div class="user-type">
                                <div class="user-option active">
                                    <div class="user-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#22a6ce" class="bi bi-person" viewBox="0 0 16 16">
                                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                                        </svg>
                                    </div>
                                    <div>Patient</div>
                                </div>
                                <div class="user-option">
                                    <div class="user-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#22a6ce" class="bi bi-heart-pulse" viewBox="0 0 16 16">
                                            <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053.918 3.995.78 5.323 1.508 7H.43c-2.128-5.697 4.165-8.83 7.394-5.857.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17c3.23-2.974 9.522.159 7.394 5.856h-1.078c.728-1.677.59-3.005.108-3.947C13.486.878 10.4.28 8.717 2.01L8 2.748ZM2.212 10h1.315C4.593 11.183 6.05 12.458 8 13.795c1.949-1.337 3.407-2.612 4.473-3.795h1.315c-1.265 1.566-3.14 3.25-5.788 5-2.648-1.75-4.523-3.434-5.788-5Z"></path>
                                            <path d="M10.464 3.314a.5.5 0 0 0-.945.049L7.921 8.956 6.464 5.314a.5.5 0 0 0-.88-.091L3.732 8H.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 .416-.223l1.473-2.209 1.647 4.118a.5.5 0 0 0 .945-.049l1.598-5.593 1.457 3.642A.5.5 0 0 0 12 9h3.5a.5.5 0 0 0 0-1h-3.162l-1.874-4.686Z"></path>
                                        </svg>
                                    </div>
                                    <div>Doctor</div>
                                </div>
                            </div> -->

                            <h4 class="mb-4 text-center">Create your account</h4>
                            <p class="text-center text-muted mb-4">Sign up to connect with our network of medical professionals.</p>

                            <form action="" method="post">
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="firstName" class="form-label">First Name</label>
                                        <input type="text" name="firstname" class="form-control" id="firstName" placeholder="First Name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lastName" class="form-label">Last Name</label>
                                        <input type="text" name="lastname" class="form-control" id="lastName" placeholder="Last Name" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" name="emailaddress" class="form-control" id="email" placeholder="Email Address" required>
                                    <div class="text-muted mt-1" style="font-size: 12px;">We'll never share your email with anyone else</div>
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" name="phonenumber" class="form-control" id="phone" placeholder="Phone Number">
                                </div>

                                <div class="mb-3">
                                    <label for="dob" class="form-label">Date of Birth</label>
                                    <input type="date" name="dob" class="form-control" id="dob" required>
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" id="username" placeholder="Username">
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Create Password</label>
                                    <div class="input-group">
                                        <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                                        <span class="input-group-text">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z" />
                                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="text-muted mt-1" style="font-size: 12px;">Password must be at least 8 characters</div>
                                </div>

                                <div class="mb-3">
                                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm Password" required>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="termsCheck" required>
                                    <label class="form-check-label" for="termsCheck">I agree to the <a href="#" class="login-link">Terms of Service</a> and <a href="#" class="login-link">Privacy Policy</a></label>
                                </div>

                                <div class="mb-3 mt-4">
                                    <input type="submit" class="btn sign-up-btn" name="submit" value="Create Account">
                                </div>
                            </form>

                            <div class="text-center mt-4">
                                Already have an account? <a href="login.php" class="login-link">Sign in</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Toast for success -->

     <div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
        <div id="loginToast" class="toast <?php echo $showToast ? 'show' : ''; ?>" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header <?php echo $isSuccess ? 'bg-success text-white' : 'bg-danger text-white'; ?>">
                <strong class="me-auto" id="toastTitle"><?php echo $isSuccess ? 'Success' : 'Error'; ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage">
                <?php echo $showToast ? $toastMessage : ''; ?>
            </div>
        </div>
    </div>  

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle between patient and doctor user types
        document.querySelectorAll('.user-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.user-option').forEach(el => el.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Password visibility toggle
        document.querySelector('.input-group-text').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('svg');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.innerHTML = '<path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>';
            } else {
                passwordInput.type = 'password';
                icon.innerHTML = '<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>';
            }
        });

        // Form validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(event) {
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirmPassword');

            if (password.value !== confirmPassword.value) {
                event.preventDefault();
                alert('Passwords do not match!');
            }

            if (password.value.length < 8) {
                event.preventDefault();
                alert('Password must be at least 8 characters long!');
            }
        });
    </script>
     <script>
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