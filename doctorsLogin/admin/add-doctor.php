<?php
include "../includes/header.php";
include "../includes/sidebar-admin.php";
require_once "../connection/globalConnection.php";
$con = connection();

$showToast = false;
$toastMessage = '';
$isSuccess = true;
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


$showToast = isset($_SESSION['toast']['show']) ? $_SESSION['toast']['show'] : false;
$toastMessage = isset($_SESSION['toast']['message']) ? $_SESSION['toast']['message'] : '';
$isSuccess = isset($_SESSION['toast']['success']) ? $_SESSION['toast']['success'] : false;

unset($_SESSION['toast']);

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
} else {
    $con->connect_error;
}
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM s_admintb WHERE id = '$user_id'";
$result = $con->query(query: $query);
$user_name = $result->fetch_assoc();


if (isset($_POST['save'])) {

    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $gender = $_POST['gender'];
    $birthdate = $_POST['birthdate'];
    $age = $_POST['age'];
    $pnum = $_POST['pnum'];
    $address = $_POST['address'];
    $eaddress = $_POST['emailaddress'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $specialization = $_POST['specialization'];
    $mnl = $_POST['mnl'];
    $affiliation = $_POST['affiliation'];
    $biography = $_POST['biography'];
    $profile_photo = $_FILES['profile_photo']['name'];



    //data for insertion to database
    if (
        empty($firstname) || empty($lastname) || empty($age) || empty($pnum) || empty($address) || empty($eaddress) ||
        empty($username) || empty($password || empty($middlename) || empty($gender) || empty($birthdate) || empty($specialization) ||
            empty($mnl) || empty($affiliation))
    ) {
        $showToast = true;
        $toastMessage = "Please fill the required field";
        $isSuccess = false;
        $con->connect_error;
    } else {
        try {
            $fname = $con->real_escape_string($firstname);
            $lname = $con->real_escape_string($lastname);
            $page = $con->real_escape_string($age);
            $contact = $con->real_escape_string($pnum);
            $addr = $con->real_escape_string($address);
            $email = $con->real_escape_string($eaddress);
            $user = $con->real_escape_string($username);
            $pword = $con->real_escape_string($password);
            $mname = $con->real_escape_string($middlename);
            $sex = $con->real_escape_string($gender);
            $bday = $con->real_escape_string($birthdate);
            $specialty = $con->real_escape_string($specialization);
            $medlicense = $con->real_escape_string($mnl);
            $aff = $con->real_escape_string($affiliation);
            $bio = $con->real_escape_string($biography);
            $userType = "Doctor";

            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                $fileName = time() . '_' . $_FILES['profile_photo']['name'];
                $targetDir = "../images/";
                $targetFile = $targetDir . $fileName;

                // Validate image
                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                    throw new Exception("Only JPG, JPEG, PNG & GIF files are allowed.");
                }

                // Upload image
                if (!move_uploaded_file($_FILES['profile_photo']['tmp_name'], $targetFile)) {
                    throw new Exception("Failed to upload image.");
                }
                // Update profile photo in the database

                $sql_login = "INSERT INTO userlogintb (User_Name, Password, User_Type) 
                VALUES ('$user', '$pword', '$userType')";

                if ($con->query($sql_login) === TRUE) {
                    $login_doctor_id = $con->insert_id;

                    $sql_doctor = "INSERT INTO doctortb (doctor_acc_id, First_Name, Middle_Name, Last_Name, Gender, Date_Birth, Age, Phone_Number, Address, Email_address,
                Specialization, Med_lic_num, Affiliation, Biography, Profile_img) 
                VALUES ('$login_doctor_id', '$fname', '$mname', '$lname','$sex', '$bday', '$age', '$pnum', '$addr', '$email', '$specialty', '$medlicense', '$aff', '$bio', '$fileName')";

                    // Check if doctor was added successfully
                    if ($con->query($sql_doctor) === TRUE) {
                        $_SESSION['toast'] = [
                            'show' => true,
                            'message' => 'Profile added successfully',
                            'success' => true
                        ];
                        echo "<script>window.location.href='doctor-list.php';</script>";
                        exit();
                    } else {
                        throw new Exception("Error adding doctor: " . $con->error);
                    }
                }
            } else {
                $sql_login = "INSERT INTO userlogintb (User_Name, Password, User_Type) 
                VALUES ('$user', '$pword', '$userType')";

                if ($con->query($sql_login) === TRUE) {
                    $login_doctor_id = $con->insert_id;

                    $sql_doctor = "INSERT INTO doctortb (doctor_acc_id, First_Name, Middle_Name, Last_Name, Gender, Date_Birth, Age, Phone_Number, Address, Email_address,
                Specialization, Med_lic_num, Affiliation, Biography) 
                VALUES ('$login_doctor_id', '$fname', '$mname', '$lname','$sex', '$bday', '$age', '$pnum', '$addr', '$email', '$specialty', '$medlicense', '$aff', '$bio')";

                    if ($con->query($sql_doctor) === TRUE) {
                        $_SESSION['toast'] = [
                            'show' => true,
                            'message' => 'Profile added successfully',
                            'success' => true
                        ];
                        echo "<script>window.location.href='doctor-list.php';</script>";
                        exit();
                    } else {
                        throw new Exception("Error adding doctor: " . $con->error);
                    }
                } else {
                    throw new Exception("Error adding login: " . $con->error);
                }
            }
        } catch (Throwable $e) {
            $_SESSION['toast'] = [
                'show' => true,
                'message' => $e->getMessage(),
                'success' => false
            ];
              echo "<script>window.location.href='doctor-list.php';</script>";
            exit();
        }
    }
} else {
    $con->connect_error;
}

