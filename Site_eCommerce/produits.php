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
    include_once("include/header.php");
    require_once("include/connect.inc.php");
    ?>

    <?php
    // paramètres par défaut
    $prix = "decroissant";
    $recherche = "";
    if (isset($_GET['categorie'])) {
        $categ = htmlentities($_GET['categorie']);
        $sql = "SELECT distinct ProduitsFinaux.reference, ProduitsFinaux.nom, Coloriser.prixActuel, Coloriser.prixOriginal, Coloriser.couleur FROM ProduitsFinaux, Coloriser, Categories WHERE ProduitsFinaux.reference = Coloriser.refProduit ";
        
        if ($categ == "Promotions") {
            $sql = $sql . "AND Coloriser.prixOriginal NOT LIKE Coloriser.prixActuel ";
        }
        else if ($categ == "Dessins" || $categ == "Peintures" || $categ == "Materiel dart") {
            $sql = $sql . "AND ProduitsFinaux.categorie = Categories.categorie AND Categories.parent = '$categ' ";
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
        <form action="<?php if (isset($_GET['categorie'])) { echo 'produits.php?categorie=' . $categ; } else { echo 'produits.php?recherche=' . $nomProduit . '&submit.x=0&submit.y=0'; } ?>" method="post">
            <label for="prix">Filtrer par prix : </label>
            <select name="prix" id="prix">
                <option value="croissant" <?php if (!isset($_POST['prix']) || $_POST['prix'] == 'croissant') echo 'selected="selected"'; ?>>Croissant</option>
                <option value="decroissant" <?php if (isset($_POST['prix']) && $_POST['prix'] == 'decroissant') echo 'selected="selected"'; ?>>Décroissant</option>
            </select>
            <input type="submit" name="Filtrer" value="Filtrer" class="buttonFiltrer">
        </form>
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



    <div id="produits">
        <?php

        $pdostat = $conn->prepare($sql);
        $pdostat->execute();

        if ($pdostat->rowCount() == 0) {
            echo "<center><h1>O produit</h1></center>";
        }

        foreach ($pdostat as $ligne) {

            echo '<div id="produit">';
            echo "<BR/><BR/><center><table id='tableProduits' border='2' >";

            echo '<div class="detail">';
            echo '<tr> <a href="produit.php?reference=' . $ligne['reference'] . '&couleur=' . $ligne['couleur'] . '"><img class="image" src="imgProduits/' . $ligne['nom'] . '.jpg" width="200" height="200"> </a> </tr><BR>';
            echo '<tr> <a href="produit.php?reference=' . $ligne['reference'] . '&couleur=' . $ligne['couleur'] . '">' . $ligne['nom'] . '</a> </tr><BR>';
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

    <?php
    include_once("include/footer.php");
    ?>
</body>

</html>