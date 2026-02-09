<?php
session_start();
require_once "config/db-connect.php";
require_once "models/Fee.php";

$feeModel = new Fee();

/*
|--------------------------------------------------------------------------
| CAPTURE FILTER INPUTS
|--------------------------------------------------------------------------
*/
$filters = [
    'session'          => $_GET['session'] ?? '',
    'term'             => $_GET['term'] ?? '',
    'class_name'       => $_GET['class_name'] ?? '',
    'payment_method'   => $_GET['payment_method'] ?? '',
    'date_of_payment'  => $_GET['date_of_payment'] ?? ''
];

$feeStructures = $feeModel->getAllFeeStructures($pdo);

if (!empty(array_filter($filters))) {
    $feeRecords = $feeModel->getFeeRecordsByFilter($filters, $pdo);
} else {
    $feeRecords = $feeModel->getAllFeeRecords($pdo);
}

/*
|--------------------------------------------------------------------------
| DELETE ACTIONS
|--------------------------------------------------------------------------
*/
if (isset($_GET['delete_structure'])) {
    $feeModel->deleteFeeStructure($_GET['delete_structure'], $pdo);
    header("Location: fee-management.php");
    exit;
}

if (isset($_GET['delete_record'])) {
    $feeModel->deleteFeeRecordById($_GET['delete_record'], $pdo);
    header("Location: fee-management.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| UPDATE FEE STRUCTURE
|--------------------------------------------------------------------------
*/
if (isset($_POST['update_fee_structure'])) {
    $feeModel->updateFeeStructure(
        $_POST['structure_id'],
        $_POST['term'],
        $_POST['class_name'],
        $_POST['division'],
        $_POST['gender'],
        $_POST['amount'],
        $pdo
    );
    header("Location: fee-management.php");
exit;
}

/*
|--------------------------------------------------------------------------
| UPDATE FEE RECORD
|--------------------------------------------------------------------------
*/
if (isset($_POST['update_fee_record'])) {
    $feeModel->updateFeeRecord(
        $_POST['record_id'],
        $_POST['amount_paid'],
        $_POST['discount'],
        $_POST['method'],
        $_POST['bank'],
        $pdo
    );
    
}

/*
|--------------------------------------------------------------------------
| HANDLE FEE STRUCTURE SAVE
|--------------------------------------------------------------------------
*/
if (isset($_POST['save_fee_structure'])) {

    $term       = $_POST['term'];
    $class_name = $_POST['class_name'];
    $division   = $_POST['division'];
    $gender     = $_POST['gender'];
    $amount     = $_POST['amount'];

    if (!$feeModel->feeStructureExists($term, $class_name, $division, $gender, $pdo)) {
        $feeModel->createFeeStructure($term, $class_name, $division, $gender, $amount, $pdo);
    }
}

/*
|--------------------------------------------------------------------------
| HANDLE FEE RECORD SAVE
|--------------------------------------------------------------------------
*/
if (isset($_POST['save_fee_record'])) {

    $feeModel->createFeeRecord(
        $_POST['reg_no'],
        $_POST['session'],
        $_POST['term'],
        $_POST['class_name'],
        $_POST['fee_amount'],
        $_POST['amount_paid'],
        $_POST['date'],
        $_POST['discount'],
        $_POST['method'],
        $_POST['bank'],
        $pdo
    );
    header("Location: fee-management.php");
exit;
}

$classes   = $pdo->query("SELECT DISTINCT class_name FROM classes")->fetchAll(PDO::FETCH_ASSOC);
$divisions = $pdo->query("SELECT DISTINCT class_arm FROM classes")->fetchAll(PDO::FETCH_ASSOC);

$feeStructures = $feeModel->getAllFeeStructures($pdo);

if (!empty(array_filter($filters))) {
    $feeRecords = $feeModel->getFilteredFeeRecords($filters, $pdo);
} else {
    $feeRecords = $feeModel->getAllFeeRecords($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Fee Management | Nexis School Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="assets/css/style.css">

<style>
.filter-scroll-container {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding-bottom: 10px;
    margin-bottom: 20px;
    border-bottom: 1px solid #eee;
}
.filter-scroll-container .form-select-custom,
.filter-scroll-container .form-control-custom {
    min-width: 150px;
    flex: 0 0 auto;
}
.btn-table-action {
    padding: 5px 12px;
    font-size: 11px;
    border-radius: 4px;
    border: none;
    font-weight: 700;
    text-transform: uppercase;
    margin-right: 2px;
}
</style>
</head>

<body class="dashboard-page">
<?php include "components/SideBar.php"; ?>

<main class="main-content">

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Fee Management</h1>
    <div class="d-flex gap-2">
        <button class="btn-primary-custom" id="openSetupBtn">Fee Setup</button>
        <button class="btn-primary-custom" id="openRecordBtn">Fee Record</button>
    </div>
</div>

<!-- =========================
     FEE STRUCTURES TABLE
========================= -->
<div class="table-container mb-4">
<h3 class="mb-3">Fee Structures</h3>
<table class="table">
<thead>
<tr>
<th>Term</th>
<th>Class</th>
<th>Division</th>
<th>Gender</th>
<th>Amount</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php foreach ($feeStructures as $row): ?>
<tr>
<td><?= $row['term'] ?></td>
<td><?= $row['class_name'] ?></td>
<td><?= $row['division'] ?></td>
<td><?= $row['gender'] ?></td>
<td><?= number_format($row['fee_amount']) ?></td>
<td>
<button class="btn btn-sm btn-primary-custom me-1"
onclick='viewData(<?= json_encode($row) ?>,"structure")'>
    <i class="bi bi-eye"></i> View
</button>

<button class="btn btn-sm btn-secondary-custom me-1"
onclick='editStructure(<?= json_encode($row) ?>)'>
    <i class="bi bi-pencil"></i> Edit
</button>

<a class="btn btn-sm btn-danger"
onclick="return confirm('Delete this fee structure?')"
href="?delete_structure=<?= $row['id'] ?>">
    <i class="bi bi-trash"></i> Delete
</a>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

<!-- =========================
     FEE RECORD FILTERS
========================= -->
<form class="filter-scroll-container" method="GET">
    <select name="session" class="form-select-custom">
        <option value="">Select Session</option>
        <option value="2024/2025" <?= ($_GET['session'] ?? '') == '2024/2025' ? 'selected' : '' ?>>2024/2025</option>
        <option value="2025/2026" <?= ($_GET['session'] ?? '') == '2025/2026' ? 'selected' : '' ?>>2025/2026</option>
    </select>

    <select name="term" class="form-select-custom">
        <option value="">Select Term</option>
        <option value="1st Term" <?= ($_GET['term'] ?? '') == '1st Term' ? 'selected' : '' ?>>1st Term</option>
        <option value="2nd Term" <?= ($_GET['term'] ?? '') == '2nd Term' ? 'selected' : '' ?>>2nd Term</option>
        <option value="3rd Term" <?= ($_GET['term'] ?? '') == '3rd Term' ? 'selected' : '' ?>>3rd Term</option>
    </select>

    <select name="class_name" class="form-select-custom">
        <option value="">Select Class</option>
        <?php foreach ($classes as $class): ?>
            <option value="<?= $class['class_name'] ?>" <?= ($_GET['class_name'] ?? '') == $class['class_name'] ? 'selected' : '' ?>>
                <?= $class['class_name'] ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="payment_method" class="form-select-custom">
        <option value="">Select Payment Method</option>
        <option value="Cash" <?= ($_GET['payment_method'] ?? '') == 'Cash' ? 'selected' : '' ?>>Cash</option>
        <option value="Transfer" <?= ($_GET['payment_method'] ?? '') == 'Transfer' ? 'selected' : '' ?>>Transfer</option>
        <option value="Bank Deposit" <?= ($_GET['payment_method'] ?? '') == 'Bank Deposit' ? 'selected' : '' ?>>Bank Deposit</option>
    </select>

    <input type="date" name="date_of_payment" value="<?= $_GET['date_of_payment'] ?? '' ?>" class="form-control-custom">

    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
</form>

<!-- =========================
     FEE RECORD TABLE
========================= -->
<table class="table">
<thead>
<tr>
<th>Reg No</th>
<th>Class</th>
<th>Amount Paid</th>
<th>Discount</th>
<th>Date</th>
<th>Method</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php foreach ($feeRecords as $row): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['class_name'] ?></td>
<td><?= number_format($row['amount_paid']) ?></td>
<td><?= number_format($row['discount']) ?></td>
<td><?= $row['date_of_payment'] ?></td>
<td><?= $row['payment_method'] ?></td>
<td>
<button class="btn btn-sm btn-primary-custom me-1"
onclick='viewData(<?= json_encode($row) ?>,"structure")'>
    <i class="bi bi-eye"></i> View
</button>

<button class="btn btn-sm btn-secondary-custom me-1"
onclick='editStructure(<?= json_encode($row) ?>)'>
    <i class="bi bi-pencil"></i> Edit
</button>

<a class="btn btn-sm btn-danger"
onclick="return confirm('Delete this fee structure?')"
href="?delete_structure=<?= $row['id'] ?>">
    <i class="bi bi-trash"></i> Delete
</a>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<!-- =========================
     VIEW MODAL
========================= -->
<div class="modal fade" id="viewModal">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content stat-card p-4" id="viewModalBody"></div>
</div>
</div>

 <!-- =========================
     FEE SETUP MODAL
========================= -->
<div class="modal fade" id="setupModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content stat-card border-0">
            <h3 style="color: var(--gold); margin-bottom:15px;">Fee Setup</h3>

            <form method="POST">
                <div class="mb-3">
                    <label>Term</label>
                    <select name="term" class="form-select-custom w-100" required>
                        <option value="">Select Term</option>
                        <option>1st Term</option>
                        <option>2nd Term</option>
                        <option>3rd Term</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Class</label>
                    <select name="class_name" class="form-select-custom w-100" required>
                        <option value="">Select Class</option>
                        <?php foreach ($classes as $class): ?>
                            <?php if (!empty($class['class_name'])): ?>
                                <option value="<?= htmlspecialchars($class['class_name'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($class['class_name'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Division / Arm</label>
                    <select name="division" class="form-select-custom w-100" required>
                        <option value="">Select Arm</option>
                        <?php foreach ($divisions as $division): ?>
                            <?php if (!empty($division['class_arm'])): ?>
                                <option value="<?= htmlspecialchars($division['class_arm'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($division['class_arm'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Gender</label>
                    <select name="gender" class="form-select-custom w-100" required>
                        <option value="">Select Gender</option>
                        <option>Both</option>
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label>Amount Payable</label>
                    <input type="number" name="amount" class="form-control-custom w-100" required>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" name="save_fee_structure" class="btn-primary-custom flex-grow-1">Save Structure</button>
                    <button type="button" class="btn btn-outline-dark flex-grow-1" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================
     FEE RECORD MODAL
========================= -->
<div class="modal fade" id="recordModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content stat-card border-0">
            <h3 class="mb-4" style="color: var(--gold);">Fee Record</h3>

            <form method="POST">
                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label>Reg No</label>
                        <input type="text" name="reg_no" class="form-control-custom w-100" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Session</label>
                        <select name="session" class="form-select-custom w-100" required>
                            <option>2024/2025</option>
                            <option>2025/2026</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Term</label>
                        <select name="term" class="form-select-custom w-100" required>
                           <option value="">Select Term</option>
                            <option value="1st Term">1st Term</option>
                            <option value="2nd Term">2nd Term</option>
                            <option value="3rd Term">3rd Term</option>
                                </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Class</label>
                        <select name="class_name" class="form-select-custom w-100" required>
                            <option value="">Select Class</option>
                            <?php foreach ($classes as $class): ?>
                                <?php if (!empty($class['class_name'])): ?>
                                    <option value="<?= htmlspecialchars($class['class_name'], ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($class['class_name'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Division / Arm</label>
                        <select name="division" class="form-select-custom w-100" required>
                            <option value="">Select Arm</option>
                            <?php foreach ($divisions as $division): ?>
    <?php if (!empty($division['class_arm'])): ?>
        <option value="<?= htmlspecialchars($division['class_arm'], ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars($division['class_arm'], ENT_QUOTES, 'UTF-8') ?>
        </option>
    <?php endif; ?>
<?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                       <select name="fee_structure" class="form-select-custom w-100" required>
                        <option value="">Select Fee Structure</option>

                        <?php foreach ($feeStructures as $structure): ?>
                            <option value="<?= $structure['id'] ?>">
                                <?= $structure['term'] ?> |
                                <?= $structure['class_name'] ?> |
                                <?= $structure['division'] ?> |
                                <?= number_format($structure['fee_amount']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                                        </div>

                    <input type="hidden" name="fee_amount" value="0">

                    <div class="col-md-6 mb-3">
                        <label>Amount Paid</label>
                        <input type="number" name="amount_paid" class="form-control-custom w-100" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Discount</label>
                        <input type="number" name="discount" class="form-control-custom w-100">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Date</label>
                        <input type="date" name="date" class="form-control-custom w-100" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Method</label>
                        <select name="method" class="form-select-custom w-100" required>
                            <option>Cash</option>
                            <option>Transfer</option>
                            <option>Bank Deposit</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Bank Name</label>
                        <input type="text" name="bank" class="form-control-custom w-100">
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" name="save_fee_record" class="btn-primary-custom flex-grow-1">Save Record</button>
                    <button type="button" class="btn btn-outline-dark flex-grow-1" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
var setupModal  = new bootstrap.Modal(document.getElementById('setupModal'));
var recordModal = new bootstrap.Modal(document.getElementById('recordModal'));
var viewModal   = new bootstrap.Modal(document.getElementById('viewModal'));

// === Connect top buttons to modals ===
document.getElementById('openSetupBtn').addEventListener('click', function() {
    setupModal.show();
});

document.getElementById('openRecordBtn').addEventListener('click', function() {
    recordModal.show();
});

function viewData(data, type) {
    let html = '<h4 class="mb-3">Details</h4>';
    for (const key in data) {
        html += `<p><strong>${key}</strong>: ${data[key]}</p>`;
    }
    document.getElementById('viewModalBody').innerHTML = html;
    viewModal.show();
}

function editStructure(data) {
    document.querySelector('#setupModal [name="term"]').value = data.term;
    document.querySelector('#setupModal [name="class_name"]').value = data.class_name;
    document.querySelector('#setupModal [name="division"]').value = data.division;
    document.querySelector('#setupModal [name="gender"]').value = data.gender;
    document.querySelector('#setupModal [name="amount"]').value = data.amount;

    let idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'structure_id';
    idInput.value = data.id;

    let flag = document.createElement('input');
    flag.type = 'hidden';
    flag.name = 'update_fee_structure';
    flag.value = 1;

    let form = document.querySelector('#setupModal form');
    form.appendChild(idInput);
    form.appendChild(flag);

    setupModal.show();
}

function editRecord(data) {
    document.querySelector('#recordModal [name="reg_no"]').value = data.reg_no;
    document.querySelector('#recordModal [name="session"]').value = data.session;
    document.querySelector('#recordModal [name="term"]').value = data.term;
    document.querySelector('#recordModal [name="class_name"]').value = data.class_name;
    document.querySelector('#recordModal [name="amount_paid"]').value = data.amount_paid;
    document.querySelector('#recordModal [name="discount"]').value = data.discount;
    document.querySelector('#recordModal [name="method"]').value = data.method;
    document.querySelector('#recordModal [name="bank"]').value = data.bank;

    let idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'record_id';
    idInput.value = data.id;

    let flag = document.createElement('input');
    flag.type = 'hidden';
    flag.name = 'update_fee_record';
    flag.value = 1;

    let form = document.querySelector('#recordModal form');
    form.appendChild(idInput);
    form.appendChild(flag);

    recordModal.show();
}
</script>

</body>
</html>






