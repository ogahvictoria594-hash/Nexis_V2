<?php
session_start();
require 'config/db-connect.php';
require 'models/Result.php';

$resultObj = new Result($pdo);

// Fetch subjects dynamically from subjects table
$allSubjects = $resultObj->getAllSubjects();

// ✅ Fetch classes dynamically from database
$classStmt = $pdo->prepare("SELECT DISTINCT class_name FROM students ORDER BY class_name ASC");
$classStmt->execute();
$allClasses = $classStmt->fetchAll(PDO::FETCH_COLUMN);

// ✅ Fetch arms dynamically from database
$armStmt = $pdo->prepare("SELECT DISTINCT class_arm FROM students ORDER BY class_arm ASC");
$armStmt->execute();
$allArms = $armStmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch students dynamically
$students = $pdo->query("SELECT * FROM students ORDER BY surname ASC")->fetchAll(PDO::FETCH_ASSOC);

// Handle saving scores
if(isset($_POST['save_scores'])){
    $session = $_POST['session'] ?? '';
    $term = $_POST['term'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $class = $_POST['class'] ?? '';
    $arm = $_POST['arm'] ?? '';

    if(!empty($_POST['scores'])){
        foreach($_POST['scores'] as $reg => $scoreData){
            foreach($scoreData as $assessment_type => $score){
                if($score !== ''){
                    $resultObj->addScore([
                        'registration_number' => $reg,
                        'session'             => $session,
                        'term'                => $term,
                        'subject_name'        => $subject,
                        'assessment_type'     => $assessment_type,
                        'score'               => $score
                    ]);
                }
            }
        }
    }
}

// Handle filtering (main table)
$filteredScores = [];
if(isset($_POST['filter_scores'])){
    $session = $_POST['filter_session'] ?? '';
    $term = $_POST['filter_term'] ?? '';
    $class = $_POST['filter_class'] ?? '';
    $arm = $_POST['filter_arm'] ?? '';
    $subject = $_POST['filter_subject'] ?? '';

    $filteredScores = $resultObj->getScoresByFilter($session, $term, $class, $arm, $subject);
} else {
    $filteredScores = $resultObj->getAllScores();
}

// Handle delete
if(isset($_POST['delete_id'])){
    $id = $_POST['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM scores WHERE id=?");
    $stmt->execute([$id]);
    header("Location: result-management.php");
    exit;
}

// Handle edit (fetch data for modal)
$edit_values = [];
if(isset($_POST['edit_id'])){
    $edit_id = $_POST['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM scores WHERE id=?");
    $stmt->execute([$edit_id]);
    $edit_values = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Results Management</title>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="assets/css/result-styles.css">
</head>
<body>

<div class="dashboard">
    <?php include "components/SideBar.php"; ?>

    <div class="main-content">
        <div class="topbar">
            <div class="topbar-info" style="display:flex; align-items:center; gap:15px;">
                <button class="hamburger-btn" onclick="toggleSidebar()" style="display:none; border:none; background:none; font-size:20px; cursor:pointer;">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1>Results Management</h1>
                    <p style="color:#666; font-size:14px;">Welcome, Administrator! Manage student results and grading systems</p>
                </div>
            </div>
            <div class="topbar-actions" style="display:flex; gap:10px;">
                <button class="login-button" onclick="document.getElementById('resultsModal').style.display='flex'">
                    <i class="fas fa-plus-circle"></i> Input Scores
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="stat-card" style="display:block; margin-bottom:25px;">
            <h3 style="margin-bottom:15px;"><i class="fas fa-filter"></i> Filter / Search Scores</h3>
            <form method="post" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap:15px; align-items:end;">
                <select class="form-input" name="filter_session">
                    <option value="">Session</option>
                    <option>2024/2025</option>
                    <option>2025/2026</option>
                </select>
                <select class="form-input" name="filter_term">
                    <option value="">Term</option>
                    <option>1st Term</option>
                    <option>2nd Term</option>
                    <option>3rd Term</option>
                </select>
                <select class="form-input" name="filter_class">
                    <option value="">Class</option>
                    <?php foreach($allClasses as $cls): ?>
                        <option><?= $cls ?></option>
                    <?php endforeach; ?>
                </select>
                <select class="form-input" name="filter_arm">
                    <option value="">Arm</option>
                    <?php foreach($allArms as $arm): ?>
                        <option><?= $arm ?></option>
                    <?php endforeach; ?>
                </select>
                <select class="form-input" name="filter_subject">
                    <option value="">Subject</option>
                    <?php foreach($allSubjects as $sub): ?>
                        <option><?= $sub ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="filter_scores" class="login-button" style="background:#6c757d;color:white;box-shadow:none;">
                    <i class="fas fa-filter"></i> Apply Filter
                </button>
            </form>
        </div>

        <!-- Scores Table -->
        <div class="stat-card" style="display:block; padding:0; overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:14px; min-width:800px;">
                <thead style="background: var(--navy-blue); color:white;">
                    <tr>
                        <th style="padding:15px;">#</th>
                        <th style="padding:15px; text-align:left;">Student Name</th>
                        <th style="padding:15px; text-align:left;">Class</th>
                        <th style="padding:15px; text-align:left;">Assessment Type</th>
                        <th style="padding:15px; text-align:center;">Score</th>
                        <th style="padding:15px; text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($filteredScores as $i => $row): ?>
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:12px; text-align:center;"><?= $i+1 ?></td>
                        <td style="padding:12px;"><?= $row['surname'] ?> <?= $row['first_name'] ?> <?= $row['other_names'] ?></td>
                        <td style="padding:12px;"><?= $row['class_name'] ?> <?= $row['class_arm'] ?></td>
                        <td style="padding:12px;"><?= $row['assessment_type'] ?></td>
                        <td style="padding:12px; text-align:center;"><?= $row['score'] ?></td>
                        <td style="padding:12px; text-align:center;">
                            <div style="display:flex; gap:6px; justify-content:center;">
                                <form method="post" style="margin:0;">
                                    <input type="hidden" name="edit_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="edit_score" class="login-button" style="padding:6px 10px;">Edit</button>
                                </form>
                                <form method="post" style="margin:0;">
                                    <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="delete_score" class="login-button" style="padding:6px 10px;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Input Scores Modal -->
<div class="modal-overlay" id="resultsModal" style="display:none;">
    <div class="modal-card" style="background:white; border-radius:14px; padding:25px; max-width:900px; width:95%;">
        <div style="display:flex; justify-content:space-between; margin-bottom:20px;">
            <h2 style="color: var(--navy-blue); font-size:20px;">Input Student Scores</h2>
            <button onclick="document.getElementById('resultsModal').style.display='none'" style="border:none; background:none; font-size:24px; cursor:pointer;">&times;</button>
        </div>

        <form method="post">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(150px,1fr)); gap:15px; margin-bottom:20px;">
                <select class="form-input" name="session" required>
                    <option value="">Select Session</option>
                    <option>2024/2025</option>
                    <option>2025/2026</option>
                </select>
                <select class="form-input" name="term" required>
                    <option value="">Select Term</option>
                    <option>1st Term</option>
                    <option>2nd Term</option>
                    <option>3rd Term</option>
                </select>

                <select class="form-input" name="class" required>
                    <option value="">Select Class</option>
                    <?php foreach($allClasses as $cls): ?>
                        <option><?= $cls ?></option>
                    <?php endforeach; ?>
                </select>
                <select class="form-input" name="arm" required>
                    <option value="">Select Arm</option>
                    <?php foreach($allArms as $arm): ?>
                        <option><?= $arm ?></option>
                    <?php endforeach; ?>
                </select>

               <select class="form-input" name="subject" required>
                    <option value="">Select Subject</option>
                    <?php foreach($allSubjects as $sub): ?>
                        <option><?= $sub ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="overflow-x:auto; max-height:400px;">
                <table style="width:100%; border-collapse:collapse; font-size:14px; min-width:800px;">
                    <thead style="background: var(--navy-blue); color:white;">
                        <tr>
                            <th style="padding:12px;">#</th>
                            <th style="padding:12px; text-align:left;">Student Name</th>
                            <th style="padding:12px;">1st CA</th>
                            <th style="padding:12px;">2nd CA</th>
                            <th style="padding:12px;">3rd CA</th>
                            <th style="padding:12px;">Exam</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($students as $i => $stu): ?>
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:12px; text-align:center;"><?= $i+1 ?></td>
                            <td style="padding:12px;"><?= $stu['surname'] ?> <?= $stu['first_name'] ?> <?= $stu['other_names'] ?></td>
                            <?php $types = ['1st CA','2nd CA','3rd CA','Exam'];
                            foreach($types as $t): ?>
                            <td style="padding:12px; text-align:center;">
                                <input type="number" name="scores[<?= $stu['registration_number'] ?>][<?= $t ?>]" class="form-input" style="width:60px;">
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="margin-top:20px; display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="login-button" style="background:#6c757d;color:white;box-shadow:none;" onclick="document.getElementById('resultsModal').style.display='none'">Cancel</button>
                <button type="submit" name="save_scores" class="login-button">Save Scores</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>

<script>
function toggleSidebar(){
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('active');
}
document.addEventListener('click', function(event){
    const sidebar = document.getElementById('sidebar');
    const btn = document.querySelector('.hamburger-btn');
    if(window.innerWidth <= 900){
        if(!sidebar.contains(event.target) && !btn.contains(event.target)){
            sidebar.classList.remove('active');
        }
    }
});
</script>