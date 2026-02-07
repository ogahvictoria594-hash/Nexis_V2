<?php
session_start();
require_once "../config/db-connect.php";
require_once "../models/Staff.php";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    // Get form data
    $staff_number = trim($_POST['staff_number']);
    $user_role = trim($_POST['user_role']);
    $password = trim($_POST['password']);
    
    // Optional fields based on role
    $class_name = isset($_POST['class_name']) && !empty($_POST['class_name']) ? trim($_POST['class_name']) : null;
    $subject_name = isset($_POST['subject_name']) && !empty($_POST['subject_name']) ? trim($_POST['subject_name']) : null;
    $user_branch = isset($_POST['user_branch']) && !empty($_POST['user_branch']) ? trim($_POST['user_branch']) : null;
    
    // Validate required fields
    if(empty($staff_number) || empty($user_role) || empty($password)){
        $_SESSION['error'] = "Please fill in all required fields";
        header("Location: ../staff-management.php");
        exit();
    }
    
    // Validate role-specific required fields
    if($user_role === 'head_of_school'){
        if(empty($user_branch)){
            $_SESSION['error'] = "User Branch is required for Head of School";
            header("Location: ../staff-management.php");
            exit();
        }
    } elseif($user_role === 'class_teacher'){
        if(empty($class_name) || empty($user_branch)){
            $_SESSION['error'] = "Class Name and User Branch are required for Class Teacher";
            header("Location: ../staff-management.php");
            exit();
        }
    } elseif($user_role === 'subject_teacher'){
        if(empty($class_name) || empty($subject_name) || empty($user_branch)){
            $_SESSION['error'] = "Class Name, Subject Name, and User Branch are required for Subject Teacher";
            header("Location: ../staff-management.php");
            exit();
        }
    }
    
    // Create staff instance
    $staffModel = new Staff();
    
    // Assign role to staff
    $result = $staffModel->assignRoleToStaff(
        $pdo,
        $staff_number,
        $user_role,
        $password,
        $class_name,
        $subject_name,
        $user_branch
    );
    
    if($result){
        $_SESSION['success'] = "Role assigned successfully to staff " . $staff_number . "!";
        header("Location: ../staff-management.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to assign role. Please try again.";
        header("Location: ../staff-management.php");
        exit();
    }
    
} else {
    header("Location: ../staff-management.php");
    exit();
}
