<?php
// Start output buffering at the very beginning
ob_start();

include "../includes/header.php";
include "../includes/sidebar-patient.php";
require_once "../connection/globalConnection.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Process form submission first
if (isset($_POST['save'])) {
    $doctor_id = $_POST['doctor_id'] ?? '';
    $doctor_name = $_POST['doctor_name'] ?? '';
    $patient_name = $_POST['patient_name'] ?? '';
    $type = $_POST['type'] ?? '';
    $date = $_POST['date'] ?? '';
    $notes = $_POST['notes'] ?? '';

    if (empty($doctor_id) || empty($doctor_name) || empty($type) || empty($date) || empty($notes)) {
        $_SESSION['toast'] = [
            'show' => true,
            'message' => 'Please fill all required fields',
            'success' => false
        ];
    } else {
        try {
            $stmt = $con->prepare("INSERT INTO appointmenttb (patient_app_acc_id, doctor_app_acc_id, patient_name, doctor_name, appt_type, appt_date, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
            if ($stmt === false) {
                throw new Exception("Prepare failed: " . htmlspecialchars($con->error));
            }
            
            $stmt->bind_param("sssssss", $user_id, $doctor_id, $patient_name, $doctor_name, $type, $date, $notes);
            
            if ($stmt->execute()) {
                $_SESSION['toast'] = [
                    'show' => true,
                    'message' => 'Appointment successfully created',
                    'success' => true
                ];
                
                // Clean output buffer and redirect
                ob_end_clean();
                header("Location: ../patient/appointment.php");
                exit();
            } else {
                throw new Exception("Error creating appointment: " . $stmt->error);
            }
        } catch (Exception $e) {
            $_SESSION['toast'] = [
                'show' => true,
                'message' => 'Error: ' . $e->getMessage(),
                'success' => false
            ];
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
        }
    }
}

// Initialize variables for the view
$showToast = isset($_SESSION['toast']['show']) ? $_SESSION['toast']['show'] : false;
$toastMessage = isset($_SESSION['toast']['message']) ? $_SESSION['toast']['message'] : '';
$isSuccess = isset($_SESSION['toast']['success']) ? $_SESSION['toast']['success'] : false;

// Clear toast data after retrieving
unset($_SESSION['toast']);

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

?>
<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<div class="container-fluid pt-4 px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-light mb-0">
                <a href="../patient/appointment.php" class="text-decoration-none">
                    <span class="text-muted">Appointments</span>
                </a>
                <span class="text-dark">/New Appointment</span>
            </h5>
            <p class="text-muted small mb-0">Schedule a new appointment with our doctors</p>
        </div>
        <a href="../patient/appointment.php" class="btn btn-outline-secondary btn-sm  rounded-0">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <!-- Appointment Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="" method="post">
                        <div class="row g-4">
                            <!-- Doctor Selection -->
                            <div class="col-12">
                                <label class="form-label small fw-bold mb-3">Select Doctor</label>
                                <input type="hidden" name="patient_name" value="<?= $user_name['First_Name'] . " " . $user_name['Last_Name'] ?>">
                                <input name="doctor_id" id="doctor_id" value="<?= isset($_POST['doctor_id']) ? htmlspecialchars($_POST['doctor_id']) : ''; ?>" type="hidden">
                                <input type="hidden" name="doctor_name" id="doctor_name" value="">

                                <select name="doctor_display" id="doctor_display" required
                                    class="form-select border-0 bg-light"
                                    onchange="updateHiddenDoctorId()">
                                    <option value="">Choose a Doctor</option>
                                    <?php foreach ($doctors as $doctor):
                                        $fullName = trim($doctor['First_Name'] . ' ' . $doctor['Middle_Name'] . ' ' . $doctor['Last_Name']);
                                    ?>
                                        <option value="<?= htmlspecialchars($doctor['doctor_acc_id']); ?>"
                                            data-appointment-type="<?= htmlspecialchars($doctor['Specialization'] ?? 'General Consultation'); ?>"
                                            data-doctor-name="<?= htmlspecialchars($fullName); ?>">
                                            <?= htmlspecialchars($fullName); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Appointment Type -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Appointment Type</label>
                                <input readonly name="type" id="type" type="text"
                                    class="form-control bg-light border-0"
                                    placeholder="Select a doctor first">
                            </div>

                            <!-- Appointment Date -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Preferred Date</label>
                                <input name="date" type="date" required
                                    class="form-control bg-light border-0"
                                    min="<?= date('Y-m-d'); ?>">
                            </div>

                            <!-- Notes -->
                            <div class="col-12">
                                <label class="form-label small fw-bold">Notes or Concerns</label>
                                <textarea name="notes" required
                                    class="form-control bg-light border-0"
                                    rows="4"
                                    placeholder="Please describe your medical concerns or any specific requirements..."></textarea>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-12 pt-3">
                                <hr class="my-3">
                                <button type="submit" name="save" class="btn btn-primary px-4 rounded-0">
                                    <i class="fa-solid fa-calendar-check me-2"></i>Schedule Appointment
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Information Card -->
        <div class="col-lg-4">
            <!-- Appointment Guidelines -->
            <div class="card bg-light border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <!-- Header -->
                    <div class="d-flex align-items-center mb-4">
                        <span class="bg-primary p-3 rounded me-3">
                            <i class="fa-solid fa-stethoscope text-white fa-lg"></i>
                        </span>
                        <div>
                            <h6 class="fw-bold mb-1">Booking Guidelines</h6>
                            <p class="text-muted small mb-0">Follow these steps to schedule your appointment</p>
                        </div>
                    </div>

                    <!-- Steps Timeline -->
                    <div class="position-relative">
                        <!-- Timeline Line -->
                        <div class="position-absolute top-0 start-0 h-100"
                            style="width: 2px; background: linear-gradient(to bottom, #0d6efd 0%, #0d6efd 100%); left: 15px;">
                        </div>

                        <!-- Step 1 -->
                        <div class="d-flex mb-4 position-relative">
                            <div class="bg-white border border-primary rounded-circle p-2 shadow-sm ms-2"
                                style="width: 28px; height: 28px; z-index: 1;">
                                <span class="d-flex align-items-center justify-content-center h-100 text-primary fw-bold">1</span>
                            </div>
                            <div class="ms-3 pt-1">
                                <h6 class="mb-1 fw-semibold">Choose Your Specialist</h6>
                                <p class="text-muted small mb-0">Select a doctor based on your medical needs</p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="d-flex mb-4 position-relative">
                            <div class="bg-white border border-primary rounded-circle p-2 shadow-sm ms-2"
                                style="width: 32px; height: 32px; z-index: 1;">
                                <span class="d-flex align-items-center justify-content-center h-100 text-primary fw-bold">2</span>
                            </div>
                            <div class="ms-3 pt-1">
                                <h6 class="mb-1 fw-semibold">Schedule Date</h6>
                                <p class="text-muted small mb-0">Choose your preferred appointment time</p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="d-flex mb-4 position-relative">
                            <div class="bg-white border border-primary rounded-circle p-2 shadow-sm ms-2"
                                style="width: 32px; height: 32px; z-index: 1;">
                                <span class="d-flex align-items-center justify-content-center h-100 text-primary fw-bold">3</span>
                            </div>
                            <div class="ms-3 pt-1">
                                <h6 class="mb-1 fw-semibold">Add Details</h6>
                                <p class="text-muted small mb-0">Explain your medical concerns briefly</p>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="d-flex position-relative">
                            <div class="bg-white border border-primary rounded-circle p-2 shadow-sm ms-2"
                                style="width: 32px; height: 32px; z-index: 1;">
                                <span class="d-flex align-items-center justify-content-center h-100 text-primary fw-bold">4</span>
                            </div>
                            <div class="ms-3 pt-1">
                                <h6 class="mb-1 fw-semibold">Get Confirmation</h6>
                                <p class="text-muted small mb-0">Wait for your appointment approval</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Important Notice -->
            <div class="card border-start border-warning border-4 bg-light">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="bg-warning p-2 rounded me-3">
                            <i class="fa-solid fa-bell text-white"></i>
                        </span>
                        <h6 class="fw-bold mb-0">Important Notice</h6>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex align-items-center mb-2">
                            <i class="fa-solid fa-check-circle text-success me-2"></i>
                            <span class="small">Arrive 15 minutes before schedule</span>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="fa-solid fa-check-circle text-success me-2"></i>
                            <span class="small">Bring valid ID and medical records</span>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="fa-solid fa-check-circle text-success me-2"></i>
                            <span class="small">Wear face mask during visit</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 99999">
    <div id="loginToast" class="toast <?= $showToast ? 'show' : ''; ?>" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header <?= $isSuccess ? 'bg-success' : 'bg-danger'; ?> text-white">
            <strong class="me-auto"><?= $isSuccess ? 'Success' : 'Error'; ?></strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            <?= $showToast ? $toastMessage : ''; ?>
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