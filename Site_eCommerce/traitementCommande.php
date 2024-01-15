<?php
require_once('include/connect.inc.php');

if (isset($_POST['submit'])) {
    // On récupère les données du formulaire avec htmlentities() 

    $paiement = htmlentities($_POST['paiement']);

    if ($paiement == 'CB') {
        $carte = htmlentities($_POST['numCarte']);
        $nomCarte = htmlentities($_POST['nom']);
        $prenom = htmlentities($_POST['prenom']);
        $dateCarte = htmlentities($_POST['dateExp']);
        $codeSecu = htmlentities($_POST['codeSecu']);
        $email = null;

    } else {
        $numero = null;
        $dateExp = null;
        $nomTitulaire = null;
        $prenomTitulaire = null;
        $codeSecu = null;
        $email = htmlentities($_POST['mail']);

    }

    $idAdresse = htmlentities($_POST['idAdresse']);
    $statut = htmlentities($_POST['statut']);


    $numCommande = htmlentities($_GET['numCommande']);


    //si le type de paiement est carte de crédit on ajoute les données de la carte de crédit dans la table carte de crédit (CB avec les champs numero dateExp nomTitulaire prenomTitulaire idCB idClient )

    if ($paiement == "CB") {
        $requete = $conn->prepare('INSERT INTO CB (numero, dateExp, nomTitulaire, prenomTitulaire, idCB, idClient) VALUES (:numero, :dateExp, :nomTitulaire, :prenomTitulaire, :idCB, :idClient)');
        $requete->bindParam(':numero', $carte);
        $requete->bindParam(':dateExp', $dateCarte);
        $requete->bindParam(':nomTitulaire', $nomCarte);
        $requete->bindParam(':prenomTitulaire', $prenom);
        $requete->bindParam(':idCB', $codeCarte);
        $requete->bindParam(':idClient', $_SESSION['idClient']);

        try {
            $requete->execute();
            echo "<script>alert('Carte de crédit ajoutée')</script>";
        } catch (PDOException $e) {
            echo "<script>alert('Erreur lors de l'ajout de la carte de crédit')</script>";
        }
    }

    //on modifie la commande avec les données du formulaire (numero date statut typePaiement numTransaction adrLivraison idClient ),  le statut passe à "paye" et la date est la date du jour 
    $requete = $conn->prepare('UPDATE Commande SET date = :date, statut = :statut, typePaiement = :typePaiement, adrLivraison = :adrLivraison WHERE numero = :numero');
    $date = date("Y-m-d");
    $nvStatut = "paye";
    $requete->bindParam(':date',  $date);
    $requete->bindParam(':statut', $nvStatut);
    $requete->bindParam(':typePaiement', $paiement);
    $requete->bindParam(':adrLivraison', $livraison);
    $requete->bindParam(':numero', $numCommande );
    try {
        $requete->execute();
        echo "<script>alert('Commande effectuée !')</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Erreur lors de la modification de la commande')</script>";
    }

    echo "<script>window.location.replace('index.php')</script>";
} else {
    // On redirige vers la page d'accueil
    header('Location: index.php');
    exit();
}
