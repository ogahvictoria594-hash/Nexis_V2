<?php

class User{
    public function getUserByStaffNumberAndUserRole($staff_number, $user_role, $pdo){
        $sql = "SELECT * FROM users WHERE staff_number = ? AND user_role = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$staff_number, $user_role]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    }
}