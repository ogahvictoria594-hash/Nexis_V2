<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="assets/images/logo.jpg" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexis - Admin Portal Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
    <div class="container-fluid">
        <div class="row">
            <!-- Left Panel -->
            <div class="col-lg-6 left-panel">
                <div>
                    <div class="logo-section">
                        <img src="assets/images/logo.jpg" alt="Nexis Logo" class="logo-image" width="50">
                        <div class="logo-text">Nexis</div>
                    </div>

                    <div class="welcome-section">
                        <h1>Welcome to Admin Portal</h1>
                        <p>Access the comprehensive school management system. Manage student records, academic results, communication, and administrative operations securely.</p>

                        <div class="feature-box">
                            <h3>Unified Platform</h3>
                            <p>Integrated solution for academic management, communication, and administrative tasks in one secure environment.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel -->
            <div class="col-lg-6 right-panel">
                <div class="login-form-container">
                    <h2>Secure Access Portal</h2>
                    <p class="subtitle">Enter your credentials to access the platform</p>

                    <!-- Alert Message -->
                    <?php include 'components/AlertMessage.php'; ?>

                    <form id="loginForm" method="POST" action="processes/login-processes.php">
                        <!-- User Role Selection -->
                        <div class="mb-3">
                            <label for="user_role" class="form-label">
                                <i class="bi bi-people-fill"></i>
                                User Role Selection
                            </label>
                            <select class="form-select" id="user_role" name="user_role" required>
                                <option value="" selected disabled>Select your role</option>
                                <option value="super_admin">Administrator</option>
                                <option value="head_of_school">Head of School</option>
                                <option value="subject_teacher">Subject Teacher</option>
                                <option value="class_teacher">Class Teacher</option>
                            </select>
                        </div>

                        <!-- Staff Number -->
                        <div class="mb-3">
                            <label for="staff_number" class="form-label">
                                <i class="bi bi-person-badge"></i>
                                Staff Number
                            </label>
                            <input type="text" class="form-control" id="staff_number" name="staff_number" placeholder="Enter staff number" required>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="bi bi-key-fill"></i>
                                Password
                            </label>
                            <div class="password-wrapper">
                                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••••••" required>
                                <button type="button" class="password-toggle" onclick="togglePassword()">
                                    <i class="bi bi-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">
                                    Keep me logged in
                                </label>
                            </div>
                            <a href="#" class="forgot-password">Forgot Password?</a>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" name="login" class="btn btn-access">
                            Access Platform
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>