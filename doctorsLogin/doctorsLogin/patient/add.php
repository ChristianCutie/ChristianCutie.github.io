<?php
include "../includes/header.php";
include "../includes/sidebar-patient.php";
require_once "../connection/globalConnection.php";


if (session_status()  == PHP_SESSION_NONE) {
    session_start();
}
$showToast = false;
$toastMessage = '';
$isSuccess = false;
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


$sql_doctor_list = "SELECT * FROM doctortb ORDER BY doctor_acc_id ASC";

$result = $con->query($sql_doctor_list);
$doctors = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
}

if(isset($_POST['save'])) {
    $doctor_id = $_POST['doctor_id'];
    $doctor_name = $_POST['doctor_name'];
    $patient_name = $_POST['patient_name']; // Get the doctor's name from hidden field
    $type = $_POST['type'];
    $date = $_POST['date'];
    $notes = $_POST['notes'];
    
    // Validate all required fields
    if (empty($doctor_id) || empty($doctor_name) || empty($type) || empty($date) || empty($notes)) {
        $showToast = true;
        $toastMessage = "Please fill all required fields";
        $isSuccess = false;
    } else {
        try {
            $stmt = $con->prepare("INSERT INTO appointmenttb (patient_app_acc_id, doctor_app_acc_id, patient_name, doctor_name, appt_type, appt_date, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
            if ($stmt === false) {
                throw new Exception("Prepare failed: " . htmlspecialchars($con->error));
            }
            $stmt->bind_param(  "sssssss", $user_id, $doctor_id, $patient_name, $doctor_name, $type, $date, $notes);
            
            if ($stmt->execute()) {
                $showToast = true;
                $toastMessage = "Appointment successfully created";
                $isSuccess = true;
            } else {
                $showToast = true;
                $toastMessage = "Error creating appointment: " . $stmt->error;
                $isSuccess = false;
            }
            $stmt->close();
        } catch (Exception $e) {
            $showToast = true;
            $toastMessage = "Error: " . $e->getMessage();
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
    <div class=" d-flex justify-content-between">
        <h5 class=" fw-light"><a href="../admin/doctor-list.php"><span class="text-muted">Appointment</span></a>
            <span class="text-dark"> / Add New</span>
        </h5>
    </div>
    <div class="row py-4">
        <div class="col-lg-6">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <form action="" method="post">
                        <h6 class="mb-2">Appointment Information</h6>
                        <div class="alert alert-success p-2" role="alert">
                            <span style="font-size: 14px;" class="fst-italic"> Please fill the required field</span>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <input type="hidden" name="patient_name" id="" value="<?=$user_name['First_Name']. " " . $user_name['Last_Name']?>">
                                <input type="hidden" name="doctor_id" id="doctor_id" value="">
                                <input type="hidden" name="doctor_name" id="doctor_name" value="">
                                <select name="doctor_display" id="doctor_display" required class="form-select mb-4" onchange="updateHiddenDoctorId()">
                                    <option value="">-- Choose a Doctor --</option>
                                    <?php foreach ($doctors as $doctor): ?>
                                        <?php
                                        $fullName = trim($doctor['First_Name'] . ' ' . $doctor['Middle_Name'] . ' ' . $doctor['Last_Name']);
                                        ?>
                                        <option value="<?php echo htmlspecialchars($doctor['doctor_acc_id']); ?>"
                                            data-appointment-type="<?php echo htmlspecialchars($doctor['Specialization'] ?? 'General Consultation'); ?>"
                                            data-doctor-name="<?php echo htmlspecialchars($fullName); ?>">
                                            <?php echo htmlspecialchars($fullName); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="col-lg-12"><input readonly name="type" id="type" type="text" class=" form-control mb-4" placeholder="Appointment type"></div>
                                <div class="col-lg-12"><input name="date" type="date" class=" form-control mb-4" placeholder="Appointment Date"></div>
                                <div class="col-lg-12"><textarea name="notes" id="" class="form-control mb-4" style="height: 100px;" placeholder="Your notes or concern here... "></textarea></div>
                                <input name="save" type="submit" value="Save" class="btn btn-primary">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateHiddenDoctorId() {
    const selectElement = document.getElementById('doctor_display');
    const hiddenInput = document.getElementById('doctor_id');
    const doctorNameInput = document.getElementById('doctor_name');
    const typeInput = document.getElementById('type');
    
    // Get the selected option
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    
    if (selectElement.value === "") {
        // If no doctor is selected, clear all fields
        hiddenInput.value = "";
        doctorNameInput.value = "";
        typeInput.value = "";
    } else {
        // Set the hidden input values
        hiddenInput.value = selectElement.value;
        doctorNameInput.value = selectedOption.getAttribute('data-doctor-name');
        
        // Get the appointment type
        const appointmentType = selectedOption.getAttribute('data-appointment-type');
        typeInput.value = appointmentType;
    }
}

    document.addEventListener('DOMContentLoaded', function() {
        var spinner = document.getElementById('spinner');
        spinner.style.display = 'none'; // Hide the spinner when the page is loaded
    });
</script>

<div class="position-fixed top-0 end-0 p-3" style="z-index: 99999">
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
<?php

include "../includes/script.php";

?>
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