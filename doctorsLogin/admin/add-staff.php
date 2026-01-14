<?php
include("../includes/header.php");
include "../includes/sidebar-admin.php";
require_once("../connection/globalConnection.php");
$con = connection();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$showToast = false;
$toastMessage = '';
$isSuccess = true;

if (isset($_POST['save'])) {
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $birthdate = $_POST['birthdate'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $job_title = $_POST['job_title'];
    $department = $_POST['department'];
    $specialization = $_POST['specialization'];
    $med_lic_num = $_POST['med_lic_num'];
    $pnum = $_POST['pnum'];
    $address = $_POST['address'];
    $eaddress = $_POST['emailaddress'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Profile photo upload
    $profile_img = "";
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../images/";
        $file_name = time() . '_' . basename($_FILES["profile_photo"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowedTypes) && move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
            $profile_img = $file_name;
        }
    }

    // Validation
    if (
        empty($firstname) || empty($lastname) || empty($birthdate) || empty($gender) || empty($job_title) ||
        empty($department) || empty($pnum) || empty($address) || empty($eaddress) ||
        empty($username) || empty($password) || empty($confirm_password)
    ) {
        $showToast = true;
        $toastMessage = "Please fill all required fields.";
        $isSuccess = false;
    } elseif ($password !== $confirm_password) {
        $showToast = true;
        $toastMessage = "Passwords do not match.";
        $isSuccess = false;
    } else {
        try {
            $fname = $con->real_escape_string($firstname);
            $mname = $con->real_escape_string($middlename);
            $lname = $con->real_escape_string($lastname);
            $bdate = $con->real_escape_string($birthdate);
            $page = $con->real_escape_string($age);
            $pgender = $con->real_escape_string($gender);
            $pjob = $con->real_escape_string($job_title);
            $pdept = $con->real_escape_string($department);
            $pspec = $con->real_escape_string($specialization);
            $plic = $con->real_escape_string($med_lic_num);
            $contact = $con->real_escape_string($pnum);
            $addr = $con->real_escape_string($address);
            $email = $con->real_escape_string($eaddress);
            $user = $con->real_escape_string($username);
            $pword = $con->real_escape_string($password);
            $userType = "Staff";

            $con->begin_transaction();

            $sql_login = "INSERT INTO userlogintb (User_Name, Password, User_Type, Status) 
                VALUES ('$user', '$pword', '$userType', 'Active')";

            if ($con->query($sql_login) === TRUE) {
                $login_staff_id = $con->insert_id;

                $sql_staff = "INSERT INTO stafftb 
                    (staff_acc_id, First_Name, Middle_Name, Last_Name, Date_Birth, Age, Gender, Job_Title, Department, Specialization, Med_lic_num, Phone_Number, Address, Email_address, Profile_img, Status)
                    VALUES 
                    ('$login_staff_id', '$fname', '$mname', '$lname', '$bdate', '$page', '$pgender', '$pjob', '$pdept', '$pspec', '$plic', '$contact', '$addr', '$email', '$profile_img', 'Active')";

                if ($con->query($sql_staff) === TRUE) {
                    $con->commit();
                    $showToast = true;
                    $toastMessage = "Successfully Added!";
                    $isSuccess = true;
                } else {
                    throw new Exception("Error adding staff: " . $con->error);
                }
            } else {
                throw new Exception("Error adding login: " . $con->error);
            }
        } catch (Throwable $e) {
            $con->rollback();
            $showToast = true;
            $toastMessage = $e->getMessage();
            $isSuccess = false;
        }
    }
}
?>
<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<div class="container-fluid pt-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Add New Staff</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="staff-list.php">Staff</a></li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </nav>
        </div>
        <a href="staff-list.php" class="btn btn-outline-secondary btn-sm rounded-0">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>

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
                                        <label class="form-label small fw-bold">First Name *</label>
                                        <input type="text" class="form-control rounded-0" name="firstname" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Middle Name</label>
                                        <input type="text" class="form-control rounded-0" name="middlename">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Last Name *</label>
                                        <input type="text" class="form-control rounded-0" name="lastname" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Date of Birth *</label>
                                        <input type="date" class="form-control rounded-0" name="birthdate" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Age</label>
                                        <input type="number" class="form-control rounded-0" name="age" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Gender *</label>
                                        <select class="form-select rounded-0" name="gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Job Information -->
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body p-4">
                                <h6 class="card-title mb-4">Job Information</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Job Title *</label>
                                        <input type="text" class="form-control rounded-0" name="job_title" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Department *</label>
                                        <input type="text" class="form-control rounded-0" name="department" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Specialization</label>
                                        <input type="text" class="form-control rounded-0" name="specialization">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Medical License No.</label>
                                        <input type="text" class="form-control rounded-0" name="med_lic_num">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body p-4">
                                <h6 class="card-title mb-4">Contact Information</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Email Address *</label>
                                        <input type="email" class="form-control rounded-0" name="emailaddress" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Phone Number *</label>
                                        <input type="tel" class="form-control rounded-0" name="pnum" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Home Address *</label>
                                        <textarea class="form-control rounded-0" name="address" rows="2" required></textarea>
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
                                        <label class="form-label small fw-bold">Username *</label>
                                        <input type="text" class="form-control rounded-0" name="username" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Password *</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control rounded-0" name="password" required>
                                            <button class="btn btn-outline-secondary rounded-0 toggle-password" type="button">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Confirm Password *</label>
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
                            <a href="staff-list.php" class="btn btn-light rounded-0">Cancel</a>
                            <button type="submit" name="save" class="btn btn-primary rounded-0">
                                <i class="fa-solid fa-save me-2"></i>Save Staff
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999999">
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
<?php include "../includes/script.php"?>
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
            }
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
    });
</script>