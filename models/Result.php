<?php

class Result {

    private $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    /* ================================
       ADD RESULT
    ================================ */
    public function addScore($data){
        // $data must be an associative array with keys:
        // registration_number, session, term, subject_name, assessment_type, score

        $sql = "INSERT INTO scores (
                    registration_number,
                    session,
                    term,
                    subject_name,
                    assessment_type,
                    score
                ) VALUES (
                    :registration_number,
                    :session,
                    :term,
                    :subject_name,
                    :assessment_type,
                    :score
                )";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':registration_number' => $data['registration_number'],
            ':session'             => $data['session'],
            ':term'                => $data['term'],
            ':subject_name'        => $data['subject_name'],
            ':assessment_type'     => $data['assessment_type'],
            ':score'               => $data['score']
        ]);
    }

    /* ================================
       FETCH ALL RESULTS
    ================================ */
    public function getAllScores(){
        $sql = "SELECT scores.*, 
                       students.first_name,
                       students.surname,
                       students.other_names,
                       students.class_name,
                       students.class_arm
                FROM scores
                INNER JOIN students
                    ON scores.registration_number = students.registration_number
                ORDER BY students.surname ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ================================
       FILTERED RESULTS
    ================================ */
    public function getScoresByFilter($session, $term, $class, $arm, $subject){
        $sql = "SELECT scores.*, 
                       students.first_name, 
                       students.surname, 
                       students.other_names,
                       students.class_name, 
                       students.class_arm
                FROM scores
                INNER JOIN students 
                    ON scores.registration_number = students.registration_number
                WHERE 1=1";

        $params = [];

        if($session !== ''){
            $sql .= " AND scores.session = ?";
            $params[] = $session;
        }

        if($term !== ''){
            $sql .= " AND scores.term = ?";
            $params[] = $term;
        }

        if($class !== ''){
            $sql .= " AND students.class_name = ?";
            $params[] = $class;
        }

        if($arm !== ''){
            $sql .= " AND students.class_arm = ?";
            $params[] = $arm;
        }

        if($subject !== ''){
            $sql .= " AND scores.subject_name = ?";
            $params[] = $subject;
        }

        $sql .= " ORDER BY students.surname ASC, scores.assessment_type ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ================================
       DYNAMIC DROPDOWNS
    ================================ */

    // Sessions
    public function getAllSessions(){
        $sql = "SELECT DISTINCT session FROM scores ORDER BY session DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Terms
    public function getAllTerms(){
        $sql = "SELECT DISTINCT term FROM scores ORDER BY term ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Subjects (from subjects table)
    public function getAllSubjects(){
        $sql = "SELECT subject_name FROM subjects ORDER BY subject_name ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Classes
    public function getAllClasses(){
        $sql = "SELECT DISTINCT class_name FROM students ORDER BY class_name ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Arms
    public function getAllArms(){
        $sql = "SELECT DISTINCT class_arm FROM students ORDER BY class_arm ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}