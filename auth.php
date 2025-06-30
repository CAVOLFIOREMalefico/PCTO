<?php
session_start();
require 'database.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nickname = trim($_POST['nickname']);
    $password = trim($_POST['password']);
    
    if(empty($nickname) || empty($password)) {
        header("Location: index.php?error=Por favor complete todos los campos");
        exit;
    }
    
    try {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE nickname = :nickname");
        $stmt->bindParam(':nickname', $nickname);
        $stmt->execute();
        
        if($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nickname'] = $nickname;
                header("Location: welcome.php");
                exit;
            } else {
                header("Location: index.php?error=Credenciales incorrectas");
                exit;
            }
        } else {
            header("Location: index.php?error=Credenciales incorrectas");
            exit;
        }
    } catch(PDOException $e) {
        header("Location: index.php?error=Error en el servidor");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>