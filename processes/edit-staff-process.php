<?php
session_start();
require_once "../config/db-connect.php";
require_once "../models/Staff.php";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    // Get form data
    $staff_id = trim($_POST['staff_id']);
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
    if(empty($staff_id) || empty($first_name) || empty($surname) || empty($gender) || empty($category) || empty($job_description) || empty($phone_number)){
        $_SESSION['error'] = "Please fill in all required fields";
        header("Location: ../staff-management.php");
        exit();
    }
    
    // Create staff instance
    $staffModel = new Staff();
    
    // Get existing staff data
    $existing_staff = $staffModel->getStaffById($staff_id, $pdo);
    
    if(!$existing_staff){
        $_SESSION['error'] = "Staff not found";
        header("Location: ../staff-management.php");
        exit();
    }
    
    // Handle profile picture upload
    $profile_picture = $existing_staff['profile_picture']; // Keep existing picture by default
    
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
                // Delete old profile picture if it exists
                if(!empty($existing_staff['profile_picture']) && file_exists("../" . $existing_staff['profile_picture'])){
                    unlink("../" . $existing_staff['profile_picture']);
                }
                
                $profile_picture = "uploads/staff/" . $file_name;
            }
        } else {
            $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
            header("Location: ../staff-management.php");
            exit();
        }
    }
    
    // Update staff record
    $result = $staffModel->editStaffById(
        $staff_id,
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
    
    // Note: Staff number is NOT updated during edit - it remains the same
    
    if($result){
        $_SESSION['success'] = "Staff updated successfully!";
        header("Location: ../staff-management.php");
        exit();
    } else {
        $_SESSION['error'] = "No changes were made or failed to update staff.";
        header("Location: ../staff-management.php");
        exit();
    }
    
} else {
    header("Location: ../staff-management.php");
    exit();
}
