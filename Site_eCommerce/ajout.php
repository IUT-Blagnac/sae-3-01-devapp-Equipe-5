<?php
session_start();
require_once('include/loginCheck.php');


if (isset($_GET['reference']) && isset($_GET['couleur']) && isset($_SESSION['id'])) {

    require_once('include/connect.inc.php');

    // Récupération des données
    $idClient = $_SESSION['id'];
    $couleur = htmlentities($_GET['couleur']);
    $reference = htmlentities($_GET['reference']);

    // Appel de la procédure stockée
    $sql = "CALL AjouterAuPanier(:idClient, :couleur, :reference)";
    $request = $conn->prepare($sql);
    $request->bindParam(':idClient', $idClient);
    $request->bindParam(':couleur', $couleur);
    $request->bindParam(':reference', $reference);
    try {
        $request->execute();

        echo "<script>alert('Produit ajouté au panier')</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Erreur sql lors de l\'ajout au panier')</script>";
        echo "<script>window.location.replace('panier.php')</script>";
    }
} else {
    echo "<script>alert('Erreur php lors de l\'ajout au panier')</script>";
}

echo "<script>window.location.replace('panier.php')</script>";
