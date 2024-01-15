<?php
require_once('include/connect.inc.php');

// On récupère les données du formulaire
if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['mail']) && isset($_POST['tel']) && isset($_POST['dtN']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['ConfirmPassword'])) {
    //recuperation des données avec htmlentities 
    $nom = htmlentities($_POST['nom'], ENT_QUOTES, 'UTF-8');
    $prenom = htmlentities($_POST['prenom'], ENT_QUOTES, 'UTF-8');
    $mail = htmlentities($_POST['mail'], ENT_QUOTES, 'UTF-8');
    $tel = htmlentities($_POST['tel'], ENT_QUOTES, 'UTF-8');
    $dtN = htmlentities($_POST['dtN'], ENT_QUOTES, 'UTF-8');
    $username = htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8');
    $password = htmlentities($_POST['password'], ENT_QUOTES, 'UTF-8');
    $ConfirmPassword = htmlentities($_POST['ConfirmPassword'], ENT_QUOTES, 'UTF-8');
    //On recupere les données de l'adresse
    $rue = htmlentities($_POST['rue'], ENT_QUOTES, 'UTF-8');
    $ville = htmlentities($_POST['ville'], ENT_QUOTES, 'UTF-8');
    $codePostal = htmlentities($_POST['codePostal'], ENT_QUOTES, 'UTF-8');
    $complement = htmlentities($_POST['complement'], ENT_QUOTES, 'UTF-8');
    $pays = htmlentities($_POST['pays'], ENT_QUOTES, 'UTF-8');


    $nbErreur = 0;

    if (isset($_GET['redirect']) && isset($_GET['couleur'])) {
        $redirect = $_GET['redirect'] . "&couleur=" .  $_GET['couleur'];
    } else if (isset($_GET['redirect']) && !isset($_GET['couleur'])) {
        $redirect = $_GET['redirect'];
    }

    // On vérifie que le nom d'utilisateur n'est pas déjà utilisé
    $request = $conn->prepare('SELECT * FROM Clients WHERE pseudo = :username');
    $request->bindParam(':username', $username);
    $request->execute();
    if ($request->rowCount() != 0) {
        $nbErreur++;
        header('Location: inscription.php?erreur=usernameUsed&' . $redirect);
        die();
    }
    // On vérifie que l'adresse mail n'est pas déjà utilisée
    $request = $conn->prepare('SELECT * FROM Clients WHERE adresseMail = :mail');
    $request->bindParam(':mail', $mail);
    $request->execute();
    if ($request->rowCount() != 0) {
        $nbErreur++;
        header('Location: inscription.php?erreur=mailUsed&' . $redirect);
        die();
    }
    // On vérifie que le numéro de téléphone n'est pas déjà utilisé
    $request = $conn->prepare('SELECT * FROM Clients WHERE tel = :tel');
    $request->bindParam(':tel', $tel);
    $request->execute();
    if ($request->rowCount() != 0) {
        $nbErreur++;
        header('Location: inscription.php?erreur=telUsed&' . $redirect);
        die();
    }
    // On vérifie que l'adresse mail est valide
    if (!preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]{2,}[.][a-zA-Z]{2,4}$/", $mail)) {
        $nbErreur++;
        header('Location: inscription.php?erreur=mail&' . $redirect);
        die();
    }
    // On vérifie que le numéro de téléphone est valide
    if (!preg_match("/^(?:\+33|0)[1-9]([0-9]{1,8})$/", $tel)) {
        $nbErreur++;
        header('Location: inscription.php?erreur=tel&' . $redirect);
        die();
    }
    // On chiffre le mot de passe
    $encPassword = hash('sha256', $password);
    if ($nbErreur == 0) {

        try {
            $sql = "CALL AjouterClient(:rue, :ville, :codePostal, :complement, :pays, :nom, :prenom, :mail, :tel, :dtN, :username, :password)";
            $request = $conn->prepare($sql);
            $request->bindParam(':rue', $rue);
            $request->bindParam(':ville', $ville);
            $request->bindParam(':codePostal', $codePostal);
            $request->bindParam(':complement', $complement);
            $request->bindParam(':pays', $pays);
            $request->bindParam(':nom', $nom);
            $request->bindParam(':prenom', $prenom);
            $request->bindParam(':mail', $mail);
            $request->bindParam(':tel', $tel);
            $request->bindParam(':dtN', $dtN);
            $request->bindParam(':username', $username);
            $request->bindParam(':password', $encPassword);
            $request->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
            // on redirige vers la page d'inscription avec un message d'erreur
            header('Location: inscription.php?erreur=' . $e->$e->getMessage() . ' ' . $e->getCode());
        }
        // On redirige l'utilisateur vers la page de connexion
        echo '<script type="text/javascript">window.alert("Inscription réussie !");</script>';
        //si ?redirect= est présent dans l'url, on redirige vers login.php avec le paramètre redirect



        echo '<script type="text/javascript">window.location.replace("login.php?redirect=' . $redirect . '");</script>';
    }
} else {
    echo '<script type="text/javascript">window.location.replace("inscription.php");</script>';
}
