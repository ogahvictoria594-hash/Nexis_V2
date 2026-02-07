<?php
session_start();
require_once "../config/db-connect.php";
require_once "../models/Student.php";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    // Get form data
    $student_id = trim($_POST['student_id']);
    $first_name = trim($_POST['first_name']);
    $surname = trim($_POST['surname']);
    $other_names = trim($_POST['other_names'] ?? '');
    $gender = trim($_POST['gender']);
    $date_of_birth = trim($_POST['date_of_birth'] ?? '');
    $class_name = trim($_POST['class_name']);
    $class_arm = trim($_POST['class_arm']);
    $residential_address = trim($_POST['residential_address'] ?? '');
    $parent_name = trim($_POST['parent_name'] ?? '');
    $parent_phone = trim($_POST['parent_phone'] ?? '');
    $password = trim($_POST['password']);
    
    // Validate required fields
    if(empty($student_id) || empty($first_name) || empty($surname) || empty($gender) || empty($class_name) || empty($class_arm) || empty($password)){
        $_SESSION['error'] = "Please fill in all required fields";
        header("Location: ../student-management.php");
        exit();
    }
    
    // Create student instance
    $studentModel = new Student();
    
    // Get existing student data
    $existing_student = $studentModel->getStudentById($student_id, $pdo);
    
    if(!$existing_student){
        $_SESSION['error'] = "Student not found";
        header("Location: ../student-management.php");
        exit();
    }
    
    // Handle profile picture upload
    $profile_picture = $existing_student['profile_picture']; // Keep existing picture by default
    
    if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK){
        $upload_dir = "../uploads/students/";
        
        // Create directory if it doesn't exist
        if(!is_dir($upload_dir)){
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if(in_array($file_extension, $allowed_extensions)){
            $file_name = uniqid('student_') . '.' . $file_extension;
            $file_path = $upload_dir . $file_name;
            
            if(move_uploaded_file($_FILES['profile_picture']['tmp_name'], $file_path)){
                // Delete old profile picture if it exists
                if(!empty($existing_student['profile_picture']) && file_exists("../" . $existing_student['profile_picture'])){
                    unlink("../" . $existing_student['profile_picture']);
                }
                
                $profile_picture = "uploads/students/" . $file_name;
            }
        } else {
            $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
            header("Location: ../student-management.php");
            exit();
        }
    }
    
    // Update student record
    $result = $studentModel->editStudentById(
        $student_id,
        $first_name,
        $surname,
        $other_names,
        $gender,
        $date_of_birth,
        $class_name,
        $class_arm,
        $residential_address,
        $parent_name,
        $parent_phone,
        $profile_picture,
        $password,
        $pdo
    );
    
    // Note: Registration number is NOT updated during edit - it remains the same
    
    if($result){
        $_SESSION['success'] = "Student updated successfully!";
        header("Location: ../student-management.php");
        exit();
    } else {
        $_SESSION['error'] = "No changes were made or failed to update student.";
        header("Location: ../student-management.php");
        exit();
    }
    
} else {
    header("Location: ../student-management.php");
    exit();
}
