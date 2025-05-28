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

if (isset($_POST['change_pass'])) {
    try {
        $current_pass = $_POST['current_pass'];
        $new_pass = $_POST['new_pass'];
        $confirm_pass = $_POST['confirm_pass'];
        
        // Validate password match
        if ($new_pass !== $confirm_pass) {
            throw new Exception("New passwords do not match.");
        }

        // Get user's current password
        $stmt = $con->prepare("SELECT password FROM patienttb WHERE patient_acc_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("User not found.");
        }

        $user = $result->fetch_assoc();
        
        // Verify current password
        if (!password_verify($current_pass, $user['password'])) {
            throw new Exception("Current password is incorrect.");
        }

        // Update password
        $hashed_new_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        $update_stmt = $con->prepare("UPDATE patienttb SET password = ? WHERE patient_acc_id = ?");
        $update_stmt->bind_param("si", $hashed_new_pass, $_SESSION['user_id']);
        
        if ($update_stmt->execute()) {
            $_SESSION['toast'] = [
                'show' => true,
                'message' => 'Password changed successfully!',
                'success' => true
            ];
        } else {
            throw new Exception("Error updating password.");
        }
        
        header("Location: profile.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['toast'] = [
            'show' => true,
            'message' => $e->getMessage(),
            'success' => false
        ];
    }
}
?>

<div class="container-fluid pt-4 px-4">
    <div class="d-flex justify-content-between mb-3">
        <h5 class="fw-light">
            <a href="../patient/profile.php"><span class="text-muted">Profile</span></a>
            <span class="text-dark"> / Change Password</span>
        </h5>
        <a href="../patient/profile.php" class="btn btn-outline-secondary btn-sm rounded-0">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="bg-light rounded p-4 shadow-sm">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" 
                      class="needs-validation" novalidate>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Current Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" 
                                   name="current_pass" required>
                            <button class="btn btn-outline-secondary toggle-password" 
                                    type="button">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" 
                                   name="new_pass" required 
                                   pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$">
                            <button class="btn btn-outline-secondary toggle-password" 
                                    type="button">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted">
                            Password must be at least 8 characters long and contain uppercase, 
                            lowercase, and numbers
                        </small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" 
                                   name="confirm_pass" required>
                            <button class="btn btn-outline-secondary toggle-password" 
                                    type="button">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" name="change_pass" 
                                class="btn btn-primary rounded-0">
                            Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include "../includes/script.php";
?>

<script>
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
