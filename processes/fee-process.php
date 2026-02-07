<?php
session_start();
require_once "../config/db-connect.php";
require_once "../models/Fee.php";

$feeModel = new Fee();

// ===== FEE SETUP HANDLER =====
if(isset($_POST['setupAction'])) {
    $id        = $_POST['id'] ?? null;
    $term      = $_POST['term'] ?? '';
    $class     = $_POST['class_name'] ?? '';
    $div       = $_POST['division'] ?? '';
    $gender    = $_POST['gender'] ?? '';
    $amount    = $_POST['fee_amount'] ?? 0;

    if($id) {
        $stmt = $pdo->prepare("UPDATE fee_structure SET term=:term, class_name=:class, division=:div, gender=:gender, fee_amount=:amount WHERE id=:id");
        $stmt->execute([
            ':term'=>$term,
            ':class'=>$class,
            ':div'=>$div,
            ':gender'=>$gender,
            ':amount'=>$amount,
            ':id'=>$id
        ]);
        $_SESSION['msg'] = "Fee Structure Updated!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO fee_structure (term, class_name, division, gender, fee_amount) VALUES (:term, :class, :div, :gender, :amount)");
        $stmt->execute([
            ':term'=>$term,
            ':class'=>$class,
            ':div'=>$div,
            ':gender'=>$gender,
            ':amount'=>$amount
        ]);
        $_SESSION['msg'] = "Fee Structure Added!";
    }
    header("Location: ../fee-management.php");
    exit();
}

// ===== FEE RECORD HANDLER =====
if(isset($_POST['recordAction'])) {
    $id            = $_POST['id'] ?? null;
    $session       = $_POST['session'] ?? '';
    $term          = $_POST['term'] ?? '';
    $class_name    = $_POST['class_name'] ?? '';
    $fee_amount    = $_POST['fee_amount'] ?? 0;
    $amount_paid   = $_POST['amount_paid'] ?? 0;
    $discount      = $_POST['discount'] ?? 0;
    $date_payment  = $_POST['date_of_payment'] ?? date('Y-m-d');
    $method        = $_POST['payment_method'] ?? '';
    $bank          = $_POST['bank'] ?? null;

    if($id) {
        $stmt = $pdo->prepare("UPDATE fee_records SET session=:session, term=:term, class_name=:class_name, fee_amount=:fee_amount, amount_paid=:amount_paid, discount=:discount, date_of_payment=:date_payment, payment_method=:method, bank=:bank WHERE id=:id");
        $stmt->execute([
            ':session'=>$session,
            ':term'=>$term,
            ':class_name'=>$class_name,
            ':fee_amount'=>$fee_amount,
            ':amount_paid'=>$amount_paid,
            ':discount'=>$discount,
            ':date_payment'=>$date_payment,
            ':method'=>$method,
            ':bank'=>$bank,
            ':id'=>$id
        ]);
        $_SESSION['msg'] = "Fee Record Updated!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO fee_records (session, term, class_name, fee_amount, amount_paid, discount, date_of_payment, payment_method, bank) VALUES (:session, :term, :class_name, :fee_amount, :amount_paid, :discount, :date_payment, :method, :bank)");
        $stmt->execute([
            ':session'=>$session,
            ':term'=>$term,
            ':class_name'=>$class_name,
            ':fee_amount'=>$fee_amount,
            ':amount_paid'=>$amount_paid,
            ':discount'=>$discount,
            ':date_payment'=>$date_payment,
            ':method'=>$method,
            ':bank'=>$bank
        ]);
        $_SESSION['msg'] = "Fee Record Added!";
    }
    header("Location: ../fee-management.php");
    exit();
}
?>