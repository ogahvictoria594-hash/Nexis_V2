<?php

class Fee {

    // Get total amount paid
    function getTotalAmountPaid($pdo) {
        $sql = "SELECT SUM(amount_paid) AS totalAmountPaid FROM fee_records";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $totalAmountPaid = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)$totalAmountPaid['totalAmountPaid'];
    }

    // Create Fee Structure
    function createFeeStructure($term, $class_name, $division, $gender, $fee_amount, $pdo) {
        $sql = "INSERT INTO fee_structure (term, class_name, division, gender, fee_amount) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$term, $class_name, $division, $gender, $fee_amount]);
        return $stmt->rowCount() > 0;
    }

    // Check if Fee Structure exists
    function feeStructureExists($term, $class_name, $division, $gender, $pdo) {
        $sql = "SELECT COUNT(*) FROM fee_structure WHERE term = ? AND class_name = ? AND division = ? AND gender = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$term, $class_name, $division, $gender]);
        return $stmt->fetchColumn() > 0;
    }

    // Edit Fee Structure by ID
    function editFeeStructureById($id, $term, $class_name, $division, $gender, $fee_amount, $pdo) {
        $sql = "UPDATE fee_structure SET term = ?, class_name = ?, division = ?, gender = ?, fee_amount = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$term, $class_name, $division, $gender, $fee_amount, $id]);
        return $stmt->rowCount() > 0;
    }

    public function updateFeeStructure($id, $term, $class_name, $division, $gender, $amount, $pdo)
{
    $sql = "
        UPDATE fee_structure
        SET term = ?, 
            class_name = ?, 
            division = ?, 
            gender = ?, 
            fee_amount = ?
        WHERE id = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $term,
        $class_name,
        $division,
        $gender,
        $amount,
        $id
    ]);
}

    public function deleteFeeStructure($id, $pdo)
{
    $sql = "DELETE FROM fee_structure WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
}

    // Get all Fee Structures
    function getAllFeeStructures($pdo) {
        $sql = "SELECT * FROM fee_structure ORDER BY id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get fee structure by term and class
    function getFeeStructureByTermAndClass($term, $class_name, $pdo) {
        $sql = "SELECT * FROM fee_structure WHERE term = ? AND class_name = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$term, $class_name]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Create Fee Record
    function createFeeRecord($id, $session, $term, $class_name, $amount_paid, $date_of_payment, $discount, $payment_method, $bank, $pdo) {
        $sql = "INSERT INTO fee_records (id, session, term, class_name, amount_paid, date_of_payment, discount, payment_method, bank)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id, $session, $term, $class_name, $amount_paid, $date_of_payment, $discount, $payment_method, $bank]);
    }

    // Get all fee records
    function getAllFeeRecords($pdo) {
        $sql = "SELECT * FROM fee_records ORDER BY id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Edit Fee Record by ID
    function editFeeRecordById($id, $session, $term, $class_name, $amount_paid, $date_of_payment, $discount, $payment_method, $bank, $pdo) {
        $sql = "UPDATE fee_records SET id =?, session = ?, term = ?, class_name = ?, amount_paid = ?, date_of_payment = ?, discount = ?, payment_method = ?, bank = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$session, $term, $class_name, $amount_paid, $date_of_payment, $discount, $payment_method, $bank_name, $id]);
    }

    // Delete fee record by ID
    function deleteFeeRecordById($id, $pdo) {
        $sql = "DELETE FROM fee_records WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    // Get fee records by filter
    function getFeeRecordsByFilter($filters, $pdo) {
        $sql = "SELECT * FROM fee_records WHERE 1=1";
        $params = [];

        foreach($filters as $key => $value) {
            if (!empty($value)) {
                $sql .= " AND $key = ?";
                $params[] = $value;
            }
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFilteredFeeRecords($filters, $pdo)
{
    $sql = "SELECT fr.*, fs.fee_amount 
            FROM fee_records fr 
            LEFT JOIN fee_structure fs 
            ON fr.term = fs.term 
            AND fr.class_name = fs.class_name";
    $params = [];

    if (!empty($filters['session'])) {
        $sql .= " AND fr.session = ?";
        $params[] = $filters['session'];
    }

    if (!empty($filters['term'])) {
        $sql .= " AND fr.term = ?";
        $params[] = $filters['term'];
    }

    if (!empty($filters['class_name'])) {
        $sql .= " AND fr.class_name = ?";
        $params[] = $filters['class_name'];
    }

    if (!empty($filters['payment_method'])) {
        $sql .= " AND fr.payment_method = ?";
        $params[] = $filters['payment_method'];
    }

    if (!empty($filters['date_of_payment'])) {
        $sql .= " AND fr.date_of_payment = ?";
        $params[] = $filters['date_of_payment'];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Compute status dynamically
    if (!empty($filters['status'])) {
        $records = array_filter($records, function($row) use ($filters) {
            $status = $row['amount_paid'] >= $row['fee_amount'] ? 'Paid' : 'Owing';
            return $status === $filters['status'];
        });
    }

    return $records;
}

public function getFilteredFeeRecordsWithStatus($filters, $pdo)
{
    $sql = "
        SELECT fr.*, fs.fee_amount
        FROM fee_records fr
        LEFT JOIN fee_structure fs
            ON fr.term = fs.term
            AND fr.class_name = fs.class_name
            AND fr.division = fs.division
        WHERE 1=1
    ";

    $params = [];

    // Regular filters
    if (!empty($filters['session'])) {
        $sql .= " AND fr.session = ?";
        $params[] = $filters['session'];
    }

    if (!empty($filters['term'])) {
        $sql .= " AND fr.term = ?";
        $params[] = $filters['term'];
    }

    if (!empty($filters['class_name'])) {
        $sql .= " AND fr.class_name = ?";
        $params[] = $filters['class_name'];
    }

    if (!empty($filters['payment_method'])) {
        $sql .= " AND fr.payment_method = ?";
        $params[] = $filters['payment_method'];
    }

    if (!empty($filters['date_of_payment'])) {
        $sql .= " AND fr.date_of_payment = ?";
        $params[] = $filters['date_of_payment'];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Filter by status if requested
    if (!empty($filters['status'])) {
        $status = strtolower($filters['status']);
        $records = array_filter($records, function($r) use ($status) {
            $totalPayable = $r['fee_amount'] - $r['discount'];
            if ($status === 'paid') {
                return $r['amount_paid'] >= $totalPayable;
            } elseif ($status === 'owing') {
                return $r['amount_paid'] < $totalPayable;
            }
            return true;
        });
    }

    return $records;
}
}
?>