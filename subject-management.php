<?php
session_start();
require_once "config/db-connect.php";
require_once "models/Subject.php";
require_once "models/ClassModel.php";

// Protect the dashboard: only logged-in users
if(!isset($_SESSION['id'])){
   header("Location: index.php");
   exit();
}

// Create instances
$subjectModel = new Subject();
$classModel = new ClassModel();

//Get all subjects
$subjects = $subjectModel->getAllSubjects($pdo);
//Get all classes
$classes = $classModel->getAllClasses($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="assets/images/logo.jpg" type="image/png">
    <meta charset="UTF-8">
    <title>Subject Management - Nexis School Admin</title>
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
            <h1 class="page-title">Subject Management</h1>
            <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#createSubjectModal">
                <i class="bi bi-plus-circle"></i> Create Subject
            </button>
        </div>

        <!-- Alert Message -->
        <?php include 'components/AlertMessage.php'; ?>

        <!-- Subject Table -->
        <div class="table-container">
            <h3 class="mb-4">Subject List</h3>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Subject Name</th>
                            <th>Class Name</th>
                            <th>Class Division</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($subjects as $subject): ?>
                            <tr>
                                <td><?= htmlspecialchars($subject['subject_name']) ?></td>
                                <td><?= htmlspecialchars($subject['class_name']) ?></td>
                                <td>
                                    <?php if(!empty($subject['class_division'])): ?>
                                        <span class="badge bg-info"><?= htmlspecialchars($subject['class_division']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary-custom me-1" data-bs-toggle="modal" data-bs-target="#viewModal<?= $subject['id'] ?>">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                    <button class="btn btn-sm btn-secondary-custom me-1" data-bs-toggle="modal" data-bs-target="#editModal<?= $subject['id'] ?>">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $subject['id'] ?>)">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>

                            <!-- VIEW SUBJECT MODAL -->
                            <div class="modal fade" id="viewModal<?= $subject['id'] ?>" tabindex="-1" aria-labelledby="viewModalLabel<?= $subject['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewModalLabel<?= $subject['id'] ?>">Subject Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <strong>Subject Name:</strong>
                                                    <p><?= htmlspecialchars($subject['subject_name']) ?></p>
                                                </div>
                                                <div class="col-12">
                                                    <strong>Class Name:</strong>
                                                    <p><?= htmlspecialchars($subject['class_name']) ?></p>
                                                </div>
                                                <div class="col-12">
                                                    <strong>Class Division:</strong>
                                                    <p>
                                                        <?php if(!empty($subject['class_division'])): ?>
                                                            <span class="badge bg-info"><?= htmlspecialchars($subject['class_division']) ?></span>
                                                        <?php else: ?>
                                                            <span class="text-muted">N/A</span>
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- EDIT SUBJECT MODAL -->
                            <div class="modal fade" id="editModal<?= $subject['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $subject['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel<?= $subject['id'] ?>">Edit Subject</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="processes/add-edit-delete-subject-process.php" method="POST">
                                            <input type="hidden" name="action" value="edit">
                                            <input type="hidden" name="subject_id" value="<?= $subject['id'] ?>">
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="edit_subject_name<?= $subject['id'] ?>" class="form-label">Subject Name</label>
                                                    <input type="text" id="edit_subject_name<?= $subject['id'] ?>" name="subject_name" class="form-control" value="<?= htmlspecialchars($subject['subject_name']) ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="edit_class_name<?= $subject['id'] ?>" class="form-label">Class Name</label>
                                                    <select id="edit_class_name<?= $subject['id'] ?>" name="class_name" class="form-select" required>
                                                        <option value="" disabled>Select Class</option>
                                                        <?php foreach($classes as $class): ?>
                                                            <option value="<?= htmlspecialchars($class['class_name']) ?>" <?= $subject['class_name'] === $class['class_name'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($class['class_name']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="edit_class_division<?= $subject['id'] ?>" class="form-label">Class Division (Optional)</label>
                                                    <select id="edit_class_division<?= $subject['id'] ?>" name="class_division" class="form-select">
                                                        <option value="">None</option>
                                                        <option value="Science" <?= $subject['class_division'] === 'Science' ? 'selected' : '' ?>>Science</option>
                                                        <option value="Art" <?= $subject['class_division'] === 'Art' ? 'selected' : '' ?>>Art</option>
                                                        <option value="Commercial" <?= $subject['class_division'] === 'Commercial' ? 'selected' : '' ?>>Commercial</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary-custom">
                                                    <i class="bi bi-save"></i> Update Subject
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

    <!-- CREATE SUBJECT MODAL -->
    <div class="modal fade" id="createSubjectModal" tabindex="-1" aria-labelledby="createSubjectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createSubjectModalLabel">Create Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="processes/add-edit-delete-subject-process.php" method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="subject_name" class="form-label">Subject Name</label>
                            <input type="text" id="subject_name" name="subject_name" class="form-control" placeholder="e.g., Mathematics" required>
                        </div>

                        <div class="mb-3">
                            <label for="class_name" class="form-label">Class Name</label>
                            <select id="class_name" name="class_name" class="form-select" required>
                                <option value="" disabled selected>Select Class</option>
                                <?php foreach($classes as $class): ?>
                                    <option value="<?= htmlspecialchars($class['class_name']) ?>">
                                        <?= htmlspecialchars($class['class_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="class_division" class="form-label">Class Division (Optional)</label>
                            <select id="class_division" name="class_division" class="form-select">
                                <option value="" selected>None</option>
                                <option value="Science">Science</option>
                                <option value="Art">Art</option>
                                <option value="Commercial">Commercial</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-save"></i> Save Subject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Form (Hidden) -->
    <form id="deleteForm" action="processes/add-edit-delete-subject-process.php" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="subject_id" id="deleteSubjectId">
    </form>

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

        // Delete confirmation
        function confirmDelete(subjectId) {
            if(confirm('Are you sure you want to delete this subject? This action cannot be undone.')) {
                document.getElementById('deleteSubjectId').value = subjectId;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>
</html>
