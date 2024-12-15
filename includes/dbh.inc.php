<?php
session_start();

$dbconn = "mysql:host=localhost;dbname=";
$user = "";  
$pass = ""; 

function ofcourseAnERROR() {
    header('Location: error.php'); 
    exit();
}

try {
    $pdo = new PDO($dbconn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $errorSQL) {
    ofcourseAnERROR();
}

function checkSubjectIfTeacher($pdo)
{
    if (!isset($_SESSION['user_id'])) {
        return;
    }
    $userId = $_SESSION['user_id'];
    try {
        $stmt = $pdo->prepare("SELECT role, subject FROM users WHERE id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $user = $stmt->fetch();

        if ($user && $user['role'] === 'teacher' && (empty($user['subject']) || $user['subject'] === NULL)) {
            header("Location: choosesubject.php");
            exit();
        }
    } catch (PDOException $e) {
        error_log("SQL Error: " . $e->getMessage());
        ofcourseAnERROR();
    }
}
checkSubjectIfTeacher($pdo);
?>