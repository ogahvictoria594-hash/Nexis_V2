<?php
session_start();
require_once "config/db-connect.php";
require_once "models/ClassModel.php";

// Protect the dashboard: only logged-in users
if(!isset($_SESSION['id'])){
   header("Location: index.php");
   exit();
}

// Create instances
$classModel = new ClassModel();

//Get all classes
$classes = $classModel->getAllClasses($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="assets/images/logo.jpg" type="image/png">
    <meta charset="UTF-8">
    <title>Class Management - Nexis School Admin</title>
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
            <h1 class="page-title">Class Management</h1>
            <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#createClassModal">
                <i class="bi bi-plus-circle"></i> Create Class
            </button>
        </div>

        <!-- Alert Message -->
        <?php include 'components/AlertMessage.php'; ?>

        <!-- Class Table -->
        <div class="table-container">
            <h3 class="mb-4">Class List</h3>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Class Branch</th>
                            <th>Class Name</th>
                            <th>Class Arms</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($classes as $class): ?>
                            <tr>
                                <td><?= htmlspecialchars($class['class_branch']) ?></td>
                                <td><?= htmlspecialchars($class['class_name']) ?></td>
                                <td>
                                    <?php 
                                    $arms = !empty($class['class_arm']) ? explode(',', $class['class_arm']) : [];
                                    foreach($arms as $arm): 
                                    ?>
                                        <span class="badge bg-secondary me-1"><?= htmlspecialchars(trim($arm)) ?></span>
                                    <?php endforeach; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary-custom me-1" data-bs-toggle="modal" data-bs-target="#viewModal<?= $class['id'] ?>">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                    <button class="btn btn-sm btn-secondary-custom me-1" data-bs-toggle="modal" data-bs-target="#editModal<?= $class['id'] ?>">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $class['id'] ?>)">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>

                            <!-- VIEW CLASS MODAL -->
                            <div class="modal fade" id="viewModal<?= $class['id'] ?>" tabindex="-1" aria-labelledby="viewModalLabel<?= $class['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewModalLabel<?= $class['id'] ?>">Class Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <strong>Class Branch:</strong>
                                                    <p><?= htmlspecialchars($class['class_branch']) ?></p>
                                                </div>
                                                <div class="col-12">
                                                    <strong>Class Name:</strong>
                                                    <p><?= htmlspecialchars($class['class_name']) ?></p>
                                                </div>
                                                <div class="col-12">
                                                    <strong>Class Arms:</strong>
                                                    <p>
                                                        <?php 
                                                        $arms = !empty($class['class_arm']) ? explode(',', $class['class_arm']) : [];
                                                        foreach($arms as $arm): 
                                                        ?>
                                                            <span class="badge bg-secondary me-1"><?= htmlspecialchars(trim($arm)) ?></span>
                                                        <?php endforeach; ?>
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

                            <!-- EDIT CLASS MODAL -->
                            <div class="modal fade" id="editModal<?= $class['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $class['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel<?= $class['id'] ?>">Edit Class</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="processes/add-edit-delete-class-process.php" method="POST">
                                            <input type="hidden" name="action" value="edit">
                                            <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="edit_class_branch<?= $class['id'] ?>" class="form-label">Class Branch</label>
                                                    <select id="edit_class_branch<?= $class['id'] ?>" name="class_branch" class="form-select" required>
                                                        <option value="" disabled>Select Branch</option>
                                                        <option value="Nursery" <?= $class['class_branch'] === 'Nursery' ? 'selected' : '' ?>>Nursery</option>
                                                        <option value="Primary" <?= $class['class_branch'] === 'Primary' ? 'selected' : '' ?>>Primary</option>
                                                        <option value="Secondary" <?= $class['class_branch'] === 'Secondary' ? 'selected' : '' ?>>Secondary</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="edit_class_name<?= $class['id'] ?>" class="form-label">Class Name</label>
                                                    <input type="text" id="edit_class_name<?= $class['id'] ?>" name="class_name" class="form-control" value="<?= htmlspecialchars($class['class_name']) ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Class Arms</label>
                                                    <div class="d-flex flex-wrap gap-3">
                                                        <?php 
                                                        $selectedArms = !empty($class['class_arm']) ? explode(',', $class['class_arm']) : [];
                                                        $selectedArms = array_map('trim', $selectedArms);
                                                        $allArms = ['A', 'B', 'C', 'D', 'E'];
                                                        foreach($allArms as $arm): 
                                                        ?>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="class_arm[]" value="<?= $arm ?>" id="edit_arm_<?= $arm ?>_<?= $class['id'] ?>" <?= in_array($arm, $selectedArms) ? 'checked' : '' ?>>
                                                                <label class="form-check-label" for="edit_arm_<?= $arm ?>_<?= $class['id'] ?>">
                                                                    <?= $arm ?>
                                                                </label>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary-custom">
                                                    <i class="bi bi-save"></i> Update Class
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

    <!-- CREATE CLASS MODAL -->
    <div class="modal fade" id="createClassModal" tabindex="-1" aria-labelledby="createClassModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createClassModalLabel">Create Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="processes/add-edit-delete-class-process.php" method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="class_branch" class="form-label">Class Branch</label>
                            <select id="class_branch" name="class_branch" class="form-select" required>
                                <option value="" disabled selected>Select Branch</option>
                                <option value="Nursery">Nursery</option>
                                <option value="Primary">Primary</option>
                                <option value="Secondary">Secondary</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="class_name" class="form-label">Class Name</label>
                            <input type="text" id="class_name" name="class_name" class="form-control" placeholder="e.g., Primary 1" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Class Arms</label>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="class_arm[]" value="A" id="arm_A">
                                    <label class="form-check-label" for="arm_A">A</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="class_arm[]" value="B" id="arm_B">
                                    <label class="form-check-label" for="arm_B">B</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="class_arm[]" value="C" id="arm_C">
                                    <label class="form-check-label" for="arm_C">C</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="class_arm[]" value="D" id="arm_D">
                                    <label class="form-check-label" for="arm_D">D</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="class_arm[]" value="E" id="arm_E">
                                    <label class="form-check-label" for="arm_E">E</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-save"></i> Save Class
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Form (Hidden) -->
    <form id="deleteForm" action="processes/add-edit-delete-class-process.php" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="class_id" id="deleteClassId">
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
        function confirmDelete(classId) {
            if(confirm('Are you sure you want to delete this class? This action cannot be undone.')) {
                document.getElementById('deleteClassId').value = classId;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>
</html>
