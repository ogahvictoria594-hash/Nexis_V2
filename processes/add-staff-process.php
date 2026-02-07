<?php
session_start();
require_once "../config/db-connect.php";
require_once "../models/Staff.php";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    // Get form data
    $first_name = trim($_POST['first_name']);
    $surname = trim($_POST['surname']);
    $other_names = trim($_POST['other_names'] ?? '');
    $gender = trim($_POST['gender']);
    $date_of_birth = trim($_POST['date_of_birth'] ?? '');
    $date_of_employment = trim($_POST['date_of_employment'] ?? '');
    $category = trim($_POST['category']);
    $job_description = trim($_POST['job_description']);
    $phone_number = trim($_POST['phone_number']);
    $residential_address = trim($_POST['residential_address'] ?? '');
    
    // Validate required fields
    if(empty($first_name) || empty($surname) || empty($gender) || empty($category) || empty($job_description) || empty($phone_number)){
        $_SESSION['error'] = "Please fill in all required fields";
        header("Location: ../staff-management.php");
        exit();
    }
    
    // Handle profile picture upload
    $profile_picture = null;
    if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK){
        $upload_dir = "../uploads/staff/";
        
        // Create directory if it doesn't exist
        if(!is_dir($upload_dir)){
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if(in_array($file_extension, $allowed_extensions)){
            $file_name = uniqid('staff_') . '.' . $file_extension;
            $file_path = $upload_dir . $file_name;
            
            if(move_uploaded_file($_FILES['profile_picture']['tmp_name'], $file_path)){
                $profile_picture = "uploads/staff/" . $file_name;
            }
        } else {
            $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
            header("Location: ../staff-management.php");
            exit();
        }
    }
    
    // Create staff instance
    $staffModel = new Staff();
    
    // Create staff record
    $staff_id = $staffModel->createStaff(
        $first_name,
        $surname,
        $other_names,
        $gender,
        $date_of_birth,
        $date_of_employment,
        $residential_address,
        $phone_number,
        $profile_picture,
        $job_description,
        $category,
        $pdo
    );
    
    if($staff_id){
        // Generate staff number based on staff ID (format: NEXS0001, NEXS0010, NEXS0100, NEXS1000)
        $staff_number = 'NEXS' . str_pad($staff_id, 4, '0', STR_PAD_LEFT);
        
        // Update staff record with staff number
        $sql = "UPDATE staffs SET staff_number = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$staff_number, $staff_id]);
        
        $_SESSION['success'] = "Staff created successfully! Staff Number: " . $staff_number;
        header("Location: ../staff-management.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to create staff. Please try again.";
        header("Location: ../staff-management.php");
        exit();
    }
    
} else {
    header("Location: ../staff-management.php");
    exit();
}
