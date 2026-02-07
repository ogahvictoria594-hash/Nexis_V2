<?php

class Student{
    
    //Get Total Students Count
    function getTotalStudentsCount($pdo){
        $sql = "SELECT COUNT(*) AS total FROM students";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $totalStudents = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$totalStudents['total'];
    }

    //Get All Students
    function getAllStudents($pdo){
        $sql = "SELECT * FROM students ORDER BY id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $students;
    }

    //Get Students By Class
    function getStudentsByClass($class_name, $pdo){
        $sql = "SELECT * FROM students WHERE class_name = ? ORDER BY id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$class_name]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $students;
    }

    //Get Student By ID
    function getStudentById($id, $pdo){
        $sql = "SELECT * FROM students WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        return $student;
    }

    //Create Student
    function createStudent($first_name, $surname, $other_names, $gender, $date_of_birth, $class_name, $class_arm, $residential_address, $parent_name, $parent_phone, $profile_picture, $password, $pdo){
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO students (first_name, surname, other_names, gender, date_of_birth, class_name, class_arm, residential_address, parent_name, parent_phone, profile_picture, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
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
            $hashed_password
        ]);
        
        return $result ? $pdo->lastInsertId() : false;
    }

    //Edit Student by ID
    function editStudentById($id, $first_name, $surname, $other_names, $gender, $date_of_birth, $class_name, $class_arm, $residential_address, $parent_name, $parent_phone, $profile_picture, $password, $pdo){
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "UPDATE students SET first_name = ?, surname = ?, other_names = ?, gender = ?, date_of_birth = ?, class_name = ?, class_arm = ?, residential_address = ?, parent_name = ?, parent_phone = ?, profile_picture = ?, password = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
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
            $hashed_password,
            $id
        ]);
        return $stmt->rowCount() > 0;
    }

    //Delete Student by ID
    function deleteStudentById($id, $pdo){
        $sql = "DELETE FROM students WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}