<?php
session_start();
require_once "config/db-connect.php";
require_once "models/Staff.php";
require_once "models/ClassModel.php";
require_once "models/Subject.php";

// Protect the dashboard: only logged-in users
if(!isset($_SESSION['id'])){
   header("Location: index.php");
   exit();
}

// Create instances
$staffModel = new Staff();
$classModel = new ClassModel();
$subjectModel = new Subject();

//Get all staff
$staffs = $staffModel->getAllStaff($pdo);
//Get all classes
$classes = $classModel->getAllClasses($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="assets/images/logo.jpg" type="image/png">
    <meta charset="UTF-8">
    <title>Staff Management - Nexis School Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dashboard-page">
    <!-- Side Bar -->
    <?php include_once "components/SideBar.php"; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Staff Management</h1>
            <div class="d-flex gap-2">
                <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#assignRoleModal">
                    <i class="bi bi-person-check"></i> Assign Role
                </button>
                <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#createStaffModal">
                    <i class="bi bi-plus-circle"></i> Create Staff
                </button>
            </div>
        </div>

        <!-- Alert Message -->
        <?php include 'components/AlertMessage.php'; ?>

        <!-- Staff Table -->
        <div class="table-container">
            <h3 class="mb-4">Staff List</h3>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Staff Number</th>
                            <th>Staff Name</th>
                            <th>Category</th>
                            <th>Job Description</th>
                            <th>Phone Number</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($staffs as $staff): ?>
                            <tr>
                                <td><?= htmlspecialchars($staff['staff_number']) ?></td>
                                <td><?= htmlspecialchars($staff['first_name'] . ' ' . $staff['surname'] . ' ' . $staff['other_names']) ?></td>
                                <td><?= htmlspecialchars($staff['category']) ?></td>
                                <td><?= htmlspecialchars($staff['job_description']) ?></td>
                                <td><?= htmlspecialchars($staff['phone_number']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary-custom me-1" data-bs-toggle="modal" data-bs-target="#viewModal<?= $staff['id'] ?>">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                    <button class="btn btn-sm btn-secondary-custom me-1" data-bs-toggle="modal" data-bs-target="#editModal<?= $staff['id'] ?>">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>

                            <!-- VIEW STAFF MODAL -->
                            <div class="modal fade" id="viewModal<?= $staff['id'] ?>" tabindex="-1" aria-labelledby="viewModalLabel<?= $staff['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewModalLabel<?= $staff['id'] ?>">Staff Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <strong>First Name:</strong>
                                                    <p><?= htmlspecialchars($staff['first_name']) ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Surname:</strong>
                                                    <p><?= htmlspecialchars($staff['surname']) ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Other Names:</strong>
                                                    <p><?= htmlspecialchars($staff['other_names']) ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Gender:</strong>
                                                    <p><?= htmlspecialchars($staff['gender']) ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Date of Birth:</strong>
                                                    <p><?= htmlspecialchars($staff['date_of_birth']) ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Date of Employment:</strong>
                                                    <p><?= htmlspecialchars($staff['date_of_employment']) ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Category:</strong>
                                                    <p><?= htmlspecialchars($staff['category']) ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Job Description:</strong>
                                                    <p><?= htmlspecialchars($staff['job_description']) ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Phone Number:</strong>
                                                    <p><?= htmlspecialchars($staff['phone_number']) ?></p>
                                                </div>
                                                <div class="col-12">
                                                    <strong>Residential Address:</strong>
                                                    <p><?= htmlspecialchars($staff['residential_address']) ?></p>
                                                </div>
                                                <?php if(!empty($staff['profile_picture'])): ?>
                                                <div class="col-12">
                                                    <strong>Profile Picture:</strong><br>
                                                    <img src="<?= htmlspecialchars($staff['profile_picture']) ?>" 
                                                         alt="Profile" 
                                                         class="img-fluid rounded mt-2"
                                                         style="max-width: 200px;">
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- EDIT STAFF MODAL -->
                            <div class="modal fade" id="editModal<?= $staff['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $staff['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel<?= $staff['id'] ?>">Edit Staff</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="processes/edit-staff-process.php" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="staff_id" value="<?= $staff['id'] ?>">
                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="edit_first_name<?= $staff['id'] ?>" class="form-label">First Name</label>
                                                        <input type="text" class="form-control" id="edit_first_name<?= $staff['id'] ?>" name="first_name" value="<?= htmlspecialchars($staff['first_name']) ?>" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="edit_surname<?= $staff['id'] ?>" class="form-label">Surname</label>
                                                        <input type="text" class="form-control" id="edit_surname<?= $staff['id'] ?>" name="surname" value="<?= htmlspecialchars($staff['surname']) ?>" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="edit_other_names<?= $staff['id'] ?>" class="form-label">Other Names</label>
                                                        <input type="text" class="form-control" id="edit_other_names<?= $staff['id'] ?>" name="other_names" value="<?= htmlspecialchars($staff['other_names']) ?>">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="edit_gender<?= $staff['id'] ?>" class="form-label">Gender</label>
                                                        <select id="edit_gender<?= $staff['id'] ?>" name="gender" class="form-select" required>
                                                            <option value="Male" <?= $staff['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                                                            <option value="Female" <?= $staff['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="edit_date_of_birth<?= $staff['id'] ?>" class="form-label">Date of Birth</label>
                                                        <input type="date" id="edit_date_of_birth<?= $staff['id'] ?>" name="date_of_birth" class="form-control" value="<?= htmlspecialchars($staff['date_of_birth']) ?>">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="edit_date_of_employment<?= $staff['id'] ?>" class="form-label">Date of Employment</label>
                                                        <input type="date" id="edit_date_of_employment<?= $staff['id'] ?>" name="date_of_employment" class="form-control" value="<?= htmlspecialchars($staff['date_of_employment']) ?>">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="edit_category<?= $staff['id'] ?>" class="form-label">Category</label>
                                                        <select id="edit_category<?= $staff['id'] ?>" name="category" class="form-select" required>
                                                            <option value="Teaching" <?= $staff['category'] === 'Teaching' ? 'selected' : '' ?>>Teaching</option>
                                                            <option value="Non-Teaching" <?= $staff['category'] === 'Non-Teaching' ? 'selected' : '' ?>>Non-Teaching</option>
                                                            <option value="Administrative" <?= $staff['category'] === 'Administrative' ? 'selected' : '' ?>>Administrative</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="edit_job_description<?= $staff['id'] ?>" class="form-label">Job Description</label>
                                                        <input type="text" id="edit_job_description<?= $staff['id'] ?>" name="job_description" class="form-control" value="<?= htmlspecialchars($staff['job_description']) ?>" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="edit_phone_number<?= $staff['id'] ?>" class="form-label">Phone Number</label>
                                                        <input type="tel" id="edit_phone_number<?= $staff['id'] ?>" name="phone_number" class="form-control" value="<?= htmlspecialchars($staff['phone_number']) ?>" required>
                                                    </div>

                                                    <div class="col-12">
                                                        <label for="edit_residential_address<?= $staff['id'] ?>" class="form-label">Residential Address</label>
                                                        <input type="text" id="edit_residential_address<?= $staff['id'] ?>" name="residential_address" class="form-control" value="<?= htmlspecialchars($staff['residential_address']) ?>">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="edit_profile_picture<?= $staff['id'] ?>" class="form-label">Profile Picture</label>
                                                        <input type="file" id="edit_profile_picture<?= $staff['id'] ?>" name="profile_picture" class="form-control">
                                                        <?php if(!empty($staff['profile_picture'])): ?>
                                                            <small class="text-muted">Current: <?= basename($staff['profile_picture']) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary-custom">
                                                    <i class="bi bi-save"></i> Update Staff
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- ASSIGN ROLE MODAL -->
    <div class="modal fade" id="assignRoleModal" tabindex="-1" aria-labelledby="assignRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignRoleModalLabel">Assign Role to Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="processes/assign-role-process.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="staff_number" class="form-label">Select Staff</label>
                            <select id="staff_number" name="staff_number" class="form-select" required>
                                <option value="" selected disabled>Select Staff</option>
                                <?php foreach($staffs as $staff): ?>
                                    <option value="<?= htmlspecialchars($staff['staff_number']) ?>">
                                        <?= htmlspecialchars($staff['staff_number'] . ' - ' . $staff['first_name'] . ' ' . $staff['surname']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="user_role" class="form-label">User Role</label>
                            <select id="user_role" name="user_role" class="form-select" required onchange="toggleRoleFields()">
                                <option value="" selected disabled>Select Role</option>
                                <option value="super_admin">Super Admin</option>
                                <option value="head_of_school">Head of School</option>
                                <option value="class_teacher">Class Teacher</option>
                                <option value="subject_teacher">Subject Teacher</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="role_password" class="form-label">Password</label>
                            <input type="password" id="role_password" name="password" class="form-control" placeholder="Enter password" required>
                        </div>

                        <div class="mb-3" id="user_branch_field" style="display: none;">
                            <label for="user_branch" class="form-label">User Branch</label>
                            <select id="user_branch" name="user_branch" class="form-select">
                                <option value="">Select Branch</option>
                                <option value="Nursery">Nursery</option>
                                <option value="Primary">Primary</option>
                                <option value="Secondary">Secondary</option>
                            </select>
                        </div>

                        <div class="mb-3" id="class_name_field" style="display: none;">
                            <label for="role_class_name" class="form-label">Class Name</label>
                            <select id="role_class_name" name="class_name" class="form-select">
                                <option value="">Select Class</option>
                                <?php foreach($classes as $class): ?>
                                    <option value="<?= htmlspecialchars($class['class_name']) ?>"><?= htmlspecialchars($class['class_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3" id="subject_name_field" style="display: none;">
                            <label for="subject_name" class="form-label">Subject Name</label>
                            <select id="subject_name" name="subject_name" class="form-select">
                                <option value="">Select Subject</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-check-circle"></i> Assign Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- CREATE STAFF MODAL -->
    <div class="modal fade" id="createStaffModal" tabindex="-1" aria-labelledby="createStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createStaffModalLabel">Create Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="processes/add-staff-process.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" required>
                            </div>

                            <div class="col-md-6">
                                <label for="surname" class="form-label">Surname</label>
                                <input type="text" class="form-control" id="surname" name="surname" placeholder="Surname" required>
                            </div>

                            <div class="col-md-6">
                                <label for="other_names" class="form-label">Other Names</label>
                                <input type="text" class="form-control" id="other_names" name="other_names" placeholder="Other Names">
                            </div>

                            <div class="col-md-6">
                                <label for="gender" class="form-label">Gender</label>
                                <select id="gender" name="gender" class="form-select" required>
                                    <option value="" selected disabled>Select gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label for="date_of_employment" class="form-label">Date of Employment</label>
                                <input type="date" id="date_of_employment" name="date_of_employment" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label for="category" class="form-label">Category</label>
                                <select id="category" name="category" class="form-select" required>
                                    <option value="" selected disabled>Select Category</option>
                                    <option value="Teaching">Teaching</option>
                                    <option value="Non-Teaching">Non-Teaching</option>
                                    <option value="Administrative">Administrative</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="job_description" class="form-label">Job Description</label>
                                <input type="text" id="job_description" name="job_description" class="form-control" placeholder="Job Description" required>
                            </div>

                            <div class="col-md-6">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="tel" id="phone_number" name="phone_number" class="form-control" placeholder="Phone Number" required>
                            </div>

                            <div class="col-12">
                                <label for="residential_address" class="form-label">Residential Address</label>
                                <input type="text" id="residential_address" name="residential_address" class="form-control" placeholder="Residential Address">
                            </div>

                            <div class="col-md-6">
                                <label for="profile_picture" class="form-label">Profile Picture</label>
                                <input type="file" id="profile_picture" name="profile_picture" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-save"></i> Save Staff
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Toggle role-specific fields based on selected role
        function toggleRoleFields() {
            const userRole = document.getElementById('user_role').value;
            const userBranchField = document.getElementById('user_branch_field');
            const classNameField = document.getElementById('class_name_field');
            const subjectNameField = document.getElementById('subject_name_field');
            
            const userBranchInput = document.getElementById('user_branch');
            const classNameInput = document.getElementById('role_class_name');
            const subjectNameInput = document.getElementById('subject_name');

            // Hide all fields initially
            userBranchField.style.display = 'none';
            classNameField.style.display = 'none';
            subjectNameField.style.display = 'none';
            
            // Remove required attribute from all
            userBranchInput.removeAttribute('required');
            classNameInput.removeAttribute('required');
            subjectNameInput.removeAttribute('required');

            // Clear subject dropdown when role changes
            subjectNameInput.innerHTML = '<option value="">Select Subject</option>';

            // Show fields based on role
            if (userRole === 'super_admin') {
                // Hide all three fields
            } else if (userRole === 'head_of_school') {
                // Show only user_branch
                userBranchField.style.display = 'block';
                userBranchInput.setAttribute('required', 'required');
            } else if (userRole === 'class_teacher') {
                // Show class_name and user_branch
                classNameField.style.display = 'block';
                userBranchField.style.display = 'block';
                classNameInput.setAttribute('required', 'required');
                userBranchInput.setAttribute('required', 'required');
            } else if (userRole === 'subject_teacher') {
                // Show all three fields
                classNameField.style.display = 'block';
                subjectNameField.style.display = 'block';
                userBranchField.style.display = 'block';
                classNameInput.setAttribute('required', 'required');
                subjectNameInput.setAttribute('required', 'required');
                userBranchInput.setAttribute('required', 'required');
            }
        }

        // Load subjects dynamically when class is selected
        document.getElementById('role_class_name').addEventListener('change', function() {
            const className = this.value;
            const subjectSelect = document.getElementById('subject_name');
            
            // Clear existing options
            subjectSelect.innerHTML = '<option value="">Loading...</option>';
            
            if (className) {
                // Fetch subjects for the selected class
                fetch('processes/get-subjects-by-class.php?class_name=' + encodeURIComponent(className))
                    .then(response => response.json())
                    .then(data => {
                        subjectSelect.innerHTML = '<option value="">Select Subject</option>';
                        
                        if (data.success && data.subjects.length > 0) {
                            data.subjects.forEach(subject => {
                                const option = document.createElement('option');
                                option.value = subject.subject_name;
                                option.textContent = subject.subject_name;
                                subjectSelect.appendChild(option);
                            });
                        } else {
                            subjectSelect.innerHTML = '<option value="">No subjects found</option>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching subjects:', error);
                        subjectSelect.innerHTML = '<option value="">Error loading subjects</option>';
                    });
            } else {
                subjectSelect.innerHTML = '<option value="">Select Subject</option>';
            }
        });
    </script>
</body>
</html>
