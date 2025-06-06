<?php
include "../includes/header.php";
include "../includes/sidebar-patient.php";
require_once "../connection/globalConnection.php";


if (isset($_POST['update_profile'])) {
    $user_id = $_SESSION['user_id'];
    $firstname = $_POST['firstname'] ?? '';
    $middlename = $_POST['middlename'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $blood_type = $_POST['blood_type'] ?? '';
    $civil_status = $_POST['civil_status'] ?? '';
    $nationality = $_POST['nationality'] ?? '';
    $occupation = $_POST['occupation'] ?? '';
    $medical_conditions = $_POST['medical_conditions'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $age = $_POST['age'] ?? '';

    try {
        // Handle image upload if a file was selected
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

            // Update query with image
            $sql = "UPDATE patienttb SET 
                Profile_img = ?,
                First_Name = ?, 
                Middle_Name = ?,
                Last_Name = ?,
                Date_Birth = ?,
                Gender = ?,
                Blood_Type = ?,
                Civil_Status = ?,
                Nationality = ?,
                Occupation = ?,
                Medical_Conditions = ?,
                Email_address = ?,
                Phone_Number = ?,
                Address = ?,
                Age = ?
                WHERE patient_acc_id = ?";

            $stmt = $con->prepare($sql);
            $stmt->bind_param(
                "ssssssssssssssss",
                $fileName,
                $firstname,
                $middlename,
                $lastname,
                $dob,
                $gender,
                $blood_type,
                $civil_status,
                $nationality,
                $occupation,
                $medical_conditions,
                $email,
                $phone,
                $address,
                $age,
                $user_id
            );
        } else {
            // Update without image
            $sql = "UPDATE patienttb SET 
                First_Name = ?, 
                Middle_Name = ?,
                Last_Name = ?,
                Date_Birth = ?,
                Gender = ?,
                Blood_Type = ?,
                Civil_Status = ?,
                Nationality = ?,
                Occupation = ?,
                Medical_Conditions = ?,
                Email_address = ?,
                Phone_Number = ?,
                Address = ?,
                Age = ?
                WHERE patient_acc_id = ?";

            $stmt = $con->prepare($sql);
            $stmt->bind_param(
                "sssssssssssssss",
                $firstname,
                $middlename,
                $lastname,
                $dob,
                $gender,
                $blood_type,
                $civil_status,
                $nationality,
                $occupation,
                $medical_conditions,
                $email,
                $phone,
                $address,
                $age,
                $user_id
            );
        }

        if ($stmt->execute()) {
            
            $_SESSION['toast'] = [
                'show' => true,
                'message' => 'Profile updated successfully',
                'success' => true
            ];
            echo "<script>window.location.href='profile.php';</script>";
            exit();
        } else {
            throw new Exception("Error updating profile: " . $stmt->error);
        }
    } catch (Exception $e) {
         $_SESSION['toast'] = [
        'show' => true,
        'message' => $e->getMessage(),
        'success' => false
    ];
    echo "<script>window.location.href='profile.php';</script>";
    exit();
    }
}

$con = connection();

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM patienttb WHERE patient_acc_id = '$user_id'";
$result = $con->query($sql);
$user_name = $result->fetch_assoc();
$fullname = $user_name['First_Name'] . " " . $user_name['Last_Name'];
if ($result === false) {
    die("Error fetching user data: " . htmlspecialchars($con->error));
}
if ($result->num_rows === 0) {
    header('Location: /login.php');
    exit();
}
if ($user_name === null) {
    die("User not found.");
}
if ($user_name === false) {
    die("Error fetching user data: " . htmlspecialchars($con->error));
}
$result = $con->query($sql);
if ($result === false) {
    die("Error fetching user data: " . htmlspecialchars($con->error));
}



//Fetching the profile information

?>

<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<div class="container-fluid pt-4 px-4">
    <div class="d-flex justify-content-between mb-3">
        <h5 class="fw-light">
            <a href="../patient/profile.php"><span class="text-muted">Profile</span></a>
            <span class="text-dark"> / Edit Profile</span>
        </h5>
        <a href="../patient/profile.php" class="btn btn-outline-secondary btn-sm rounded-0">
            <i class="fa-solid fa-arrow-left"></i> Back to Profile
        </a>
    </div>

    <div class="col-lg-12">
        <div class="bg-light rounded h-100 p-4">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
                <div class="row">
                    <!-- Profile Photo Section -->
                    <div class="col-md-3 text-center border-end">
                        <div class="position-relative d-inline-block mb-3">
                            <?php
                            if (!empty($user_name['Profile_img']) && file_exists('../images/' . $user_name['Profile_img'])) {
                                $profile_photo_default = '../images/' . $user_name['Profile_img'];
                            } else {
                                $profile_photo_default = '../images/team_placeholder.jpg';
                            }
                            ?>
                            <img src="<?= htmlspecialchars($profile_photo_default) ?>"
                                alt="Patient Image"
                                id="profilePreview"
                                class="img-fluid rounded-circle"
                                style="width: 150px; height: 150px; object-fit: cover;">
                            <label for="profileImage" class="position-absolute bottom-0 end-0 btn btn-sm btn-primary rounded-circle">
                                <i class="fa-solid fa-camera"></i>
                            </label>
                            <input type="file"
                                class="d-none"
                                id="profileImage"
                                name="profile_photo"
                                accept="image/*"
                                onchange="previewImage(this);">
                        </div>
                        <p class="text-muted small">Click the camera icon to change profile photo</p>
                    </div>

                    <!-- Profile Information Section -->
                    <div class="col-md-9">
                        <div class="row g-3">
                            <!-- Personal Information -->
                            <div class="col-12">
                                <h6 class="border-bottom pb-2">Personal Information</h6>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">First Name</label>
                                <input type="text" class="form-control rounded-0" name="firstname"
                                    value="<?php echo htmlspecialchars($user_name['First_Name']); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Middle Name</label>
                                <input type="text" class="form-control rounded-0" name="middlename"
                                    value="<?php echo htmlspecialchars($user_name['Middle_Name'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Last Name</label>
                                <input type="text" class="form-control rounded-0" name="lastname"
                                    value="<?php echo htmlspecialchars($user_name['Last_Name']); ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">Date of Birth</label>
                                <input type="date" class="form-control rounded-0" name="dob" id="dob"
                                    value="<?php echo htmlspecialchars($user_name['Date_Birth'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Age</label>
                                <input type="text" class="form-control rounded-0" id="age" name="age" disabled
                                    value="<?php
                                            if (!empty($user_name['Date_Birth'])) {
                                                $birthDate = new DateTime($user_name['Date_Birth']);
                                                $today = new DateTime();
                                                $age = $birthDate->diff($today)->y;
                                                echo $age;
                                            }
                                            ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Gender</label>
                                <select class="form-select rounded-0" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?php echo ($user_name['Gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo ($user_name['Gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                                    <option value="Other" <?php echo ($user_name['Gender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Blood Type</label>
                                <select class="form-select rounded-0" name="blood_type">
                                    <option value="">Select Blood Type</option>
                                    <?php
                                    $blood_types = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
                                    foreach ($blood_types as $type) {
                                        $selected = ($user_name['Blood_Type'] ?? '') === $type ? 'selected' : '';
                                        echo "<option value=\"$type\" $selected>$type</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">Civil Status</label>
                                <select class="form-select rounded-0" name="civil_status" required>
                                    <option value="">Select Civil Status</option>
                                    <?php
                                    $civil_statuses = ['Single', 'Married', 'Divorced', 'Widowed'];
                                    foreach ($civil_statuses as $status) {
                                        $selected = ($user_name['Civil_Status'] ?? '') === $status ? 'selected' : '';
                                        echo "<option value=\"$status\" $selected>$status</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Nationality</label>
                                <input type="text" class="form-control rounded-0" name="nationality"
                                    value="<?php echo htmlspecialchars($user_name['Nationality'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Occupation</label>
                                <input type="text" class="form-control rounded-0" name="occupation"
                                    value="<?php echo htmlspecialchars($user_name['Occupation'] ?? ''); ?>">
                            </div>

                            <div class="col-12">
                                <label class="form-label small">Medical Conditions/Allergies</label>
                                <textarea class="form-control rounded-0" name="medical_conditions" rows="2"
                                    placeholder="List any existing medical conditions or allergies"><?php echo htmlspecialchars($user_name['Medical_Conditions'] ?? ''); ?></textarea>
                            </div>

                            <!-- Contact Information -->
                            <div class="col-12 mt-4">
                                <h6 class="border-bottom pb-2">Contact Information</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Email Address</label>
                                <input type="email" class="form-control rounded-0" name="email"
                                    value="<?php echo htmlspecialchars($user_name['Email_address'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Phone Number</label>
                                <input type="text" class="form-control rounded-0" name="phone" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    value="<?php echo htmlspecialchars($user_name['Phone_Number'] ?? ''); ?>">
                            </div>

                            <!-- Additional Information -->
                            <div class="col-12 mt-4">
                                <h6 class="border-bottom pb-2">Additional Information</h6>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small">Complete Address</label>
                                <textarea class="form-control rounded-0" name="address" rows="2"
                                    placeholder="Enter your complete address"><?php echo htmlspecialchars($user_name['Address'] ?? ''); ?></textarea>
                            </div>

                            <!-- Form Buttons -->
                            <div class="col-12 mt-4">
                                <button type="submit" name="update_profile" class="btn btn-primary rounded-0">
                                    <i class="fa-solid fa-save me-1"></i> Save Changes
                                </button>
                                <a href="../patient/profile.php" class="btn btn-light rounded-0">
                                    <i class="fa-solid fa-times me-1"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<?php

include "../includes/script.php";

?>
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];

            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Please select an image file');
                input.value = '';
                return;
            }

            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profilePreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
<script>
    document.getElementById('dob').addEventListener('change', function() {
        const dob = new Date(this.value);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }

        document.getElementById('age').value = age;
    });
</script>