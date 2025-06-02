<?php
include "../includes/sidebar-admin.php";

$con = connection();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$showToast = isset($_SESSION['toast']['show']) ? $_SESSION['toast']['show'] : false;
$toastMessage = isset($_SESSION['toast']['message']) ? $_SESSION['toast']['message'] : '';
$isSuccess = isset($_SESSION['toast']['success']) ? $_SESSION['toast']['success'] : false;

unset($_SESSION['toast']);

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
    $blood_type = $_POST['blood_type'];
    $civil_status = $_POST['civil_status'];
    $nationality = $_POST['nationality'];
    $emailaddress = $_POST['emailaddress'];
    $pnum = $_POST['pnum'];
    $address = $_POST['address'];
    $medical_conditions = $_POST['medical_conditions'];
    $username = $_POST['username'];
    $password = $_POST['password'];


    if (empty($firstname) || empty($lastname) || empty($birthdate) || empty($gender) || empty($blood_type) ||
     empty($civil_status) || empty($nationality) || empty($emailaddress) || empty($pnum) || empty($address) ||
      empty($username) || empty($password)) {

        $_SESSION['toast'] = [
            'show' => true,
            'message' => 'Profile added successfully',
            'success' => true
        ];
        echo "<script>window.location.href='patient-list.php';</script>";
        exit();
    } else {
        // Handle file upload
        $profile_photo = $_FILES['profile_photo']['name'];
        $target_dir = "../images/";
        $target_file = $target_dir . basename($profile_photo);
        $uploadOk = 1;

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES['profile_photo']['tmp_name']);
        if ($check === false) {
            $isSuccess = false;
            $toastMessage = 'File is not an image.';
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES['profile_photo']['size'] > 5000000) {
            $isSuccess = false;
            $toastMessage = 'Sorry, your file is too large.';
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (!in_array(pathinfo($target_file, PATHINFO_EXTENSION), ['jpg', 'png', 'jpeg', 'gif'])) {
            $isSuccess = false;
            $toastMessage = 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
            $uploadOk = 0;
        }
    }
}

?>

<div class="container-fluid pt-4 px-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Add New Patient</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="patient-list.php">Patients</a></li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </nav>
        </div>
        <a href="../admin/patient-list.php" class="btn btn-outline-secondary btn-sm  rounded-0">
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
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Blood Type *</label>
                                        <select class="form-select rounded-0" name="blood_type" required>
                                            <option value="">Select Blood Type</option>
                                            <option value="A+">A+</option>
                                            <option value="A-">A-</option>
                                            <option value="B+">B+</option>
                                            <option value="B-">B-</option>
                                            <option value="AB+">AB+</option>
                                            <option value="AB-">AB-</option>
                                            <option value="O+">O+</option>
                                            <option value="O-">O-</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Civil Status *</label>
                                        <select class="form-select rounded-0" name="civil_status" required>
                                            <option value="">Select Status</option>
                                            <option value="Single">Single</option>
                                            <option value="Married">Married</option>
                                            <option value="Widowed">Widowed</option>
                                            <option value="Divorced">Divorced</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Nationality *</label>
                                        <input type="text" class="form-control rounded-0" name="nationality" required>
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

                        <!-- Medical Information -->
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body p-4">
                                <h6 class="card-title mb-4">Medical Information</h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Medical Conditions</label>
                                        <textarea class="form-control rounded-0" name="medical_conditions" rows="3"
                                            placeholder="List any existing medical conditions..."></textarea>
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
                            <a href="patient-list.php" class="btn btn-light rounded-0">Cancel</a>
                            <button type="submit" name="save" class="btn btn-primary rounded-0">
                                <i class="fa-solid fa-save me-2"></i>Save Patient
                            </button>
                        </div>
                    </form>
                </div>
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