<?php
session_start();
require_once "config/db-connect.php";
require_once "models/Student.php";
require_once "models/ClassModel.php";

// Protect the dashboard: only logged-in users
if(!isset($_SESSION['id'])){
   header("Location: index.php");
   exit();
}

// Create instances
$studentModel = new Student();
$classModel = new ClassModel();
//Get all students
$students = $studentModel->getAllStudents($pdo);
//Get all classes
$classes = $classModel->getAllClasses($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="assets/images/logo.jpg" type="image/png">
    <meta charset="UTF-8">
    <title>Student Management - Nexis School Admin</title>
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
            <h1 class="page-title">Student Management</h1>
            <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#createStudentModal">
                <i class="bi bi-plus-circle"></i> Create Student
            </button>
        </div>

        <!-- Alert Message -->
        <?php include 'components/AlertMessage.php'; ?>

        <!-- Student Table -->
        <div class="table-container">
            <h3 class="mb-4">Student List</h3>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Reg Number</th>
                            <th>Student Name</th>
                            <th>Class</th>
                            <th>Arm</th>
                            <th>Parent Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($students as $student): ?>
                            <tr>
                                <td><?= htmlspecialchars($student['registration_number']) ?></td>
                                <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['surname'] . ' ' . $student['other_names']) ?></td>
                                <td><?= htmlspecialchars($student['class_name']) ?></td>
                                <td><?= htmlspecialchars($student['class_arm']) ?></td>
                                <td><?= htmlspecialchars($student['parent_phone']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary-custom me-1" data-bs-toggle="modal" data-bs-target="#viewModal<?= $student['id'] ?>">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                    <button class="btn btn-sm btn-secondary-custom me-1" data-bs-toggle="modal" data-bs-target="#editModal<?= $student['id'] ?>">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>

                            <!-- VIEW STUDENT MODAL -->
                            <div class="modal fade" id="viewModal<?= $student['id'] ?>" tabindex="-1" aria-labelledby="viewModalLabel<?= $student['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewModalLabel<?= $student['id'] ?>">Student Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <strong>First Name:</strong>
                                                    <p><?= htmlspecialchars($student['first_name']) ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Surname:</strong>
                                                    <p><?= htmlspecialchars($student['surname']) ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Other Names:</strong>
                                                    <p><?= htmlspecialchars($student['other_names']) ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Gender:</strong>
                                                    <p><?= htmlspecialchars($student['gender']) ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Date of Birth:</strong>
                                                    <p><?= htmlspecialchars($student['date_of_birth']) ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Class:</strong>
                                                    <p><?= htmlspecialchars($student['class_name']) ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Class Arm:</strong>
                                                    <p><?= htmlspecialchars($student['class_arm']) ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Parent's Phone:</strong>
                                                    <p><?= htmlspecialchars($student['parent_phone']) ?></p>
                                                </div>
                                                <div class="col-12">
                                                    <strong>Residential Address:</strong>
                                                    <p><?= htmlspecialchars($student['residential_address']) ?></p>
                                                </div>
                                                <div class="col-12">
                                                    <strong>Parent's Name:</strong>
                                                    <p><?= htmlspecialchars($student['parent_name']) ?></p>
                                                </div>
                                                <?php if(!empty($student['profile_picture'])): ?>
                                                <div class="col-12">
                                                    <strong>Profile Picture:</strong><br>
                                                    <img src="<?= htmlspecialchars($student['profile_picture']) ?>" 
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

                            <!-- EDIT STUDENT MODAL -->
                            <div class="modal fade" id="editModal<?= $student['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $student['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel<?= $student['id'] ?>">Edit Student</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="processes/edit-student-process.php" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="student_id" value="<?= $student['id'] ?>">
                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="edit_first_name<?= $student['id'] ?>" class="form-label">First Name</label>
                                                        <input type="text" class="form-control" id="edit_first_name<?= $student['id'] ?>" name="first_name" value="<?= htmlspecialchars($student['first_name']) ?>" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="edit_surname<?= $student['id'] ?>" class="form-label">Surname</label>
                                                        <input type="text" class="form-control" id="edit_surname<?= $student['id'] ?>" name="surname" value="<?= htmlspecialchars($student['surname']) ?>" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="edit_other_names<?= $student['id'] ?>" class="form-label">Other Names</label>
                                                        <input type="text" class="form-control" id="edit_other_names<?= $student['id'] ?>" name="other_names" value="<?= htmlspecialchars($student['other_names']) ?>">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="edit_gender<?= $student['id'] ?>" class="form-label">Gender</label>
                                                        <select id="edit_gender<?= $student['id'] ?>" name="gender" class="form-select" required>
                                                            <option value="Male" <?= $student['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                                                            <option value="Female" <?= $student['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="edit_date_of_birth<?= $student['id'] ?>" class="form-label">Date of Birth</label>
                                                        <input type="date" id="edit_date_of_birth<?= $student['id'] ?>" name="date_of_birth" class="form-control" value="<?= htmlspecialchars($student['date_of_birth']) ?>">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="edit_class_name<?= $student['id'] ?>" class="form-label">Class</label>
                                                        <select id="edit_class_name<?= $student['id'] ?>" name="class_name" class="form-select" required>
                                                            <option value="JSS 1" <?= $student['class_name'] === 'JSS 1' ? 'selected' : '' ?>>JSS 1</option>
                                                            <option value="JSS 2" <?= $student['class_name'] === 'JSS 2' ? 'selected' : '' ?>>JSS 2</option>
                                                            <option value="SS 1" <?= $student['class_name'] === 'SS 1' ? 'selected' : '' ?>>SS 1</option>
                                                            <option value="SS 2" <?= $student['class_name'] === 'SS 2' ? 'selected' : '' ?>>SS 2</option>
                                                            <option value="SS 3" <?= $student['class_name'] === 'SS 3' ? 'selected' : '' ?>>SS 3</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="edit_class_arm<?= $student['id'] ?>" class="form-label">Class Arm</label>
                                                        <select id="edit_class_arm<?= $student['id'] ?>" name="class_arm" class="form-select" required>
                                                            <option value="A" <?= $student['class_arm'] === 'A' ? 'selected' : '' ?>>A</option>
                                                            <option value="B" <?= $student['class_arm'] === 'B' ? 'selected' : '' ?>>B</option>
                                                            <option value="C" <?= $student['class_arm'] === 'C' ? 'selected' : '' ?>>C</option>
                                                            <option value="D" <?= $student['class_arm'] === 'D' ? 'selected' : '' ?>>D</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="edit_parent_phone<?= $student['id'] ?>" class="form-label">Parent's Phone Number</label>
                                                        <input type="tel" id="edit_parent_phone<?= $student['id'] ?>" name="parent_phone" class="form-control" value="<?= htmlspecialchars($student['parent_phone']) ?>">
                                                    </div>

                                                    <div class="col-12">
                                                        <label for="edit_residential_address<?= $student['id'] ?>" class="form-label">Residential Address</label>
                                                        <input type="text" id="edit_residential_address<?= $student['id'] ?>" name="residential_address" class="form-control" value="<?= htmlspecialchars($student['residential_address']) ?>">
                                                    </div>

                                                    <div class="col-12">
                                                        <label for="edit_parent_name<?= $student['id'] ?>" class="form-label">Parent's Name</label>
                                                        <input type="text" id="edit_parent_name<?= $student['id'] ?>" name="parent_name" class="form-control" value="<?= htmlspecialchars($student['parent_name']) ?>">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="edit_profile_picture<?= $student['id'] ?>" class="form-label">Profile Picture</label>
                                                        <input type="file" id="edit_profile_picture<?= $student['id'] ?>" name="profile_picture" class="form-control">
                                                        <?php if(!empty($student['profile_picture'])): ?>
                                                            <small class="text-muted">Current: <?= basename($student['profile_picture']) ?></small>
                                                        <?php endif; ?>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="edit_password<?= $student['id'] ?>" class="form-label">Password</label>
                                                        <input type="password" id="edit_password<?= $student['id'] ?>" name="password" class="form-control" placeholder="Enter new password" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary-custom">
                                                    <i class="bi bi-save"></i> Update Student
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

    <!-- CREATE STUDENT MODAL -->
    <div class="modal fade" id="createStudentModal" tabindex="-1" aria-labelledby="createStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createStudentModalLabel">Create Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="processes/add-student-process.php" method="POST" enctype="multipart/form-data">
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
                                <label for="class_name" class="form-label">Class</label>
                                <select id="class_name" name="class_name" class="form-select" required>
                                    <option value="" selected disabled>Select Class</option>
                                    <?php foreach($classes as $class): ?>
                                        <option value="<?= htmlspecialchars($class['class_name']) ?>"><?= htmlspecialchars($class['class_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="class_arm" class="form-label">Class Arm</label>
                                <select id="class_arm" name="class_arm" class="form-select" required>
                                    <option value="">Select Class Arm</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="parent_phone" class="form-label">Parent's Phone Number</label>
                                <input type="tel" id="parent_phone" name="parent_phone" class="form-control" placeholder="Parent's Phone Number">
                            </div>

                            <div class="col-12">
                                <label for="residential_address" class="form-label">Residential Address</label>
                                <input type="text" id="residential_address" name="residential_address" class="form-control" placeholder="Residential Address">
                            </div>

                            <div class="col-12">
                                <label for="parent_name" class="form-label">Parent's Name</label>
                                <input type="text" id="parent_name" name="parent_name" class="form-control" placeholder="Parent's Name">
                            </div>

                            <div class="col-md-6">
                                <label for="profile_picture" class="form-label">Profile Picture</label>
                                <input type="file" id="profile_picture" name="profile_picture" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-save"></i> Save Student
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
    </script>
</body>
</html>
