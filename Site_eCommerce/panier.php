<?php
session_start();
//check si l'utilisateur est connecté sinon le redirige vers la page de connexion
require_once('include/loginCheck.php');
?>



<!DOCTYPE html>
<html>

<head>
    <title>Votre panier</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="./css/panier.css">
    <link rel="icon" href="include/logoRond.png" type="image/x-icon">
    <style>

    </style>

</head>

<body>
    <?php
    require_once('include/connect.inc.php');
    if (isset($_POST['quantity']) && isset($_POST['submit'])) {

        $quantite = htmlentities($_POST['quantity']);
        $numCommande = htmlentities($_POST['numCommande']);
        $ref = htmlentities($_POST['refProduit']);
        $couleur = htmlentities($_POST['couleur']);

        //check si il y a du stock
        $requete = $conn->prepare('SELECT nbStock FROM ProduitsFinaux, Coloriser WHERE reference = :refProduit and couleur = :couleur and ProduitsFinaux.reference = Coloriser.refProduit');
        $requete->bindParam(':refProduit', $ref);
        $requete->bindParam(':couleur', $couleur);
        $requete->execute();
        $stock = $requete->fetch();
        if ($stock['nbStock'] < $quantite) {
            echo "<script>alert('Pas assez de stock')</script>";
        } else {
            $requete = $conn->prepare('UPDATE Panier SET quantite = :quantite WHERE numCommande = :numCommande AND refProduit = :refProduit and couleur = :couleur');
            $requete->bindParam(':quantite', $quantite);
            $requete->bindParam(':numCommande', $numCommande);
            $requete->bindParam(':refProduit', $ref);
            $requete->bindParam(':couleur', $couleur);
            $requete->execute();
            //js alert
            echo "<script>alert('Quantité modifiée')</script>";
        }
    }
    if (isset($_POST['delete_x']) && isset($_POST['delete_y'])) {
        //confirm message


        $quantite = htmlentities($_POST['quantity']);
        $numCommande = htmlentities($_POST['numCommande']);
        $ref = htmlentities($_POST['refProduit']);
        $couleur = htmlentities($_POST['couleur']);

        $requete = $conn->prepare('DELETE FROM Panier WHERE numCommande = :numCommande AND refProduit = :refProduit and couleur = :couleur');
        $requete->bindParam(':numCommande', $numCommande);
        $requete->bindParam(':refProduit', $ref);
        $requete->bindParam(':couleur', $couleur);
        $requete->execute();
        //js alert
        echo "<script>alert('Produit supprimé')</script>";
    }
    ?>
    <?php
    include_once("include/header.php");

    ?>
    <div class="panier">
        <h1>Panier</h1>
        <!--produits-->
        <div class="produits">

            <?php
            //prepare sql request
            try {
                $request1 = $conn->prepare('SELECT * FROM Commande,Panier, ProduitsFinaux, Coloriser where Commande.numero=Panier.numCommande and Panier.refProduit = ProduitsFinaux.reference and Coloriser.refProduit = ProduitsFinaux.reference and idClient = :idClient and Panier.couleur=Coloriser.couleur AND statut = "panier"');
                $request1->bindParam(':idClient', $_SESSION['id']);
                $request1->execute();
            } catch (PDOException $e) {
                echo "Erreur: " . $e->getMessage() . "<BR>";
                die();
            }

            if ($request1->rowCount() == 0) {
                echo "<h2>Votre panier est vide</h2>";
            } else {
                foreach ($request1 as $produit) {
                    $reference = $produit['refProduit'];
                    $quantity = $produit['quantite'];
                    $prix = $produit['prixActuel'];
                    $nom = $produit['nom'];
                    $couleur = $produit['couleur'];


                    echo '
                    <div class="produit">
                        <a href="produit.php?reference=' . $reference . '&couleur=' . $couleur . '">
                            <img src="./imgProduits/' . $reference . '.jpg" alt="produit' . $nom . '">
                        </a>
                        <div class="description">
                            <a href="produit.php?reference=' . $reference . '&couleur=' . $couleur . '">
                                <h2>' . $nom . ' &nbsp;</h2>
                            </a>
                        </div>
                        <div class="square" style="background-color:' . $produit['couleur'] . ';"></div>
                        </tr>
                        <div class="control">
                            <form class="myForm" action="panier.php" method="post">
                                <input type="hidden" name="numCommande" value="' . $produit['numCommande'] . '" readonly>
                                <input type="hidden" name="refProduit" value="' . $produit['refProduit'] . '" readonly>
                                <input type="hidden" name="couleur" value="' . $produit['couleur'] . '" readonly>
                                <div class="quantities">
                                    <img src="./images/plus.png" alt="plus icon" onclick="adjustQuantity(this, 1)">
                                    <input type="number" id="quantity" name="quantity" min="1" value="' . $quantity . '" max="1000" oninput="updateTotal(this)">
                                    <img src="./images/moins.png" alt="minus icon" onclick="adjustQuantity(this, -1)">
                                </div>
                                <div class="price">
                                    <label for="price">Price:</label>
                                    <span> ' . $prix . '</span>
                                    <input type="hidden" name="price" value="' . $prix . '" readonly>
                                    <br>
                                    <label for="total">Total: </label>
                                    <span class="totalValue"> 0</span>
                                </div>
                                <div class="buttons">
                                    <input type="submit" id="submit" name="submit" class="saveButton" style="display: none;" value="Save">
                                    <input type="button" class="cancelButton" style="display: none;" value="Cancel" onclick="resetForm(this)">
                                </div>
                                <div class="delete">
                                    <input type="image" src="./images/poubelle.png" alt="delete icon" name="delete" onclick="return confirmDelete()">
                                </div>
                            </form>
                        </div>
                    </div>';
                }
            }
            ?>
            <script>
                function confirmDelete() {
                    return confirm("Voulez-vous vraiment supprimer ce produit?");
                }
                var path = window.location.pathname;
                var page = path.split("/").pop();
                console.log(page);
            </script>

        </div>
        <!--total-->
        <div class="total">
            <!--Affichage du prix total-->
            <?php

            if ($request1->rowCount() == 0) {
                echo '<h2 id="totalComplet">Total: 0€</h2>';
                die();
            } else {

                try {
                    $request = $conn->prepare('SELECT SUM(quantite*prixActuel) as total FROM Commande,Panier, ProduitsFinaux, Coloriser where Commande.numero=Panier.numCommande and Panier.refProduit = ProduitsFinaux.reference and Coloriser.refProduit = ProduitsFinaux.reference and idClient = :idClient and Panier.couleur=Coloriser.couleur AND statut = "panier"');
                    $request->bindParam(':idClient', $_SESSION['id']);
                    $request->execute();
                } catch (PDOException $e) {
                    echo "Erreur: " . $e->getMessage() . "<BR>";
                    die();
                }
                $total = $request->fetch();
                echo '<h2 id="totalComplet">Total: ' . $total['total'] . '</h2>';
            }
            ?>
            <form action="commander.php?numCommande=<?php echo $produit['numCommande'];  ?>" method="post">
                <input type="hidden" name="total" value="<?php echo $total['total']; ?>">
                <input type="hidden" name="numCommande" value="<?php echo $produit['numCommande']; ?>">
                <input type="submit" id="commander" value="Valider ma commande ">
            </form>
        </div>

    </div>

    <?php
    include_once("include/footer.php");
    ?>
    <script src="./js/panier.js"></script>
</body>


</html>