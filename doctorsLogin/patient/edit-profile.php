<?php 
include "../includes/header.php";
include "../includes/sidebar-patient.php";
require_once "../connection/globalConnection.php";


if (session_status()  == PHP_SESSION_NONE) {
    session_start();
}
$con = connection();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
} else {
    $con->connect_error;
}
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
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>
    
    <div class="col-lg-12">
        <div class="bg-light rounded h-100 p-4">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="row">
                    <!-- Profile Photo Section -->
                    <div class="col-md-3 text-center border-end">
                        <div class="position-relative d-inline-block mb-3">
                            <img src="../images/team_placeholder.jpg" alt="Patient Image" 
                                class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                            <label for="profileImage" class="position-absolute bottom-0 end-0 btn btn-sm btn-primary rounded-circle">
                                <i class="fa-solid fa-camera"></i>
                            </label>
                            <input type="file" class="d-none" id="profileImage" name="profile_photo" accept="image/*">
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
                            <div class="col-md-6">
                                <label class="form-label small">First Name</label>
                                <input type="text" class="form-control rounded-0" name="firstname" 
                                    value="<?php echo htmlspecialchars($user_name['First_Name']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Last Name</label>
                                <input type="text" class="form-control rounded-0" name="lastname" 
                                    value="<?php echo htmlspecialchars($user_name['Last_Name']); ?>" required>
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
                                <input type="tel" class="form-control rounded-0" name="phone" 
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
                            <div class="col-md-6">
                                <label class="form-label small">Date of Birth</label>
                                <input type="date" class="form-control rounded-0" name="dob" 
                                    value="<?php echo htmlspecialchars($user_name['Date_of_Birth'] ?? ''); ?>">
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