// Fetch user profile image
if (!empty($user_name['Profile_img']) && file_exists('../images/' . $user_name['Profile_img'])) {
    $profile_photo_default = '../images/' . $user_name['Profile_img'];
} else {
    $profile_photo_default = '../images/team_placeholder.jpg';
}
?>
<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<div class="container-fluid pt-4 px-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Add New Doctor</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="doctor-list.php">Doctors</a></li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </nav>
        </div>
        <a href="../admin/doctor-list.php" class="btn btn-outline-secondary btn-sm  rounded-0">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <!-- Form Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-0">
                <div class="card-body p-4">
                    <form method="post" action="" class="needs-validation" novalidate enctype="multipart/form-data">
                        <!-- Profile Photo -->
                        <div class="row mb-4">
                            <div class="col-12 text-center">
                                <div class="position-relative d-inline-block">
                                    <img src="../images/team_placeholder.jpg"
                                        id="profilePreview"
                                        class="rounded-circle border"
                                        style="width: 150px; height: 150px; object-fit: cover;">
                                    <label for="profile_photo"
                                        class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle">
                                        <i class="fa-solid fa-camera"></i>
                                    </label>
                                    <input type="file"
                                        id="profile_photo"
                                        name="profile_photo"
                                        class="d-none"
                                        accept="image/*"
                                        onchange="previewImage(this);">
                                </div>
                                <p class="text-muted small mt-2">Click the camera icon to upload profile photo</p>
                            </div>
                        </div>

                        <!-- Personal Information -->
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body p-4">
                                <h6 class="card-title mb-4">Personal Information</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">First Name</label>
                                        <input type="text" class="form-control rounded-0" name="firstname" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Middle Name</label>
                                        <input type="text" class="form-control rounded-0" name="middlename">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Last Name</label>
                                        <input type="text" class="form-control rounded-0" name="lastname" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Gender</label>
                                        <select class="form-select rounded-0" name="gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Date of Birth</label>
                                        <input type="date" class="form-control rounded-0" name="birthdate" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Age</label>
                                        <input type="number" class="form-control rounded-0" id="age" name="age" required readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Phone Number</label>
                                        <input type="tel" class="form-control rounded-0" name="pnum" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Email Address</label>
                                        <input type="email" class="form-control rounded-0" name="emailaddress" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Home Address</label>
                                        <textarea class="form-control rounded-0" name="address" rows="2" required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Professional Information -->
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body p-4">
                                <h6 class="card-title mb-4">Professional Information</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Specialization</label>
                                        <input type="text" class="form-control rounded-0" name="specialization" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Medical License Number</label>
                                        <input type="text" class="form-control rounded-0" name="mnl" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Hospital Affiliation</label>
                                        <input type="text" class="form-control rounded-0" name="affiliation" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Biography</label>
                                        <textarea class="form-control rounded-0" name="biography" rows="4"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Information -->
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body p-4">
                                <h6 class="card-title mb-4">Account Information</h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Username</label>
                                        <input type="text" class="form-control rounded-0" name="username" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control rounded-0" name="password" required>
                                            <button class="btn btn-outline-secondary rounded-0 toggle-password" type="button">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Confirm Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control rounded-0" name="confirm_password" required>
                                            <button class="btn btn-outline-secondary rounded-0 toggle-password" type="button">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="doctor-list.php" class="btn btn-light rounded-0">Cancel</a>
                            <button type="submit" name="save" class="btn btn-primary rounded-0">
                                <i class="fa-solid fa-save me-2"></i>Save Doctor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePreview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Password toggle functionality
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
            const form = document.querySelector('.needs-validation');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });

            // Age calculation
            const birthdateInput = document.querySelector('input[name="birthdate"]');
            const ageInput = document.querySelector('input[name="age"]');

            birthdateInput.addEventListener('change', function() {
                if (this.value) {
                    const birthDate = new Date(this.value);
                    const today = new Date();
                    let age = today.getFullYear() - birthDate.getFullYear();
                    const monthDiff = today.getMonth() - birthDate.getMonth();

                    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                        age--;
                    }

                    ageInput.value = age;
                } else {
                    ageInput.value = '';
                }
            });
        });
    </script>

    <?php include "../includes/script.php"?>
</div>