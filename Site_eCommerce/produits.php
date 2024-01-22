<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits - MalyArt</title>
    <link rel="stylesheet" href="style.css">

    <link rel="icon" href="include/logoRond.png" type="image/x-icon">
</head>

<body>

    <?php
    include_once("include/header.php");
    require_once("include/connect.inc.php");
    ?>

    <?php
    // paramètres par défaut
    $prix = "decroissant";
    $recherche = "";
    if (isset($_GET['categorie'])) {
        $categ = htmlentities($_GET['categorie']);
        $sql = "SELECT distinct ProduitsFinaux.reference, ProduitsFinaux.nom, Coloriser.prixActuel, Coloriser.prixOriginal, Coloriser.couleur FROM ProduitsFinaux, Coloriser, Categories, Avis WHERE ProduitsFinaux.reference = Coloriser.refProduit ";

        if ($categ == "Promotions") {
            $sql = $sql . "AND Coloriser.prixOriginal NOT LIKE Coloriser.prixActuel ";
        } else if ($categ == "Dessins" || $categ == "Peintures" || $categ == "Materiel dart") {
            $sql = $sql . "AND ProduitsFinaux.categorie = Categories.categorie AND Categories.parent = '$categ' ";
        } else if ($categ == "MieuxNotes") {
            $sql = "SELECT ProduitsFinaux.reference, MAX(ProduitsFinaux.nom) as nom, AVG(Coloriser.prixActuel) as prixActuel, AVG(Coloriser.prixOriginal) as prixOriginal, MAX(Coloriser.couleur) as couleur 
        FROM ProduitsFinaux, Coloriser, Categories, Avis 
        WHERE ProduitsFinaux.reference = Coloriser.refProduit AND ProduitsFinaux.reference = Avis.refProduit 
        GROUP BY ProduitsFinaux.reference 
        ORDER BY AVG(Avis.note) DESC";
        } else {
            $sql = $sql . "AND ProduitsFinaux.categorie = '$categ' ";
        }
    } else {
        $recherche = "oui";
        $nomProduit = htmlentities($_GET['recherche']);
        $sql = "SELECT distinct ProduitsFinaux.reference, ProduitsFinaux.nom, Coloriser.prixActuel, Coloriser.prixOriginal, Coloriser.couleur FROM ProduitsFinaux, Coloriser WHERE ProduitsFinaux.reference = Coloriser.refProduit AND ProduitsFinaux.nom LIKE '%$nomProduit%' ";
    }

    ?>

    <div id="filtrer">
        <form action="<?php if (isset($_GET['categorie'])) {
                            echo 'produits.php?categorie=' . $categ;
                        } else {
                            echo 'produits.php?recherche=' . $nomProduit . '&submit.x=0&submit.y=0';
                        } ?>" method="post">
            <label for="prix">Filtrer par prix : </label>
            <select name="prix" id="prix">
                <option value="croissant" <?php if (!isset($_POST['prix']) || $_POST['prix'] == 'croissant') echo 'selected="selected"'; ?>>Croissant</option>
                <option value="decroissant" <?php if (isset($_POST['prix']) && $_POST['prix'] == 'decroissant') echo 'selected="selected"'; ?>>Décroissant</option>
            </select>
            <input type="submit" name="Filtrer" value="Filtrer" class="buttonFiltrer">
        </form>
        <?php
        session_start();
        if (isset($_SESSION['login'])) {
            $login = $_SESSION['login'];
            $sql3 = "SELECT DISTINCT isAdmin FROM Clients WHERE pseudo = '$login'";
            $pdostat3 = $conn->prepare($sql3);
            $pdostat3->execute();
            $statut = $pdostat3->fetch(PDO::FETCH_ASSOC);
            if ($statut['isAdmin'] == "true") {
                echo '<a href="ajoutProduit.php"><button id="ajoutProduit">Ajouter un nouveau produit</button></a>';
                echo '<a href="ajoutCompo.php"><button id="ajoutProduit">Ajouter une nouvelle composition</button></a>';
                echo '<a href="ajoutProduitToCompo.php"><button id="ajoutProduit">Ajouter un nouveau produit à une composition</button></a>';
            }
        }

        ?>
    </div>

    <?php


    if (isset($_POST['Filtrer'])) {
        $prix = $_POST['prix'];

        if ($prix == "croissant") {
            $sql = $sql . " ORDER BY prixActuel ASC";
        } else {
            $sql = $sql . " ORDER BY prixActuel DESC";
        }
    }
    ?>

    <br>;


    <div id="produits">
        <?php

        $pdostat = $conn->prepare($sql);
        $pdostat->execute();

        if ($pdostat->rowCount() == 0) {
            echo "<center><h1>Aucun produit correspondant à la recherche</h1></center>";
        }

        foreach ($pdostat as $ligne) {
            echo '<div class="table-container">';

            echo "<BR/><BR/><center><table id='tableProduits' border='2' >";

            echo '<div class="detail">';
            echo '<tr> <a href="produit.php?reference=' . $ligne['reference'] . '&couleur=' . $ligne['couleur'] . '"><img class="image" src="imgProduits/' . $ligne['reference'] . '.jpg" width="200" height="200"> </a> </tr><BR>';
            echo '<tr> <a href="produit.php?reference=' . $ligne['reference'] . '&couleur=' . $ligne['couleur'] . '" class="texteProduit">' . $ligne['nom'] . '</a> </tr><BR>';
            echo '<tr> <div class="square" style="background-color:' . $ligne['couleur'] . ';"></div> </tr>';
            echo '<tr>';
            if ($ligne['prixOriginal'] != $ligne['prixActuel']) {
                echo ' <strike>' . $ligne['prixOriginal'] . '€</strike>  ';
            }
            echo '' . $ligne['prixActuel'] . '€</tr>';
            echo '</div>';

            echo "</table></center>";

            echo '</div>';
        }

        ?>
    </div>

    <br>;

    <?php
    include_once("include/footer.php");
    ?>
</body>

</html>