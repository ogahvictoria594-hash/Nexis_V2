<?php

class Staff{
    function getTotalStaffCount($pdo){
        $sql = "SELECT COUNT(*) AS total FROM staffs";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $totalStaff = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$totalStaff['total'];
    }

    //Get all staffs
    function getAllStaff($pdo){
        $sql = "SELECT * FROM staffs ORDER BY id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $staffs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $staffs;
    }

    //Get Staff By ID
    function getStaffById($id, $pdo){
        $sql = "SELECT * FROM staffs WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);
        return $staff;
    }

    //Create Staff Record
    function createStaff($first_name, $surname, $other_names, $gender, $date_of_birth, $date_of_employment, $residential_address, $phone_number, $profile_picture, $job_description, $category, $pdo){
        $sql = "INSERT INTO staffs (first_name, surname, other_names, gender, date_of_birth, date_of_employment, residential_address, phone_number, profile_picture, job_description, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
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
            $category
        ]);
        return $result ? $pdo->lastInsertId() : false;
    }


    //Edit Staff By ID
    function editStaffById($id, $first_name, $surname, $other_names, $gender, $date_of_birth, $date_of_employment, $residential_address, $phone_number, $profile_picture, $job_description, $category, $pdo){
        $sql = "UPDATE staffs SET first_name = ?, surname = ?, other_names = ?, gender = ?, date_of_birth = ?, date_of_employment = ?, residential_address = ?, phone_number = ?, profile_picture = ?, job_description = ?, category = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
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
            $id
        ]);
        return $stmt->rowCount() > 0;
    }


    //Delete Staff By ID
    function deleteStaffById($id, $pdo){
        $sql = "DELETE FROM staffs WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    //Assign roles to staff
    function assignRoleToStaff($pdo, $staff_number, $user_role, $password, $class_name = null, $subject_name = null, $user_branch = null){
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO staff_roles (staff_number, user_role, password, class_name, subject_name, user_branch) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $staff_number,
            $user_role,
            $hashed_password,
            $class_name,
            $subject_name,
            $user_branch
        ]);
        return $result ? $pdo->lastInsertId() : false;
    }
}