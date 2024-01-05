<?php
require_once('include/connect.inc.php');
session_start();

if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['mail']) && isset($_POST['tel']) && isset($_POST['dtN']) && isset($_POST['username']) && isset($_POST['rue']) && isset($_POST['ville']) && isset($_POST['codeP']) && isset($_POST['pays']) ) {
    // R�cup�ration des donn�es avec htmlentities 
    $nom = htmlentities($_POST['nom']);
    $prenom = htmlentities($_POST['prenom']);
    $mail = htmlentities($_POST['mail']);
    $tel = htmlentities($_POST['tel']);
    $dtN = htmlentities($_POST['dtN']);
    $username = htmlentities($_POST['username']);
    $rue = htmlentities($_POST['rue']);
    $ville = htmlentities($_POST['ville']);
    $codeP = htmlentities($_POST['codeP']);
    $pays = htmlentities($_POST['pays']);
    

    // V�rification de l'existence de champs facultatifs
    $compl = isset($_POST['compl']) ? htmlentities($_POST['compl']) : null;

    // On v�rifie que le nom d'utilisateur n'est pas d�j� utilis� (s'il a chang�)
    $request = $conn->prepare('SELECT * FROM Clients WHERE pseudo = :username AND :idClient != idClient');
    $request->bindParam(':username', $username);
    $request->bindParam(':idClient', $_SESSION['id']);
    $request->execute();
    $result = $request->fetch(PDO::FETCH_ASSOC);
    if ($result && $result['idClient'] != $_SESSION['id']) {
        header('Location: compte.php?erreur=usernameUsed');
        die();
    }
    
    // On v�rifie que le num�ro de t�l�phone n'est pas d�j� utilis�
    $request = $conn->prepare('SELECT * FROM Clients WHERE tel = :tel AND :idClient != idClient');
    $request->bindParam(':tel', $tel);
    $request->bindParam(':idClient', $_SESSION['id']);
    $request->execute();
    if ($request->rowCount() != 0) {
        $nbErreur++;
        header('Location: inscription.php?erreur=telUsed');
        die();
    }

    // On v�rifie que l'adresse mail n'est pas d�j� utilis�e (si elle a chang�)
    $request = $conn->prepare('SELECT * FROM Clients WHERE adresseMail = :mail AND :idClient != idClient ');
    $request->bindParam(':mail', $mail);
    $request->bindParam(':idClient', $_SESSION['id']);
    $request->execute();
    $result = $request->fetch(PDO::FETCH_ASSOC);
    if ($result && $result['idClient'] != $_SESSION['id']) {
        header('Location: compte.php?erreur=mailUsed');
        die();
    }

    // On v�rifie que l'adresse mail est valide
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        header('Location: compte.php?erreur=mail');
        die();
    }

    // On v�rifie que le num�ro de t�l�phone est valide
    if ($tel != '' && !preg_match("#^(\+|00)?33[1-9]([-. ]?[0-9]{2}){4}$|0[1-9]([-. ]?[0-9]{2}){4}$#", $tel)) {
        header('Location: compte.php?erreur=tel');
        die();
    }

    // On v�rifie que la date de naissance est valide
    if (!preg_match("#^[0-9]{4}-[0-9]{2}-[0-9]{2}$#", $dtN)) {
        header('Location: compte.php?erreur=dtN');
        die();
    }

    // Si tout est OK, on met � jour les informations dans la base de donn�es
   try {
    $conn->beginTransaction();

    // Mise � jour de la table Adresses
    $updateAddressQuery = 'UPDATE Adresses
                          SET rue = :rue, ville = :ville, codePostal = :codeP, 
                              complement = :compl, pays = :pays
                          WHERE idAdresse = (SELECT adresse FROM Clients WHERE idClient = :userId)';

    $request = $conn->prepare($updateAddressQuery);
    $request->bindParam(':rue', $rue);
    $request->bindParam(':ville', $ville);
    $request->bindParam(':codeP', $codeP);
    $request->bindParam(':compl', $compl);
    $request->bindParam(':pays', $pays);
    $request->bindParam(':userId', $_SESSION['id'], PDO::PARAM_INT);
    $request->execute();

    // Mise � jour de la table Clients
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

        // Si aucune exception n'est lev�e, valide la transaction
        $conn->commit();
        $test=$_SESSION['id'];
        header('Location: compte.php?user='.$test);
    } catch (PDOException $e) {
        // En cas d'erreur, annuler la transaction et rediriger avec un message d'erreur
        $conn->rollBack();
        header('Location: compte.php?erreur=sql');
        die();
    }
}
?>
