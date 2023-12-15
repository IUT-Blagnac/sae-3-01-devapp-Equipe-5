<?php
require_once('include/connect.inc.php');

if (isset($_POST['login']) && isset($_POST['password'])) {
    $login = htmlentities($_POST['login']);
    $password = htmlentities($_POST['password']);

    $encPassword = hash('sha256', $password);

    try {
        $request = $conn->prepare(
           'SELECT * FROM Clients 
            WHERE pseudo = :login AND motDePasse = :password'
        );
        $request->bindParam(':login', $login);
        $request->bindParam(':login', $login);
        $request->bindParam(':password', $encPassword);
        $request->execute();
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage() . "<BR>";
        die();
    }

    if ($request->rowCount() == 1) {
        session_start();
        $_SESSION['login'] = $login;
        $_SESSION['id'] = $request->fetch()['idClient'];
        $_SESSION['client'] = $request->fetch();
        $_SESSION['logged'] = true;
        if (isset($_POST['remember'])) {
            setcookie("Login", $_POST['login'], time() + 3600);
        }
        //alert
        echo "<script>alert('Vous êtes connecté')</script>";

        if (isset($_GET['redirect']) && isset($_GET['couleur'])) {
            $redirect = $_GET['redirect'] . "&couleur=".  $_GET['couleur'];
            
          } else if (isset($_GET['redirect']) && !isset($_GET['couleur'])) {
            $redirect = $_GET['redirect'];
          } 

        //redirection modulaire
        if (isset($_GET['redirect'])) {
            header('Location: ' . $redirect);
        } else {
            header('Location: index.php');
        }
    } else {
        header('Location: login.php?erreur=true&redirect=' . $_GET['redirect'] . '&couleur=' . $_GET['couleur'] . '');
    }
}
