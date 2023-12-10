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
    $ref = htmlentities($_GET['reference']);
    $couleur = htmlentities($_GET['couleur']);
    $sql = "SELECT DISTINCT ProduitsFinaux.reference, ProduitsFinaux.categorie, ProduitsFinaux.nom, Coloriser.prixActuel, Coloriser.prixOriginal, Coloriser.nbStock, Coloriser.description, Produits.aspectTech, Coloriser.nbStock  FROM ProduitsFinaux, Coloriser, Produits, Composition WHERE ProduitsFinaux.reference = Coloriser.refProduit AND Composition.refProduit = Produits.reference AND Composition.refProduitFinal = ProduitsFinaux.reference AND ProduitsFinaux.reference = '$ref' AND Coloriser.couleur = '$couleur'";
    $pdostat = $conn->prepare($sql);
    $pdostat->execute();
    $ligne = $pdostat->fetch(PDO::FETCH_ASSOC);
    $sql2 = "SELECT DISTINCT  Coloriser.couleur FROM ProduitsFinaux, Coloriser WHERE ProduitsFinaux.reference = Coloriser.refProduit AND ProduitsFinaux.reference = '$ref'";
    $pdostat2 = $conn->prepare($sql2);
    $pdostat2->execute();
        echo '<table id="infoProduit"><tr>';
        echo '<td><img id="imgProduit" src="imgProduits/' . $ligne['nom'] . '.jpg" width="400" height="400"></td>';
        echo '<td> <div class="square" style="background-color:' . $couleur .';"></div></td>';
        echo '<td><p id="nom">' . $ligne['nom'] . '</p></td>';
        echo '<td>';
        if ($ligne['prixOriginal'] != $ligne['prixActuel']) {
            echo '<strike><p id="nom">' . $ligne['prixOriginal'] . ' €</p></strike>';
            echo '<p id="nom">' . $ligne['prixActuel'] . ' €</p>';
        }
        else{
            echo '<p id="nom">' .$ligne['prixActuel'] . ' €</p>'; 
        }
        echo '</td>';
        echo '</tr></table>';
        if ($ligne['nbStock'] == 0){
            echo '<p id="stock"> Ce produit à été victime de son succès </p><br>';
        }
        else{
            echo '<p id="stock"> Stock : ' . $ligne['nbStock'] . ' </p><br>';
            echo '<form action="ajout.php?reference=' . $ligne['reference'] . '" method="POST">';
            echo '<input id="bAjouter" type="submit" name="ajouter" value="Ajouter au panier">';
            echo '</form>';
        }
        session_start();
        echo '<table id="couleurs"><tr><td> Couleurs disponibles : <td>';
        foreach($pdostat2 as $couleurs){
            echo '<td><a href="produit.php?reference=' . $ligne['reference'] . '&couleur=' . $couleurs['couleur'] . '"> <div class="square" style="background-color:' . $couleurs['couleur'] .';"></div></a></td>';
        }
        echo '</tr></table>';
        echo '<div id="des">';
        echo 'Description : <br>' . $ligne['description'] . '<br><br>';
        echo 'Aspect Technique : <br>' . $ligne['aspectTech'] . '<br>';
        echo '</div>';  
        echo '<a href="produits.php?categorie='. $ligne['categorie']. '"> <button id="bRetour"> Retour </button></a>'



    ?>

   

    <?php
    include_once("include/footer.php");
    ?>
</body>

</html>