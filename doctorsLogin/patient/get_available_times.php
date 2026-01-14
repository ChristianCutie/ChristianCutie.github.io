<?php
require_once "../connection/globalConnection.php";

$con = connection();

$doctor_id = $_GET['doctor_id'] ?? '';
$date = $_GET['date'] ?? '';

$all_times = [];
for ($h = 8; $h <= 17; $h++) { // 8:00 to 17:00 (5pm)
    $all_times[] = sprintf("%02d:00", $h);
    $all_times[] = sprintf("%02d:30", $h);
}

$available = $all_times;
if ($doctor_id && $date) {
    $stmt = $con->prepare("SELECT appt_time FROM appointmenttb WHERE doctor_app_acc_id=? AND appt_date=? AND status IN ('Pending','Approved')");
    $stmt->bind_param("ss", $doctor_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $booked = [];
    while ($row = $result->fetch_assoc()) {
        $booked[] = $row['appt_time'];
    }
    $available = array_values(array_diff($all_times, $booked));
}

header('Content-Type: application/json');
echo json_encode([
    "all" => $all_times,
    "available" => $available
]);
?>