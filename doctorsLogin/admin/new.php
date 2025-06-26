<?php 
include "../includes/header.php";
include "../includes/sidebar-admin.php";
require_once "../connection/globalConnection.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$con = connection();

// Initialize variables for form handling
$showToast = false;
$toastMessage = '';
$isSuccess = false;

// Fetch all doctors for dropdown
$doctors_query = "SELECT doctor_acc_id, CONCAT(First_Name, ' ', Last_Name) as doctor_name, Specialization 
                 FROM doctortb 
                 WHERE Status = 'Active' 
                 ORDER BY First_Name";
$doctors_result = $con->query($doctors_query);

// Fetch all patients for dropdown
$patients_query = "SELECT patient_acc_id, CONCAT(First_Name, ' ', Last_Name) as patient_name 
                  FROM patienttb 
                  WHERE Status = 'Active' 
                  ORDER BY First_Name";
$patients_result = $con->query($patients_query);

// Handle form submission
if(isset($_POST['submit'])) {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $appt_date = $_POST['appt_date'];
    $appt_time = $_POST['appt_time'];
    $reason = $_POST['reason'];
    
    $sql = "INSERT INTO appointmenttb (patient_app_acc_id, doctor_app_acc_id, appt_date, appt_time, notes, status) 
            VALUES (?, ?, ?, ?, ?, 'Pending')";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param("iisss", $patient_id, $doctor_id, $appt_date, $appt_time, $reason);
    
    if($stmt->execute()) {
        $showToast = true;
        $toastMessage = "Appointment created successfully!";
        $isSuccess = true;
    } else {
        $showToast = true;
        $toastMessage = "Error creating appointment.";
        $isSuccess = false;
    }
}
?>
<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="mb-0">Create New Appointment</h5>
                        <p class="text-muted small mb-0">Schedule a new appointment for a patient</p>
                    </div>
                </div>

                <form method="POST" action="" class="needs-validation" novalidate>
                    <div class="row g-3">
                        <!-- Patient Selection -->
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select class="form-select" id="patient_id" name="patient_id" required>
                                    <option value="">Select Patient</option>
                                    <?php while($patient = $patients_result->fetch_assoc()): ?>
                                        <option value="<?= htmlspecialchars($patient['patient_acc_id']) ?>">
                                            <?= htmlspecialchars($patient['patient_name']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <label for="patient_id">Patient</label>
                                <div class="invalid-feedback">Please select a patient.</div>
                            </div>
                        </div>

                        <!-- Doctor Selection -->
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select class="form-select" id="doctor_id" name="doctor_id" required>
                                    <option value="">Select Doctor</option>
                                    <?php while($doctor = $doctors_result->fetch_assoc()): ?>
                                        <option value="<?= htmlspecialchars($doctor['doctor_acc_id']) ?>">
                                            Dr. <?= htmlspecialchars($doctor['doctor_name']) ?> 
                                            (<?= htmlspecialchars($doctor['Specialization']) ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <label for="doctor_id">Doctor</label>
                                <div class="invalid-feedback">Please select a doctor.</div>
                            </div>
                        </div>

                        <!-- Date Selection -->
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="date" class="form-control" id="appt_date" name="appt_date"
                                       min="<?= date('Y-m-d') ?>" required>
                                <label for="appt_date">Appointment Date</label>
                                <div class="invalid-feedback">Please select a valid date.</div>
                            </div>
                        </div>

                        <!-- Time Selection -->
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="time" class="form-control" id="appt_time" name="appt_time" required>
                                <label for="appt_time">Appointment Time</label>
                                <div class="invalid-feedback">Please select a valid time.</div>
                            </div>
                        </div>

                        <!-- Reason for Appointment -->
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="reason" name="reason" 
                                          style="height: 100px" required></textarea>
                                <label for="reason">Reason for Appointment</label>
                                <div class="invalid-feedback">Please provide a reason.</div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12">
                            <button type="submit" name="submit" class="btn btn-primary btn-sm rounded-0">
                                <i class="fas fa-calendar-plus me-2"></i>Create Appointment
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast <?= $showToast ? 'show' : '' ?>" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header <?= $isSuccess ? 'bg-success' : 'bg-danger' ?> text-white">
                <strong class="me-auto"><?= $isSuccess ? 'Success' : 'Error' ?></strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <?= htmlspecialchars($toastMessage) ?>
            </div>
        </div>
    </div>
</div>
<?php include "../includes/script.php"; ?>

<script>
    // Form validation
    (() => {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()

    // Initialize toast
    const toastElList = document.querySelectorAll('.toast')
    const toastList = [...toastElList].map(toastEl => new bootstrap.Toast(toastEl))
    <?php if ($showToast): ?>
        toastList[0].show()
    <?php endif; ?>
</script>

<style>
    .form-floating > .form-select {
        padding-top: 1.625rem;
        padding-bottom: 0.625rem;
    }
    
    .form-floating > label {
        z-index: 1;
    }
    
    .toast-header .btn-close-white {
        filter: invert(1) grayscale(100%) brightness(200%);
    }
</style>