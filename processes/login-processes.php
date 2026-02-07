<?php
if(isset($_POST['login'])){

    session_start();
    require_once "../config/db-connect.php"; 
    require_once "../models/User.php";

    $staff_number = trim($_POST['staff_number']);
    $user_role = trim($_POST['user_role']);
    $password = trim($_POST['password']);

    //DO YOUR VALIDATIONS
    //Check for empty fields
    if(empty($staff_number) || empty($user_role) || empty($password)){
        $_SESSION['error'] = "All fields are required";
        header("Location: ../index.php");
        exit();
    }

    //Create an instance of User class
    $userInstance = new User();
    $user = $userInstance->getUserByStaffNumberAndUserRole($staff_number, $user_role, $pdo);

    //If user does not exist, redirect back to login page with error
    if(!$user){
        $_SESSION['error'] = "Invalid Staff ID or User Role.";
        header("Location: ../index.php");
        exit();
    }

    //Verify password - use this if passwords are hashed in the database
    // if(!password_verify($password, $user['password'])){
    //     $_SESSION['error'] = "Incorrect password.";
    //     header("Location: ../index.php");
    //     exit();
    // }

    //Verify password - use this if passwords are stored in plain text
    if($password !== $user['password']){
        $_SESSION['error'] = "Incorrect password.";
        header("Location: ../index.php");
        exit();
    }

    //Set session variables
    $_SESSION['id'] = $user['id'];
    $_SESSION['staff_number'] = $user['staff_number'];
    $_SESSION['user_role'] = $user['user_role'];

    //Redirect to dashboard or appropriate page
    header("Location: ../dashboard.php");
} else {
    //If the form was not submitted, redirect to index page
    header("Location: ../index.php");
    exit();
}
