<?php
include "../includes/header.php";
include "../includes/sidebar-patient.php";
include_once(__DIR__ . "/../connection/globalConnection.php");

$con = connection();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Fetch appointment history
$patient_id = $_SESSION['user_id'];
$sql = "SELECT a.*,
CONCAT(d.First_Name, ' ', d.Last_Name) as doctor_name, d.specialization 
        FROM appointmenttb a 
        JOIN doctortb d ON a.doctor_app_acc_id = d.doctor_id 
        WHERE a.patient_app_acc_id = ? 
        ORDER BY a.appt_date DESC";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<!-- Main Content -->
<div class="container-fluid pt-4 px-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-light rounded p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-2">Appointment History</h3>
                        <p class="text-muted">View all your past and upcoming appointments</p>
                    </div>
                    <a href="../patient/add.php" class="btn btn-primary btn-sm rounded-0">
                        <i class="fa fa-plus me-2"></i>New Appointment
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointment Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-light rounded p-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="completed">Completed</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" id="dateFilter">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-secondary btn-sm rounded-0" onclick="resetFilters()">
                            Reset Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointments List -->
    <div class="row">
        <div class="col-12">
            <div class="bg-light rounded p-4">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Doctor</th>
                                <th>Specialization</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($row['appt_date'])); ?></td>
                                    <td><?php echo date('h:i A', strtotime($row['appt_time'])); ?></td>
                                    <td>Dr. <?php echo htmlspecialchars($row['doctor_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['specialization']); ?></td>
                                    <td>
                                        <?php
                                        $status = strtolower($row['status']);
                                        $badge_class = match ($status) {
                                            'completed' => 'bg-success',
                                            'upcoming' => 'bg-primary',
                                            'cancelled' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?php echo $badge_class; ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="float-end">
                                            <button class="btn btn-sm btn-info me-2"
                                                onclick="viewDetails(<?php echo $row['appt_id']; ?>)">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <?php if ($row['status'] == 'upcoming'): ?>
                                                <button class="btn btn-sm btn-warning me-2"
                                                    onclick="reschedule(<?php echo $row['appt_id']; ?>)">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="cancelAppointment(<?php echo $row['appt_id']; ?>)">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>

                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="appointmentDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Appointment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="appointmentDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<?php include "../includes/script.php"; ?>

<script>
    
    function viewDetails(appointmentId) {
        // Add your logic to show appointment details in modal
        $('#appointmentDetailsModal').modal('show');
    }

    function reschedule(appointmentId) {
        // Add your rescheduling logic
    }

    function cancelAppointment(appointmentId) {
        if (confirm('Are you sure you want to cancel this appointment?')) {
            // Add your cancellation logic
        }
    }

    function resetFilters() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('dateFilter').value = '';
        // Add logic to reset the table
    }

    // Add filter functionality
    document.getElementById('statusFilter').addEventListener('change', function() {
        // Add filter logic
    });

    document.getElementById('dateFilter').addEventListener('change', function() {
        // Add filter logic
    });
</script>