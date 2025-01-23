<?php

session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST["id"];

if(!empty($_POST['old_password']) && !empty($_POST['new_password'])){
    $old_password = trim($_POST['old_password']);
    $new_password = trim($_POST['new_password']);

    try{
        $sql = "SELECT password FROM users WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if(password_verify($old_password, $user['password'])){
            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

            $sql = "UPDATE users SET password = :password WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':id', $id);

            if($stmt->execute()){
                header("Location: /ATIS/views/profile/profile");
                exit();
            }else{
                throw  new Exception("Failed to update password.");
            }
        } else{
            throw new Exception("Incorrect old password.");
        }
    } catch(Exception $e){
        $_SESSION["messages"]["errors"][] = $e->getMessage();
        header("Location: /ATIS/views/profile/edit");
        exit();
    }
}
}
?>