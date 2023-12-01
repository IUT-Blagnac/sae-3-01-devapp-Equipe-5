<?php
require_once('include/Connect.inc.php');
if (isset($_POST['login']) && isset($_POST['password'])) {
    $login = htmlentities($_POST['login']);
    $password = htmlentities($_POST['password']);
    $encPassword = hash('sha256', $password );

    //(...OR adresseMail = :login)
    try{
        $request = $conn -> prepare('SELECT * FROM Clients WHERE pseudo = :login AND motDePasse = :password');
        $request -> bindParam(':login', $login);
        $request -> bindParam(':login', $login);
        $request -> bindParam(':password', $encPassword);
        $request -> execute();
    }
    catch(PDOException $e){
        echo "Erreur: " . $e->getMessage() . "<BR>";
        die();
    }
    
    if ($request -> rowCount() == 1) { 
        session_start();
        $_SESSION['login'] = $login;
        $_SESSION['logged'] = true;
        if(isset($_POST['remember'])) {
            setcookie("Login", $_POST['login'], time() + 3600);  
        }
        
        header('Location: index.php');
    } else {
        header('Location: login.php?erreur=true');
    }
}
?>

