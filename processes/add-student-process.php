<?php
session_start();
require_once "../config/db-connect.php";
require_once "../models/Student.php";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    // Get form data
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
    if(empty($first_name) || empty($surname) || empty($gender) || empty($class_name) || empty($class_arm) || empty($password)){
        $_SESSION['error'] = "Please fill in all required fields";
        header("Location: ../student-management.php");
        exit();
    }
    
    // Handle profile picture upload
    $profile_picture = null;
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
                $profile_picture = "uploads/students/" . $file_name;
            }
        } else {
            $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
            header("Location: ../student-management.php");
            exit();
        }
    }
    
    // Create student instance
    $studentModel = new Student();
    
    // Create student record
    $student_id = $studentModel->createStudent(
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
    
    if($student_id){
        // Generate registration number based on student ID
        $registration_number = 'NEX' . str_pad($student_id, 4, '0', STR_PAD_LEFT);
        
        // Update student record with registration number
        $sql = "UPDATE students SET registration_number = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$registration_number, $student_id]);
        
        $_SESSION['success'] = "Student created successfully! Registration Number: " . $registration_number;
        header("Location: ../student-management.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to create student. Please try again.";
        header("Location: ../student-management.php");
        exit();
    }
    
} else {
    header("Location: ../student-management.php");
    exit();
}
