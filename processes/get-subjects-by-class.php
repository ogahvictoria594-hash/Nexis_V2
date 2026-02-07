<?php
require_once "../config/db-connect.php";
require_once "../models/Subject.php";

// Set header for JSON response
header('Content-Type: application/json');

// Check if class_name is provided
if (!isset($_GET['class_name']) || empty($_GET['class_name'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Class name is required'
    ]);
    exit();
}

$class_name = $_GET['class_name'];

try {
    $subjectModel = new Subject();
    $subjects = $subjectModel->getSubjectsByClass($class_name, $pdo);
    
    echo json_encode([
        'success' => true,
        'subjects' => $subjects
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching subjects: ' . $e->getMessage()
    ]);
}
