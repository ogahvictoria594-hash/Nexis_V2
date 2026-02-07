<?php

class ClassModel{
    //Get All Classes
    function getAllClasses($pdo){
        $sql = "SELECT * FROM classes ORDER BY class_name ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $classes;
    }

    //Get class by ID
    function getClassById($id, $pdo){
        $sql = "SELECT * FROM classes WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $class = $stmt->fetch(PDO::FETCH_ASSOC);
        return $class;
    }

    //Get class by Name
    function getClassByBranch($class_branch, $pdo){
        $sql = "SELECT * FROM classes WHERE class_branch = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$class_branch]);
        $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $classes;
    }

    //Check if class exists (ignores spaces and case)
    function classExists($class_name, $pdo){
        // Remove spaces and convert to lowercase for comparison
        $normalized_name = strtolower(str_replace(' ', '', $class_name));
        
        $sql = "SELECT * FROM classes WHERE REPLACE(LOWER(class_name), ' ', '') = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$normalized_name]);
        $class = $stmt->fetch(PDO::FETCH_ASSOC);
        return $class !== false;
    }

    //Create Class
    function createClass($pdo, $class_branch, $class_name, $class_arm = null){
        $sql = "INSERT INTO classes (class_branch, class_name, class_arm) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $class_branch,
            $class_name,
            $class_arm
        ]);
        return $stmt->rowCount() > 0;
    }

    //Edit Class by ID
    function editClassById($id, $class_branch, $class_name, $class_arm, $pdo){
        $sql = "UPDATE classes SET class_branch = ?, class_name = ?, class_arm = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $class_branch,
            $class_name,
            $class_arm,
            $id
        ]);
        return $stmt->rowCount() > 0;
    }

    //Delete Class by ID
    function deleteClassById($id, $pdo){
        $sql = "DELETE FROM classes WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}