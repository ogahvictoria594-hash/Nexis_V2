<?php
session_start();
require_once "../config/db-connect.php";
require_once "../models/Subject.php";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    $action = trim($_POST['action']);
    $subjectModel = new Subject();
    
    // CREATE SUBJECT
    if($action === 'create'){
        $subject_name = trim($_POST['subject_name']);
        $class_name = trim($_POST['class_name']);
        $class_division = isset($_POST['class_division']) && !empty($_POST['class_division']) ? trim($_POST['class_division']) : null;
        
        // Validate required fields
        if(empty($subject_name) || empty($class_name)){
            $_SESSION['error'] = "Please fill in all required fields";
            header("Location: ../subject-management.php");
            exit();
        }
        
        // Check if subject already exists for this class
        if($subjectModel->subjectExists($subject_name, $class_name, $pdo)){
            $_SESSION['error'] = "This subject already exists for " . $class_name . "!";
            header("Location: ../subject-management.php");
            exit();
        }
        
        // Create subject
        $result = $subjectModel->createSubject($pdo, $subject_name, $class_name, $class_division);
        
        if($result){
            $_SESSION['success'] = "Subject created successfully!";
        } else {
            $_SESSION['error'] = "Failed to create subject. Please try again.";
        }
        
        header("Location: ../subject-management.php");
        exit();
    }
    
    // EDIT SUBJECT
    elseif($action === 'edit'){
        $subject_id = trim($_POST['subject_id']);
        $subject_name = trim($_POST['subject_name']);
        $class_name = trim($_POST['class_name']);
        $class_division = isset($_POST['class_division']) && !empty($_POST['class_division']) ? trim($_POST['class_division']) : null;
        
        // Validate required fields
        if(empty($subject_id) || empty($subject_name) || empty($class_name)){
            $_SESSION['error'] = "Please fill in all required fields";
            header("Location: ../subject-management.php");
            exit();
        }
        
        // Check if another subject with the same name and class exists (excluding current subject)
        $existingSubject = $subjectModel->getSubjectById($subject_id, $pdo);
        
        // Only check for duplicates if subject name or class name is changing
        if($existingSubject['subject_name'] !== $subject_name || $existingSubject['class_name'] !== $class_name){
            if($subjectModel->subjectExists($subject_name, $class_name, $pdo)){
                $_SESSION['error'] = "This subject already exists for " . $class_name . "!";
                header("Location: ../subject-management.php");
                exit();
            }
        }
        
        // Update subject
        $result = $subjectModel->editSubjectById($subject_id, $subject_name, $class_name, $class_division, $pdo);
        
        if($result){
            $_SESSION['success'] = "Subject updated successfully!";
        } else {
            $_SESSION['error'] = "No changes were made or failed to update subject.";
        }
        
        header("Location: ../subject-management.php");
        exit();
    }
    
    // DELETE SUBJECT
    elseif($action === 'delete'){
        $subject_id = trim($_POST['subject_id']);
        
        if(empty($subject_id)){
            $_SESSION['error'] = "Invalid subject ID";
            header("Location: ../subject-management.php");
            exit();
        }
        
        // Delete subject
        $result = $subjectModel->deleteSubjectById($subject_id, $pdo);
        
        if($result){
            $_SESSION['success'] = "Subject deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete subject. Please try again.";
        }
        
        header("Location: ../subject-management.php");
        exit();
    }
    
    else {
        $_SESSION['error'] = "Invalid action";
        header("Location: ../subject-management.php");
        exit();
    }
    
} else {
    header("Location: ../subject-management.php");
    exit();
}
