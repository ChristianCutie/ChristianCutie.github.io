<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/chart/chart.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="../lib/tempusdominus/js/moment.min.js"></script>
    <script src="../lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="../lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>





    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Hide spinner after page loads
    setTimeout(function() {
        const spinner = document.getElementById('spinner');
        if (spinner) {
            spinner.classList.remove('show');
            // Optional: Remove from DOM completely after fade out
            setTimeout(function() {
                spinner.style.display = 'none';
            }, 300);
        }
    }, 1000); // Adjust timing as needed (1000ms = 1 second)
});

// Function to show spinner (for AJAX calls or form submissions)
function showSpinner() {
    const spinner = document.getElementById('spinner');
    if (spinner) {
        spinner.style.display = 'flex';
        spinner.classList.add('show');
    }
}

// Function to hide spinner
function hideSpinner() {
    const spinner = document.getElementById('spinner');
    if (spinner) {
        spinner.classList.remove('show');
        setTimeout(function() {
            spinner.style.display = 'none';
        }, 300);
    }
}
</script>