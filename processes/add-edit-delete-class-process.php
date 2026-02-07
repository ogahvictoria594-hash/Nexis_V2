<?php
session_start();
require_once "../config/db-connect.php";
require_once "../models/ClassModel.php";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    $action = trim($_POST['action']);
    $classModel = new ClassModel();
    
    // CREATE CLASS
    if($action === 'create'){
        $class_branch = trim($_POST['class_branch']);
        $class_name = trim($_POST['class_name']);
        $class_arm_array = isset($_POST['class_arm']) ? $_POST['class_arm'] : [];
        
        // Validate required fields
        if(empty($class_branch) || empty($class_name)){
            $_SESSION['error'] = "Please fill in all required fields";
            header("Location: ../class-management.php");
            exit();
        }
        
        // Check if class already exists (ignores spaces and case)
        if($classModel->classExists($class_name, $pdo)){
            $_SESSION['error'] = "A class with a similar name already exists!";
            header("Location: ../class-management.php");
            exit();
        }
        
        // Convert array to comma-separated string
        $class_arm = !empty($class_arm_array) ? implode(',', $class_arm_array) : null;
        
        // Create class
        $result = $classModel->createClass($pdo, $class_branch, $class_name, $class_arm);
        
        if($result){
            $_SESSION['success'] = "Class created successfully!";
        } else {
            $_SESSION['error'] = "Failed to create class. Please try again.";
        }
        
        header("Location: ../class-management.php");
        exit();
    }
    
    // EDIT CLASS
    elseif($action === 'edit'){
        $class_id = trim($_POST['class_id']);
        $class_branch = trim($_POST['class_branch']);
        $class_name = trim($_POST['class_name']);
        $class_arm_array = isset($_POST['class_arm']) ? $_POST['class_arm'] : [];
        
        // Validate required fields
        if(empty($class_id) || empty($class_branch) || empty($class_name)){
            $_SESSION['error'] = "Please fill in all required fields";
            header("Location: ../class-management.php");
            exit();
        }
        
        // Check if another class with the same name exists (excluding current class)
        $existingClass = $classModel->getClassById($class_id, $pdo);
        $normalized_current = strtolower(str_replace(' ', '', $existingClass['class_name']));
        $normalized_new = strtolower(str_replace(' ', '', $class_name));
        
        // Only check for duplicates if the class name is actually changing
        if($normalized_current !== $normalized_new){
            if($classModel->classExists($class_name, $pdo)){
                $_SESSION['error'] = "A class with a similar name already exists!";
                header("Location: ../class-management.php");
                exit();
            }
        }
        
        // Convert array to comma-separated string
        $class_arm = !empty($class_arm_array) ? implode(',', $class_arm_array) : null;
        
        // Update class
        $result = $classModel->editClassById($class_id, $class_branch, $class_name, $class_arm, $pdo);
        
        if($result){
            $_SESSION['success'] = "Class updated successfully!";
        } else {
            $_SESSION['error'] = "No changes were made or failed to update class.";
        }
        
        header("Location: ../class-management.php");
        exit();
    }
    
    // DELETE CLASS
    elseif($action === 'delete'){
        $class_id = trim($_POST['class_id']);
        
        if(empty($class_id)){
            $_SESSION['error'] = "Invalid class ID";
            header("Location: ../class-management.php");
            exit();
        }
        
        // Delete class
        $result = $classModel->deleteClassById($class_id, $pdo);
        
        if($result){
            $_SESSION['success'] = "Class deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete class. Please try again.";
        }
        
        header("Location: ../class-management.php");
        exit();
    }
    
    else {
        $_SESSION['error'] = "Invalid action";
        header("Location: ../class-management.php");
        exit();
    }
    
} else {
    header("Location: ../class-management.php");
    exit();
}
