<?php
require_once('include/connect.inc.php');
// On récupère les données du formulaire
if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['mail']) && isset($_POST['tel']) && isset($_POST['dtN']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['ConfirmPassword'])) {
    //recuperation des données avec htmlentities 
    $nom = htmlentities($_POST['nom']);
    $prenom = htmlentities($_POST['prenom']);
    $mail = htmlentities($_POST['mail']);
    $tel = htmlentities($_POST['tel']);
    $dtN = htmlentities($_POST['dtN']);
    $username = htmlentities($_POST['username']);
    $password = htmlentities($_POST['password']);
    $ConfirmPassword = htmlentities($_POST['ConfirmPassword']);

    // On vérifie que les deux mots de passe sont identiques
    if ($password == $ConfirmPassword) {
        header('Location: inscription.php?erreur=mdp');
    }
    // On vérifie que le nom d'utilisateur n'est pas déjà utilisé
    $request = $conn->prepare('SELECT * FROM Clients WHERE pseudo = :username');
    $request->bindParam(':username', $username);
    $request->execute();
    if ($request->rowCount() != 0) {
        header('Location: inscription.php?erreur=usernameUsed');
        die();
    }
    // On vérifie que l'adresse mail n'est pas déjà utilisée
    $request = $conn->prepare('SELECT * FROM Clients WHERE adresseMail = :mail');
    $request->bindParam(':mail', $mail);
    $request->execute();
    if ($request->rowCount() != 0) {
        header('Location: inscription.php?erreur=mailUsed');
        die();
    }
    // On vérifie que le numéro de téléphone n'est pas déjà utilisé
    $request = $conn->prepare('SELECT * FROM Clients WHERE tel = :tel');
    $request->bindParam(':tel', $tel);
    $request->execute();
    if ($request->rowCount() != 0) {
        header('Location: inscription.php?erreur=telUsed');
        die();
    }
    // On vérifie que l'adresse mail est valide
    if (!preg_match("#^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]{2,}[.][a-zA-Z]{2,4}$#", $mail)) {
        header('Location: inscription.php?erreur=mail');
        die();
    }
    // On vérifie que le numéro de téléphone est valide
    if (!preg_match("#^(\+|00)?33[1-9]([-. ]?[0-9]{2}){4}$|0[1-9]([-. ]?[0-9]{2}){4}$#", $tel)) {
        header('Location: inscription.php?erreur=tel');
        die();
    }
    // On vérifie que la date de naissance est valide
    if (!preg_match("#^[0-9]{4}-[0-9]{2}-[0-9]{2}$#", $dtN)) {
        header('Location: inscription.php?erreur=dtN');
        die();
    }
    // On vérifie que le mot de passe est valide
    if (!preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}#", $password)) {
        header('Location: inscription.php?erreur=mdp');
        die();
    }
    // On chiffre le mot de passe
    $encPassword = hash('sha256', $password);

    try {

        $request = $conn->prepare('INSERT INTO Clients (nom, prenom, adresseMail, tel, dateNaissance, pseudo, motDePasse) VALUES (:nom, :prenom, :mail, :tel, :dtN, :username, :password)');
        $request->bindParam(':nom', $nom);
        $request->bindParam(':prenom', $prenom);
        $request->bindParam(':mail', $mail);
        $request->bindParam(':tel', $tel);
        $request->bindParam(':dtN', $dtN);
        $request->bindParam(':username', $username);
        $request->bindParam(':password', $encPassword);
        $request->execute();
    } catch (PDOException $e) {
        // on rediriige vers la page d'inscription avec un message d'erreur
        header('Location: inscription.php?erreur=sql');
    }
    // On redirige l'utilisateur vers la page de connexion
    echo '<script type="text/javascript">window.alert("Inscription réussie !");</script>';
    echo '<script type="text/javascript">window.location.replace("login.php");</script>';
}
