<?php
session_start();
require_once "config/db-connect.php";
require_once "models/Student.php";
require_once "models/Staff.php";
require_once "models/Fee.php";

// Protect the dashboard: only logged-in users
if(!isset($_SESSION['id'])){
   header("Location: index.php");
   exit();
}

// Create instances
$studentModel = new Student();
$staffModel = new Staff();
$feeModel = new Fee();


// Fetch dynamic stats safely
$totalStudents = $studentModel->getTotalStudentsCount($pdo);

$totalStaff = $staffModel->getTotalStaffCount($pdo);

$totalFees = $feeModel->getTotalAmountPaid($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="assets/images/logo.jpg" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Nexis School Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dashboard-page">
    <!-- Side Bar -->
    <?php include_once "components/SideBar.php"; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">Dashboard Overview</h1>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4" style="max-width: 1400px;">
            <!-- Total Students Card -->
            <div class="col-lg-4 col-md-6">
                <div class="stat-card">
                    <div class="stat-card-content">
                        <div class="stat-info">
                            <h3>Total Students</h3>
                            <p class="stat-value"><?php echo htmlspecialchars($totalStudents); ?></p>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Staff Card -->
            <div class="col-lg-4 col-md-6">
                <div class="stat-card">
                    <div class="stat-card-content">
                        <div class="stat-info">
                            <h3>Total Staff</h3>
                            <p class="stat-value"><?php echo htmlspecialchars($totalStaff); ?></p>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-person-workspace"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Fees Card -->
            <div class="col-lg-4 col-md-6">
                <div class="stat-card">
                    <div class="stat-card-content">
                        <div class="stat-info">
                            <h3>Total Fees</h3>
                            <p class="stat-value">₦<?php echo htmlspecialchars(number_format($totalFees, 2)); ?></p>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-wallet-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Content Section (Optional) -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="table-container">
                    <h3 class="mb-4">Recent Activities</h3>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                        <p class="mt-3">No recent activities to display</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.mobile-menu-toggle');
            
            if (window.innerWidth <= 991) {
                if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Handle active nav link
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                // Remove active class from all links
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                // Add active class to clicked link
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>