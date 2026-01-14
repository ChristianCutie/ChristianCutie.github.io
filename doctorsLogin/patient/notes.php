<?php 
ob_start();
session_start();
include "../includes/header.php";
include "../includes/sidebar-patient.php";
require_once "../connection/globalConnection.php";

$con = connection();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM patienttb WHERE patient_acc_id = '$user_id'";
$result = $con->query($sql);
if ($result === false) {
    die("Error fetching user data: " . htmlspecialchars($con->error));
}
$user_name = $result->fetch_assoc();

// Fetch doctor's notes with doctor information
$notes_sql = "SELECT n.*, d.First_Name as doc_fname, d.Last_Name as doc_lname, d.Specialization 
              FROM doctor_notestb n 
              LEFT JOIN doctortb d ON n.doctor_id = d.doctor_acc_id 
              WHERE n.patient_id = ? 
              ORDER BY n.created_at DESC";
$stmt = $con->prepare($notes_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notes_result = $stmt->get_result();
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
            <h4 class="mb-1">Doctor's Notes</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Doctor's Notes</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-primary btn-sm rounded-0" onclick="window.print()">
            <i class="fa-solid fa-print me-2"></i>Print Notes
        </button>
    </div>

    <!-- Notes Section -->
    <div class="row">
        <div class="col-12">
            <?php if ($notes_result->num_rows > 0): ?>
                <?php while ($note = $notes_result->fetch_assoc()): ?>
                    <div class="card border-0 shadow-sm rounded-0 mb-3">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-1">
                                        Dr. <?= htmlspecialchars($note['doc_fname'] . ' ' . $note['doc_lname']) ?>
                                        <span class="badge bg-primary-subtle text-primary ms-2">
                                            <?= htmlspecialchars($note['Specialization']) ?>
                                        </span>
                                    </h6>
                                    <p class="text-muted small mb-0">
                                        <i class="fa-regular fa-clock me-1"></i>
                                        <?= date('F d, Y h:i A', strtotime($note['created_at'])) ?>
                                    </p>
                                </div>
                                <span class="badge bg-info-subtle text-info px-3">
                                    <?= htmlspecialchars($note['visit_type']) ?>
                                </span>
                            </div>

                            <div class="border-top pt-3">
                                <div class="row g-4">
                                    <!-- Diagnosis -->
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-2">Diagnosis</h6>
                                        <p class="mb-0"><?= nl2br(htmlspecialchars($note['diagnosis'])) ?></p>
                                    </div>

                                    <!-- Prescription -->
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-2">Prescription</h6>
                                        <?php if (!empty($note['prescription'])): ?>
                                            <ul class="list-unstyled mb-0">
                                                <?php foreach (explode("\n", $note['prescription']) as $med): ?>
                                                    <li class="mb-2">
                                                        <i class="fa-solid fa-prescription-bottle-medical text-primary me-2"></i>
                                                        <?= htmlspecialchars($med) ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p class="text-muted mb-0">No prescriptions given</p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Additional Notes -->
                                    <?php if (!empty($note['additional_notes'])): ?>
                                        <div class="col-12">
                                            <h6 class="fw-bold mb-2">Additional Notes</h6>
                                            <p class="mb-0"><?= nl2br(htmlspecialchars($note['additional_notes'])) ?></p>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Attachments -->
                                    <?php if (!empty($note['attachments'])): ?>
                                        <div class="col-12">
                                            <h6 class="fw-bold mb-2">Attachments</h6>
                                            <div class="d-flex flex-wrap gap-2">
                                                <?php foreach (explode(',', $note['attachments']) as $attachment): ?>
                                                    <a href="../uploads/<?= htmlspecialchars($attachment) ?>" 
                                                       class="btn btn-light btn-sm" 
                                                       target="_blank">
                                                        <i class="fa-solid fa-paperclip me-2"></i>
                                                        <?= htmlspecialchars(basename($attachment)) ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fa-solid fa-file-excel fa-2x text-secondary mb-3"></i>
                    <h6 class="text-muted">No doctor's notes found</h6>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include "../includes/script.php";?>

<style>
@media print {
    .sidebar,
    .navbar,
    .btn-print {
        display: none !important;
    }
    .card {
        border: 1px solid #dee2e6 !important;
        break-inside: avoid;
    }
    .badge {
        border: 1px solid #dee2e6 !important;
    }
}
</style>
