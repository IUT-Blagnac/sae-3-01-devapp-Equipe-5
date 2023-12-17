<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits - MalyArt</title>
    <link rel="stylesheet" href="style.css">
    <!DOCTYPE html>
    <html>
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
            echo '<form method="POST">';
            echo '<div class="rating">';
            echo '<input type="radio" id="star5" name="rating" value="5">';
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
            echo '<textarea class="form-control" type="text" name="commentaire" placeholder="Votre commentaire ...">';
            echo '</textarea><BR>';
            echo '<input type="file" name="image"><BR>';
            echo '<button type="submit" name="avis" class="buttonAvis">Laisser un avis</button>';
            echo '</div>';
        }
    }

    if (!empty($_POST)) {
        extract($_POST);

        $valid = true;

        if (isset($_POST['avis'])) {
            $commentaire = htmlentities(trim($commentaire));

            if (empty($commentaire) || empty($rating)) {
                $valid = false;
                echo "<script>alert('Les champs rating et commentaire sont vides');</script>";
                echo "<script>location.reload();</script>";
            }

            if ($valid) {
                if ($_FILES['image']['size'] != 0) {
                    $uploadedFilePath = $_FILES['image']['tmp_name'];

                    // Obtenez les informations sur le fichier
                    $infoFichier = pathinfo($_FILES['image']['name']);

                    // Récupérez l'extension du fichier
                    $extension = $infoFichier['extension'];

                    // Extensions autorisées
                    $extensionsAutorisees = array('jpg');

                    if (in_array($extension, $extensionsAutorisees)) {
                        $newFileName = $_POST['ref'] . '.jpg';

                        // Chemin où le fichier doit être enregistré
                        $destination = 'imgAvis/' . $newFileName;

                        // Vérifier si le fichier existe déjà
                        if (file_exists($destination)) {
                            // Supprimer le fichier existant
                            unlink($destination);
                        }

                        // Copier le fichier téléchargé vers son nouvel emplacement avec le nouveau nom
                        copy($uploadedFilePath, $destination);

                        // Supprimer le fichier temporaire après avoir fait la copie
                        unlink($uploadedFilePath);
                    } else {
                        echo "<br>Le fichier doit être au format jpg";
                    }
                }
                try {
                    $date = date('Y/m/d H:i:s');
                    $req = $conn->prepare("INSERT INTO Avis (refProduit, idClient, note, commentaire, dateAjout) VALUES (:refProduit, :idClient, :note, :commentaire, :dateAjout)");
                    $req->bindParam(':refProduit', $ref);
                    $req->bindParam(':idClient', $_SESSION['id']);
                    $req->bindParam(':note', $rating);
                    $req->bindParam(':commentaire', $commentaire);
                    $req->bindParam(':dateAjout', $date);
                    $req->execute();
                    echo "<script>location.reload();</script>";
                } catch (PDOException $e) {
                    echo $e->getMessage();
                }
            }
        }
    }


    echo '<br>';

    foreach ($reqAvis as $avis) {
        echo '<div id="avis">';
        echo 'De ' . $avis['pseudo'] . ' | Le ' . $avis['dateAjout'] . ' | ' . $avis['note'] . '&#9733 <br>';
        echo $avis['commentaire'] . '<br>';
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