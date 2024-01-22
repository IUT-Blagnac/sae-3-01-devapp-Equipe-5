<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produit - MalyArt</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="include/logoRond.png" type="image/x-icon">

</head>

<body>

    <?php
    session_start();
    include_once("include/header.php");
    require_once("include/connect.inc.php");
    if (isset($_GET['reference']) && isset($_GET['couleur'])) {
    } else {
        echo '<script type="text/javascript">window.location.replace("index.php");</script>';
    }
    ?>

    <?php

    $ref = htmlentities($_GET['reference']);
    $couleur = htmlentities($_GET['couleur']);
    $sql = "SELECT DISTINCT ProduitsFinaux.reference, ProduitsFinaux.categorie, ProduitsFinaux.nom, Coloriser.prixActuel, Coloriser.prixOriginal, Coloriser.nbStock, Coloriser.description, Produits.aspectTech  FROM ProduitsFinaux, Coloriser, Produits, Composition WHERE ProduitsFinaux.reference = Coloriser.refProduit AND Composition.refProduit = Produits.reference AND Composition.refProduitFinal = ProduitsFinaux.reference AND ProduitsFinaux.reference = '$ref' AND Coloriser.couleur = '$couleur'";
    $pdostat = $conn->prepare($sql);
    $pdostat->execute();
    $ligne = $pdostat->fetch(PDO::FETCH_ASSOC);
    $sql2 = "SELECT DISTINCT  Coloriser.couleur FROM ProduitsFinaux, Coloriser WHERE ProduitsFinaux.reference = Coloriser.refProduit AND ProduitsFinaux.reference = '$ref'";
    $pdostat2 = $conn->prepare($sql2);
    $pdostat2->execute();
    if (isset($_SESSION['login'])) {
        $login = $_SESSION['login'];
        $sql3 = "SELECT DISTINCT  isAdmin FROM Clients WHERE pseudo = '$login'";
        $pdostat3 = $conn->prepare($sql3);
        $pdostat3->execute();
        $statut = $pdostat3->fetch(PDO::FETCH_ASSOC);
    }
    // Requete pour les avis
    $req = $conn->prepare("SELECT Avis.*, Clients.pseudo FROM Avis, Clients WHERE Avis.idClient = Clients.idClient AND Avis.refProduit = :refProduit ORDER BY Avis.dateAjout DESC");
    $req->bindParam(':refProduit', $ref);
    $req->execute();
    $reqAvis = $req->fetchAll(PDO::FETCH_ASSOC);

    echo '<table id="infoProduit"><tr>';
    echo '<td><img id="imgProduit" src="imgProduits/' . $ligne['reference'] . '.jpg" width="400" height="400"></td>';
    echo '<td><p id="nom">' . $ligne['nom'] . '</p></td>';
    echo '<td>';
    echo '<p id="nom"> Prix : </p>';
    if ($ligne['prixOriginal'] != $ligne['prixActuel']) {
        echo '<strike><p id="nom">' . $ligne['prixOriginal'] . ' €</p></strike>';
        echo '<p id="nom">' . $ligne['prixActuel'] . ' €</p>';
    } else {
        echo '<p id="nom">' . $ligne['prixActuel'] . ' €</p>';
    }
    echo '</td>';
    echo '</tr></table>';
    echo '<p id="stock">  <a href="produits.php?categorie=' . $ligne['categorie'] . '"> <B>CATEGORIE DU PRODUIT</B> </a></p>';
    echo '<br>';
    if ($ligne['nbStock'] == 0) {
        echo '<p id="stock"> Ce produit à été victime de son succès </p><br>';
    } else {
        echo '<p id="stock"> Stock : ' . $ligne['nbStock'] . ' </p><br>';
        echo '<form action="ajout.php?reference=' . $ligne['reference'] . '&couleur=' . $couleur . ' " method="POST">';
        //si connecté le bouton ajouter au panier est affiché sinon le bouton est remplacé par un lien vers la page de connexion avec ?redirect=produit.php?reference=ref&couleur=couleur
        if (isset($_SESSION['login']) && !empty($_SESSION['login'])) {
            echo '<button type="submit" name="ajoutPanier" id="bAjouter"> Ajouter au panier </button>';
        } else {
            echo '<a href="connexion.php?redirect=produit.php?reference=' . $ligne['reference'] . '&couleur=' . $couleur . '"> <button id="bAjouter2"> Ajouter au panier </button></a>';
        }
        echo '</form>';
    }
    echo '<table id="couleurs"><tr><td> Couleurs disponibles : <td>';
    foreach ($pdostat2 as $couleurs) {
        if ($couleurs['couleur'] == $couleur) {
            echo '<td><a href="produit.php?reference=' . $ligne['reference'] . '&couleur=' . $couleurs['couleur'] . '"> <div class="square" id="selected" style="background-color:' . $couleurs['couleur'] . ';"></div></a></td>';
        } else {
            echo '<td><a href="produit.php?reference=' . $ligne['reference'] . '&couleur=' . $couleurs['couleur'] . '"> <div class="square" style="background-color:' . $couleurs['couleur'] . ';"></div></a></td>';
        }
    }
    echo '</tr></table>';
    echo '<div id="des">';
    echo 'Description : <br>' . $ligne['description'] . '<br><br>';
    echo 'Aspect Technique : <br>' . $ligne['aspectTech'] . '<br>';
    echo '</div>';
    if (isset($statut)) {
        if ($statut['isAdmin'] == "true") {
            echo '<a href="produitsModif.php?reference=' . $ligne['reference'] . '&couleur=' . $couleur . '"> <button id="bModif"> Modifier l\'article </button></a><br>';
        }
    }
    echo '<a href="produits.php?categorie=' . $ligne['categorie'] . '"> <button id="bRetour"> Retour </button></a>';


    echo '<br><br>';

    echo '<div class="commentaires">';
    echo '<h2>Commentaires</h2><BR>';


    // vérifier que dans la table avis il n y a pas d'avis pour ce produit avec ce client
    if (isset($_SESSION['login'])) {

        $dejaAvis = false;

        foreach ($reqAvis as $avis) {
            if ($avis['pseudo'] == $_SESSION['login']) {
                $dejaAvis = true;
            }
        }

        if ($dejaAvis == false) {
            echo '<div class="commenter">';
            echo '<form method="POST" enctype="multipart/form-data">';
            echo '<div class="rating">';
            echo '<input type="radio" id="star5" name="rating" value="5" required>';
            echo '<label for="star5">&#9733;</label>';
            echo '<input type="radio" id="star4" name="rating" value="4">';
            echo '<label for="star4">&#9733;</label>';
            echo '<input type="radio" id="star3" name="rating" value="3">';
            echo '<label for="star3">&#9733;</label>';
            echo '<input type="radio" id="star2" name="rating" value="2">';
            echo '<label for="star2">&#9733;</label>';
            echo '<input type="radio" id="star1" name="rating" value="1">';
            echo '<label for="star1">&#9733;</label>';
            echo '</div><BR>';
            echo '<textarea class="form-control" type="text" name="commentaire" placeholder="Votre commentaire ..." maxlength="180" required>';
            echo '</textarea><BR>';
            echo '<input type="file" name="file" accept=".jpg"><BR>';
            echo '<button type="submit" name="avis" class="buttonAvis">Laisser un avis</button>';
            echo '</div>';
        }
    }

    if (!empty($_POST)) {
        extract($_POST);

        $valid = true;
        $depasseLimite = false;

        if (isset($_POST['avis'])) {
            $commentaire = htmlentities(trim($commentaire), ENT_QUOTES, 'UTF-8');

            // Limite de caractères par mot
            $maxCaractereParMot = 15;

            // Découper le commentaire en mots
            $mots = explode(' ', $commentaire);


            foreach ($mots as $mot) {
                if (mb_strlen($mot, 'UTF-8') > $maxCaractereParMot) {
                    $depasseLimite = true;
                    break;
                }
            }

            if (empty($commentaire) || empty($rating) || $depasseLimite) {
                $valid = false;
                echo "<script>alert('Les mots ne peuvent pas avoir plus de 15 caratères !');</script>";
                echo '<script>window.location.replace("produit.php?reference=' . $ref . '&couleur=' . $couleur . '");</script>';
            }

            if ($valid) {

                try {
                    $date = date('Y/m/d H:i:s');
                    $req = $conn->prepare("INSERT INTO Avis (refProduit, idClient, note, commentaire, dateAjout) VALUES (:refProduit, :idClient, :note, :commentaire, :dateAjout)");
                    $req->bindParam(':refProduit', $ref);
                    $req->bindParam(':idClient', $_SESSION['id']);
                    $req->bindParam(':note', $rating);
                    $req->bindParam(':commentaire', $commentaire);
                    $req->bindParam(':dateAjout', $date);
                    $req->execute();

                    // Vérifier si le fichier a été correctement téléchargé
                    if (isset($_FILES['file']) && $_FILES["file"]["error"] == 0 && !empty($_FILES["file"]["name"])) {

                        $reqLastId = $conn->prepare("SELECT MAX(idCommentaire) FROM Avis");
                        $reqLastId->execute();
                        $lastId = $reqLastId->fetch(PDO::FETCH_ASSOC);

                        // Obtenir le dernier ID de commentaire
                        $lastIdValue = $lastId["MAX(idCommentaire)"];

                        // Vérifier le type de fichier (assurez-vous qu'il s'agit d'une image JPG)
                        $allowedTypes = ["image/jpeg", "image/jpg"];
                        if (in_array($_FILES["file"]["type"], $allowedTypes)) {

                            // Définir le nom du fichier avec le dernier ID de commentaire
                            $uploadDir = "imgAvis/"; // Répertoire où vous souhaitez stocker les fichiers téléchargés
                            $uploadFile = $uploadDir . $lastIdValue . ".jpg";

                            move_uploaded_file($_FILES["file"]["tmp_name"], $uploadFile);
                        }
                    }

                    echo '<script>window.location.replace("produit.php?reference=' . $ref . '&couleur=' . $couleur . '");</script>';
                } catch (PDOException $e) {
                    echo '';
                }
            }
        }
    }


    echo '<br>';

    
    foreach ($reqAvis as $avis) {
        try {
            echo '<div class="avis">';
            echo '<div class="infoAvis">';
            echo 'De ' . $avis['pseudo'] . ' | Le ' . $avis['dateAjout'] . ' | ' . $avis['note'] . '&#9733;';
            echo '</div>';
            echo '<hr>';
            echo '<br>';
            echo '<div class="contentAvis">';
            echo '<div class="commentaireAvis">';
            echo $avis['commentaire'] . '<br>';
            echo '</div>';
            if (file_exists("imgAvis/" . $avis['idCommentaire'] . ".jpg")) {
                echo '<img class="imageAvis" src="imgAvis/' . $avis['idCommentaire'] . '.jpg" alt="Avis Image" style="max-width: 150px; max-height: 150px; float: right;"> <br>';
            }
            echo '</div>';
            echo '</div>';
            if (isset($_SESSION['login']) && $avis['pseudo'] == $_SESSION['login'] || isset($statut) && $statut['isAdmin'] == "true") {
                echo '<form method="POST">';
                echo '<input type="hidden" name="idCommentaire" value="' . $avis['idCommentaire'] . '">';
                echo '<input type="submit" name="supprimer" value="Supprimer" class="buttonSupprimer">';
                echo '</form>';
            }
            echo '<br>';

            if (isset($_POST['supprimer'])) {
                $idCommentaire = $_POST['idCommentaire'];
                $req = $conn->prepare("DELETE FROM Avis WHERE idCommentaire = :idCommentaire");
                $req->bindParam(':idCommentaire', $idCommentaire);
                $req->execute();
                echo '<script>window.location.replace("produit.php?reference=' . $ref . '&couleur=' . $couleur . '");</script>';
            }
        } catch (PDOException $e) {
            echo '';
        }
    }

    if (empty($reqAvis)) {
        echo '<div class="avis"';
        echo 'div class="contentAvis"';
        echo '<h2 id="stock"> Il n\'y a pas encore d\'avis pour ce produit </h2><br>';
        echo '</div>';
        echo '</div>';
        echo '<br>';
    }

    echo '</div>';


    ?>



    <?php
    include_once("include/footer.php");
    ?>
</body>

</html>