<?php
session_start();
require_once "config/db-connect.php";
require_once "models/Fee.php";

$feeModel = new Fee();

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

    $reg_no      = $_POST['reg_no'];
    $session     = $_POST['session'];
    $term        = $_POST['term'];
    $class_name  = $_POST['class_name'];
    $fee_amount  = $_POST['fee_amount'];
    $amount_paid = $_POST['amount_paid'];
    $discount    = $_POST['discount'];
    $date        = $_POST['date'];
    $method      = $_POST['method'];
    $bank        = $_POST['bank'];

    $feeModel->createFeeRecord(
        $reg_no,
        $session,
        $term,
        $class_name,
        $fee_amount,
        $amount_paid,
        $date,
        $discount,
        $method,
        $bank,
        $pdo
    );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Fee Management | Nexis School Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
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

<main class="main-content" id="mainContent">

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
    <div class="table-responsive">
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
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- =========================
     FILTER FORM
========================= -->
<form class="filter-scroll-container">
    <select class="form-select-custom">
        <option value="">Session</option>
        <option>2024/2025</option>
        <option>2025/2026</option>
    </select>

    <select class="form-select-custom">
        <option value="">Term</option>
        <option>1st Term</option>
        <option>2nd Term</option>
        <option>3rd Term</option>
    </select>

    <select class="form-select-custom">
        <option value="">Class</option>
        <option>Primary 1</option>
        <option>JSS1</option>
        <option>JSS2</option>
        <option>JSS3</option>
        <option>SS1</option>
        <option>SS2</option>
        <option>Nursery 1</option>
        <option>Nursery 2</option>
    </select>

    <select class="form-select-custom">
        <option value="">Method</option>
        <option>Cash</option>
        <option>Transfer</option>
        <option>Bank Deposit</option>
    </select>

    <select class="form-select-custom">
        <option value="">Status</option>
        <option>Paid</option>
        <option>Partial</option>
    </select>

    <input type="date" class="form-control-custom">

    <button type="submit" class="btn-primary-custom">Filter</button>
    <button type="reset" class="btn btn-sm btn-light">Reset</button>
</form>

<!-- =========================
     FEE RECORD TABLE
========================= -->
<div class="table-responsive mt-4">
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
        <tbody></tbody>
    </table>
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
                        <option>Primary 1</option>
                        <option>JSS1</option>
                        <option>JSS2</option>
                        <option>JSS3</option>
                        <option>SS1</option>
                        <option>SS2</option>
                        <option>Nursery 1</option>
                        <option>Nursery 2</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Division / Arm</label>
                    <select name="division" class="form-select-custom w-100" required>
                        <option value="">Select Arm</option>
                        <option>A, B, C</option>
                        <option>A, B, C, D</option>
                        <option>A, B, C, D, E</option>
                        <option>A, B</option>
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
                            <option>1st Term</option>
                            <option>2nd Term</option>
                            <option>3rd Term</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Class</label>
                        <select name="class_name" class="form-select-custom w-100" required>
                            <option>Primary 1</option>
                            <option>JSS1</option>
                            <option>JSS2</option>
                            <option>JSS3</option>
                            <option>SS1</option>
                            <option>SS2</option>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
var setupModal = new bootstrap.Modal(document.getElementById('setupModal'));
document.getElementById('openSetupBtn').addEventListener('click', function () {
    setupModal.show();
});

var recordModal = new bootstrap.Modal(document.getElementById('recordModal'));
document.getElementById('openRecordBtn').addEventListener('click', function () {
    recordModal.show();
});
</script>

</main>
</body>
</html>