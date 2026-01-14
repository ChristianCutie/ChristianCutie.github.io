<?php
include "../includes/header.php";
include "../includes/sidebar-patient.php";
require_once "../connection/globalConnection.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Process form submission
if (isset($_POST['save'])) {
    $doctor_id = $_POST['doctor_id'] ?? '';
    $doctor_name = $_POST['doctor_name'] ?? '';
    $patient_name = $_POST['patient_name'] ?? '';
    $type = $_POST['type'] ?? '';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $notes = $_POST['notes'] ?? '';

    if (empty($doctor_id) || empty($doctor_name) || empty($type) || empty($date) || empty($time) || empty($notes)) {
        $_SESSION['toast'] = [
            'show' => true,
            'message' => 'Please fill all required fields',
            'success' => false
        ];
    } else {
        try {
            $stmt = $con->prepare("INSERT INTO appointmenttb (patient_app_acc_id, doctor_app_acc_id, patient_name, doctor_name, appt_type, appt_date, appt_time, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
            if ($stmt === false) {
                throw new Exception("Prepare failed: " . htmlspecialchars($con->error));
            }
            $user_id = $_SESSION['user_id'];
            $stmt->bind_param("ssssssss", $user_id, $doctor_id, $patient_name, $doctor_name, $type, $date, $time, $notes);

            if ($stmt->execute()) {
                $_SESSION['toast'] = [
                    'show' => true,
                    'message' => 'Appointment successfully created',
                    'success' => true
                ];
                echo "<script>window.location.href='appointment.php';</script>";
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

// Toast variables
$showToast = isset($_SESSION['toast']['show']) ? $_SESSION['toast']['show'] : false;
$toastMessage = isset($_SESSION['toast']['message']) ? $_SESSION['toast']['message'] : '';
$isSuccess = isset($_SESSION['toast']['success']) ? $_SESSION['toast']['success'] : false;
unset($_SESSION['toast']);

// User and doctor data
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM patienttb WHERE patient_acc_id = '$user_id'";
$result = $con->query($sql);
$user_name = $result->fetch_assoc();

$sql_doctor_list = "SELECT * FROM doctortb ORDER BY doctor_acc_id ASC";
$result = $con->query($sql_doctor_list);
$doctors = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
}
?>
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
        <a href="../patient/appointment.php" class="btn btn-outline-secondary btn-sm rounded-0">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Appointments
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="" method="post">
                        <div class="mb-3">
                            <label class="form-label small fw-bold mb-2">Select Doctor</label>
                            <input type="hidden" name="patient_name" value="<?= htmlspecialchars($user_name['First_Name'] . " " . $user_name['Last_Name']) ?>">
                            <input name="doctor_id" id="doctor_id" type="hidden">
                            <input type="hidden" name="doctor_name" id="doctor_name">
                            <select name="doctor_display" id="doctor_display" required class="form-select border-0 bg-light">
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

                        <div class="mb-3">
                            <label class="form-label small fw-bold mb-2">Appointment Type</label>
                            <input readonly name="type" id="type" type="text"
                                class="form-control bg-light border-0"
                                placeholder="Select a doctor first">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold mb-2">Pick Appointment Date</label>
                            <div id="calendar" style="pointer-events: none; opacity: 0.6;"></div>
                            <input type="hidden" name="date" id="selected-date" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold mb-2">Available Times</label>
                            <div id="available-times-container" class="d-flex flex-wrap gap-2"></div>
                            <input type="hidden" name="time" id="selected-time" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold mb-2">Notes or Concerns</label>
                            <textarea name="notes" required
                                class="form-control bg-light border-0"
                                rows="4"
                                placeholder="Please describe your medical concerns or any specific requirements..."></textarea>
                        </div>

                        <div class="pt-3">
                            <hr class="my-3">
                            <button type="submit" name="save" class="btn btn-primary px-4 rounded-0 w-100">
                                <i class="fa-solid fa-calendar-check me-2"></i>Schedule Appointment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Information Card -->
        <div class="col-lg-4">
            <div class="card bg-light border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <span class="bg-primary p-3 rounded me-3">
                            <i class="fa-solid fa-stethoscope text-white fa-lg"></i>
                        </span>
                        <div>
                            <h6 class="fw-bold mb-1">Booking Guidelines</h6>
                            <p class="text-muted small mb-0">Follow these steps to schedule your appointment</p>
                        </div>
                    </div>
                    <div class="position-relative mb-4">
                        <div class="position-absolute top-0 start-0 h-100"
                            style="width: 2px; background: linear-gradient(to bottom, #0d6efd 0%, #0d6efd 100%); left: 15px;">
                        </div>
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
                    <hr style="border-top: 3px solidrgba(222, 226, 230, 0.32);">
                    <div class="mt-3">
                        <h6 class="fw-bold mb-2"><i class="fa-regular fa-calendar-check me-2 text-primary"></i>Calendar Legend</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex align-items-center mb-2">
                                <span style="display:inline-block;width:22px;height:22px;background:#0d6efd33;border-radius:4px;border:1.5px solid #0d6efd;margin-right:10px;"></span>
                                <span class="small">Selected/Available Date</span>
                            </li>
                            <li class="d-flex align-items-center mb-2">
                                <span style="display:inline-block;width:22px;height:22px;background:#ffc10799;border-radius:4px;border:1.5px solid #ffc107;margin-right:10px;"></span>
                                <span class="small">Booked Date</span>
                            </li>
                            <li class="d-flex align-items-center">
                                <span style="display:inline-block;width:22px;height:22px;background:#dc3545cc;border-radius:4px;border:1.5px solid #dc3545;margin-right:10px;"></span>
                                <span class="small">Holiday / Unavailable</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
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

    <!-- Toast Notification -->
    <div class="position-fixed bottom-0 start-0 p-3" style="z-index: 9999;">
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
</div>

<!-- FullCalendar CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<!-- Custom Styles for Time Buttons -->
<style>
    #available-times-container .btn.active,
    #available-times-container .btn.btn-primary {
        background-color: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }

    #available-times-container .btn[disabled] {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .fc-daygrid-day.booked-date {
        background: #ffc10799 !important;
        border: 1.5px solid #ffc107 !important;
    }

    .fc-daygrid-day.holiday-date {
        background: #dc3545cc !important;
        border: 1.5px solid #dc3545 !important;
    }
</style>

<script>
    function updateHiddenDoctorId() {
        const selectElement = document.getElementById('doctor_display');
        const hiddenInput = document.getElementById('doctor_id');
        const doctorNameInput = document.getElementById('doctor_name');
        const typeInput = document.getElementById('type');
        const calendarDiv = document.getElementById('calendar');
        const selectedDateInput = document.getElementById('selected-date');
        // Get the selected option
        const selectedOption = selectElement.options[selectElement.selectedIndex];

        if (selectElement.value === "") {
            hiddenInput.value = "";
            doctorNameInput.value = "";
            typeInput.value = "";
            // Disable calendar and times
            calendarDiv.style.pointerEvents = "none";
            calendarDiv.style.opacity = "0.6";
            selectedDateInput.value = "";
            document.getElementById('available-times-container').innerHTML = '';
            document.getElementById('selected-time').value = '';
        } else {
            hiddenInput.value = selectElement.value;
            doctorNameInput.value = selectedOption.getAttribute('data-doctor-name');
            const appointmentType = selectedOption.getAttribute('data-appointment-type');
            typeInput.value = appointmentType;
            // Enable calendar
            calendarDiv.style.pointerEvents = "auto";
            calendarDiv.style.opacity = "1";
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var selectedDoctorInput = document.getElementById('doctor_id');
        var selectedDateInput = document.getElementById('selected-date');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 400,
            selectable: true,
            validRange: {
                start: new Date().toISOString().split('T')[0]
            },
            dateClick: function(info) {
                if (!selectedDoctorInput.value) {
                    alert("Please select a doctor first.");
                    return;
                }
                selectedDateInput.value = info.dateStr;
                fetchAvailableTimes();
                // Highlight selected date
                calendar.getEvents().forEach(event => event.remove());
                calendar.addEvent({
                    start: info.dateStr,
                    allDay: true,
                    display: 'background',
                    backgroundColor: '#0d6efd33'
                });
            }
        });
        calendar.render();

        // Disable calendar until doctor is selected
        calendarEl.style.pointerEvents = "none";
        calendarEl.style.opacity = "0.6";

        document.getElementById('doctor_display').addEventListener('change', function() {
            updateHiddenDoctorId();
            // Clear selected date and times when doctor changes
            selectedDateInput.value = "";
            document.getElementById('available-times-container').innerHTML = '';
            document.getElementById('selected-time').value = '';
        });

        function to12HourFormat(time24) {
            // Handles "HH:MM" or "HH:MM:SS"
            let [hour, minute] = time24.split(':');
            hour = parseInt(hour, 10);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            hour = hour % 12 || 12;
            return `${hour}:${minute} ${ampm}`;
        }

        window.fetchAvailableTimes = function() {
            var spinner = document.getElementById('spinner');
            if (spinner) spinner.style.display = 'flex';

            var doctorId = selectedDoctorInput.value;
            var date = selectedDateInput.value;
            var timesContainer = document.getElementById('available-times-container');
            var selectedTimeInput = document.getElementById('selected-time');
            timesContainer.innerHTML = '';
            selectedTimeInput.value = '';

            if (!doctorId || !date) {
                timesContainer.innerHTML = '<span class="text-muted">Select a doctor and date first</span>';
                if (spinner) spinner.style.display = 'none';
                return;
            }
            fetch('get_available_times.php?doctor_id=' + encodeURIComponent(doctorId) + '&date=' + encodeURIComponent(date))
                .then(response => response.json())
                .then(data => {
                    const allTimes = data.all || [];
                    const availableTimes = data.available || [];
                    if (allTimes.length === 0) {
                        timesContainer.innerHTML = '<span class="text-danger">No time slots configured for this doctor.</span>';
                        return;
                    }
                    allTimes.forEach(function(time) {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'btn btn-sm rounded-0 mb-1 ' +
                            (availableTimes.includes(time) ? 'btn-outline-primary' : 'btn-outline-secondary text-decoration-line-through');
                        btn.textContent = to12HourFormat(time); // <-- Use the new function here
                        btn.disabled = !availableTimes.includes(time);
                        btn.style.minWidth = '90px';
                        btn.onclick = function() {
                            Array.from(timesContainer.children).forEach(b => b.classList.remove('active', 'btn-primary'));
                            btn.classList.add('active', 'btn-primary');
                            selectedTimeInput.value = time;
                        };
                        timesContainer.appendChild(btn);
                    });
                })
                .catch(() => {
                    timesContainer.innerHTML = '<span class="text-danger">Error loading times</span>';
                })
                .finally(() => {
                    if (spinner) spinner.style.display = 'none';
                });
        }

        // Fetch booked and holiday dates
        fetch('get_booked_and_holidays.php')
            .then(res => res.json())
            .then(data => {
                // Wait for calendar to render
                setTimeout(() => {
                    document.querySelectorAll('.fc-daygrid-day').forEach(cell => {
                        const date = cell.getAttribute('data-date');
                        if (data.booked && data.booked.includes(date)) cell.classList.add('booked-date');
                        if (data.holidays && data.holidays.includes(date)) cell.classList.add('holiday-date');
                    });
                }, 100);
            });
    });
</script>