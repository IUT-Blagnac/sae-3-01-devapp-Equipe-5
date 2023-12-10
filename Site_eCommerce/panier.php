<?php
if (isset($_POST['quantity']) && isset($_POST['submit'])) {

    $quantite = htmlentities($_POST['quantity']);
    //js alert
    echo "<script>alert('Quantité modifiée')</script>";
}
if (isset($_POST['delete_x']) && isset($_POST['delete_y'])) {
    //js alert
    echo "<script>alert('Produit supprimé')</script>";
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>Page d'accueil</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="./css/panier.css">
    <style>

    </style>
</head>

<body>
    <?php
    include_once("include/header.php");

    ?>
    <div class="panier">
        <h1>Panier</h1>
        <!--produits-->
        <div class="produits">

            <?php
            for ($i = 1; $i <= 5; $i++) {
                $reference = "TVB88OLB6DQ";
                $couleur = "green";
                $quantity = 10 +$i;
                $price = 25;


                echo '<div class="produit">';
                echo '<a href="produit.php?reference=TVB88OLB6DQ&couleur=green">';
                echo '<img src="./imgProduits/Levy.jpg" alt="produit' . $i . '">';
                echo '</a>';
                echo '<div class="description">';
                echo '<a href="produit.php?reference=TVB88OLB6DQ&couleur=green">';
                echo '<h2>Produit ' . $i . '</h2>';
                echo '</a>';
                echo '</div>';
                echo '<div class="control">';
                echo '<form class="myForm" action="panier.php" method="post">';
                echo '<div class="quantities">';
                echo '<img src="./images/plus.png" alt="plus icon" onclick="adjustQuantity(this, 1)">';
                echo '<input type="number" id="quantity" name="quantity" min="1" value="10" max="1000" oninput="updateTotal(this)">';
                echo '<img src="./images/moins.png" alt="minus icon" onclick="adjustQuantity(this, -1)">';
                echo '</div>';
                echo '<div class="price">';
                echo '<label for="price">Price:</label>';
                echo '<span>25</span>';
                echo '<input type="hidden" name="price" value="'.$price.'" readonly>';
                echo '<br>';
                echo '<label for="total">Total:</label>';
                echo '<span class="totalValue">250</span>';
                echo '</div>';
                echo '<div class="delete">';
                echo '<input type="image" src="./images/poubelle.png" alt="Submit" name="delete" id="delete">';
                echo '</div>';
                echo '<div class="buttons">';
                echo '<input type="submit" id="submit" name="submit" class="saveButton" style="display: none;" value="Save">';
                echo '<input type="button" class="cancelButton" style="display: none;" value="Cancel" onclick="resetForm(this)">';
                echo '</div>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
            }
            ?>




        </div>

    </div>



    <?php
    include_once("include/footer.php");
    ?>
    <script src="./js/panier.js"></script>
</body>


</html>