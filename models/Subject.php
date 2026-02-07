<?php

class Subject{
    //Get All Subjects
    function getAllSubjects($pdo){
        $sql = "SELECT * FROM subjects ORDER BY class_name, subject_name";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $subjects;
    }

    //Get Subjects By Class
    function getSubjectsByClass($class_name, $pdo){
        $sql = "SELECT * FROM subjects WHERE class_name = ? ORDER BY subject_name";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$class_name]);
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $subjects;
    }

    //Get subject by ID
    function getSubjectById($id, $pdo){
        $sql = "SELECT * FROM subjects WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $subject = $stmt->fetch(PDO::FETCH_ASSOC);
        return $subject;
    }

    //Create Subject
    function createSubject($pdo, $subject_name, $class_name, $class_division = null){
        $sql = "INSERT INTO subjects (subject_name, class_name, class_division) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $subject_name,
            $class_name,
            $class_division
        ]);
        return $stmt->rowCount() > 0;
    }

    //Edit Subject By ID
    function editSubjectById($id, $subject_name, $class_name, $class_division, $pdo){
        $sql = "UPDATE subjects SET subject_name = ?, class_name = ?, class_division = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $subject_name,
            $class_name,
            $class_division,
            $id
        ]);
        return $stmt->rowCount() > 0;
    }

    //Delete Subject By ID
    function deleteSubjectById($id, $pdo){
        $sql = "DELETE FROM subjects WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    //Subject Exists
    function subjectExists($subject_name, $class_name, $pdo){
        $sql = "SELECT * FROM subjects WHERE subject_name = ? AND class_name = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$subject_name, $class_name]);
        $subject = $stmt->fetch(PDO::FETCH_ASSOC);
        return $subject !== false;
    }

    //Get User Subjects By User Role
    function getSubjectsByUser($staff_number, $user_role, $class_name, $pdo){
        if($user_role == 'super_admin'){
            return $this->getAllSubjects($pdo);
        } elseif($user_role == 'class_teacher'){
            $sql = "SELECT * FROM users WHERE staff_number = ? AND user_role = 'class_teacher' AND class_name = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$staff_number, $class_name]);
            $userClass = $stmt->fetch(PDO::FETCH_ASSOC);
            return $this->getSubjectsByClass($userClass['class_name'], $pdo);
        } elseif($user_role == 'subject_teacher'){
            $sql = "SELECT * FROM users WHERE staff_number = ? AND user_role = 'subject_teacher' AND class_name = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$staff_number, $class_name]);
            $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $subjects;
        } else {
            return [];
        }
    }
}