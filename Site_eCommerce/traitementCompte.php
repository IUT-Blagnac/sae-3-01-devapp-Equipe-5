<?php
require_once('include/connect.inc.php');

if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['mail']) && isset($_POST['tel']) && isset($_POST['dtN']) && isset($_POST['username'])) {
    // Récupération des données avec htmlentities 
    $nom = htmlentities($_POST['nom']);
    $prenom = htmlentities($_POST['prenom']);
    $mail = htmlentities($_POST['mail']);
    $tel = htmlentities($_POST['tel']);
    $dtN = htmlentities($_POST['dtN']);
    $username = htmlentities($_POST['username']);

    // Vérification de l'existence de champs facultatifs
    $rue = isset($_POST['rue']) ? htmlentities($_POST['rue']) : null;
    $ville = isset($_POST['ville']) ? htmlentities($_POST['ville']) : null;
    $codeP = isset($_POST['codeP']) ? htmlentities($_POST['codeP']) : null;
    $compl = isset($_POST['compl']) ? htmlentities($_POST['compl']) : null;
    $pays = isset($_POST['pays']) ? htmlentities($_POST['pays']) : null;

    // On vérifie que le nom d'utilisateur n'est pas déjà utilisé (s'il a changé)
    $request = $conn->prepare('SELECT * FROM Clients WHERE pseudo = :username AND :idClient != idClient');
    $request->bindParam(':username', $username);
    $request->bindParam(':idClient', $_SESSION['id']);
    $request->execute();
    $result = $request->fetch(PDO::FETCH_ASSOC);
    if ($result && $result['idClient'] != $_SESSION['id']) {
        header('Location: compte.php?erreur=usernameUsed');
        die();
    }
    
    // On vérifie que le numéro de téléphone n'est pas déjà utilisé
    $request = $conn->prepare('SELECT * FROM Clients WHERE tel = :tel AND :idClient != idClient');
    $request->bindParam(':tel', $tel);
    $request->bindParam(':idClient', $_SESSION['id']);
    $request->execute();
    if ($request->rowCount() != 0) {
        $nbErreur++;
        header('Location: inscription.php?erreur=telUsed');
        die();
    }

    // On vérifie que l'adresse mail n'est pas déjà utilisée (si elle a changé)
    $request = $conn->prepare('SELECT * FROM Clients WHERE adresseMail = :mail AND :idClient != idClient ');
    $request->bindParam(':mail', $mail);
    $request->bindParam(':idClient', $_SESSION['id']);
    $request->execute();
    $result = $request->fetch(PDO::FETCH_ASSOC);
    if ($result && $result['idClient'] != $_SESSION['id']) {
        header('Location: compte.php?erreur=mailUsed');
        die();
    }

    // On vérifie que l'adresse mail est valide
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        header('Location: compte.php?erreur=mail');
        die();
    }

    // On vérifie que le numéro de téléphone est valide
    if ($tel != '' && !preg_match("#^(\+|00)?33[1-9]([-. ]?[0-9]{2}){4}$|0[1-9]([-. ]?[0-9]{2}){4}$#", $tel)) {
        header('Location: compte.php?erreur=tel');
        die();
    }

    // On vérifie que la date de naissance est valide
    if (!preg_match("#^[0-9]{4}-[0-9]{2}-[0-9]{2}$#", $dtN)) {
        header('Location: compte.php?erreur=dtN');
        die();
    }

    // Si tout est OK, on met à jour les informations dans la base de données
   try {
    $updateQuery = 'UPDATE Clients 
                    SET nom = :nom, prenom = :prenom, adresseMail = :mail, 
                        tel = :tel, dateNaissance = :dtN, pseudo = :username 
                    WHERE idClient = :userId';

    $request = $conn->prepare($updateQuery);
    $request->bindParam(':nom', $nom);
    $request->bindParam(':prenom', $prenom);
    $request->bindParam(':mail', $mail);
    $request->bindParam(':tel', $tel);
    $request->bindParam(':dtN', $dtN);
    $request->bindParam(':username', $username);
    $request->bindParam(':userId', $_SESSION['id'], PDO::PARAM_INT);
    $request->execute();
} catch (PDOException $e) {
    // En cas d'erreur, rediriger avec un message d'erreur
    header('Location: compte.php?erreur=sql');
    die();
}

try {    
    $updateQuery = 'UPDATE Adresses
                    SET rue = :rue, ville = :ville, codePostal = :codeP, 
                        complement = :compl, pays = :pays
                    WHERE idClient = :userId';      

    $request = $conn->prepare($updateQuery);
    $request->bindParam(':rue', $rue);
    $request->bindParam(':ville', $ville);
    $request->bindParam(':codeP', $codeP);
    $request->bindParam(':compl', $compl);
    $request->bindParam(':pays', $pays);
    $request->bindParam(':userId', $_SESSION['id'], PDO::PARAM_INT);
    $request->execute();
} catch (PDOException $e) {
    // En cas d'erreur, rediriger avec un message d'erreur
    header('Location: compte.php?erreur=sql1');
    die();
}
}
?>