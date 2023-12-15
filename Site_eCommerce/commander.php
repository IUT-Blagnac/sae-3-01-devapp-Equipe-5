<?php
session_start();
//check si l'utilisateur est connecté sinon le redirige vers la page de connexion
require_once('include/loginCheck.php');
#si pas de get numcommande renvoi vers page d'avant ça peut être la page de compte ou la page panier
if (!isset($_GET['numCommande']) && !isset($_SESSION['id'])) {
    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
}
if (!isset($_GET['numCommande'])) {
    header('Location: panier.php');
}

if (isset($_POST['submit']) && $_POST['rue'] != "" && $_POST['ville'] != "" && $_POST['codePostal'] != "" && $_POST['pays'] != "") {
    //alert 
    echo '<script>alert("AAAAA")</script>';
    require_once('include/connect.inc.php');
    $numCommande = htmlentities($_POST['numCommande']);
    $rue = htmlentities($_POST['rue']);
    $ville = htmlentities($_POST['ville']);
    $codePostal = htmlentities($_POST['codePostal']);
    $complement = htmlentities($_POST['complement']);
    $pays = htmlentities($_POST['pays']);
}



// ID de l'utilisateur fictif (remplacez-le par votre logique réelle pour obtenir l'ID de l'utilisateur)
$userId = 12;

// Inclure votre logique de connexion à la base de données ici
require_once("include/connect.inc.php");



// Fonction pour obtenir les adresses de l'utilisateur depuis la base de données
function getUserAddresses($userId, $conn)
{
    // Implémentez votre requête pour obtenir les adresses de l'utilisateur ici
    $request1 = $conn->prepare('SELECT * FROM Adresses, Clients WHERE Clients.idClient = :idClient AND Clients.adresse = Adresses.idAdresse');
    $request1->bindParam(':idClient', $userId);
    $request1->execute();
    $userAddresses = $request1->fetchAll();
    return $userAddresses;
}


// Traitement de la soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["existingAddress"]) && isset($_POST["newAddress"])) {
    $selectedAddress = htmlentities($_POST["existingAddress"]) ?? "";
    $newAddress = array_map('htmlspecialchars', $_POST["newAddress"]) ?? "";
    var_dump($selectedAddress);
    var_dump($newAddress);
    
    if ($selectedAddress == "default") {
        //requete pour update l'addresse de la commande a celle du client


    }else{
        //requete pour creer nouvelle addresses et update l'addresse de la commande a celle du client
    }
    
}

// Récupérer l'adresse par défaut de l'utilisateur depuis la base de données
$userDefaultAddress = getUserAddresses($userId, $conn); // Implémentez cette fonction

?>

<!DOCTYPE html>
<html>

<head>
    <title>Commander</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="./css/panier.css">
    <style>

    </style>

</head>

<body>
    <?php
    require_once('include/connect.inc.php');


    ?>
    <?php
    include_once("include/header.php");

    ?>
    <!--Récapitulatifs des produits dans le panier-->


    <div class="elements">
        <?php
        try {
            $request1 = $conn->prepare('SELECT * FROM Commande,Panier, ProduitsFinaux, Coloriser where Commande.numero=Panier.numCommande and Panier.refProduit = ProduitsFinaux.reference and Coloriser.refProduit = ProduitsFinaux.reference and idClient = :idClient and Panier.couleur=Coloriser.couleur AND statut = "panier"');
            $request1->bindParam(':idClient', $_SESSION['id']);
            $request1->execute();
        } catch (PDOException $e) {
            echo "Erreur: " . $e->getMessage() . "<BR>";
            die();
        }

        if ($request1->rowCount() == 0) {
            echo "<h2>Commande nº " . htmlentities($_GET["numCommande"]) . " n'est pas disponible </h2>";
            die();
        } else {
            $total = 0;
            echo '<div class="produits">';
            echo '<h2>Numéro de commande:  ' . htmlentities($_GET["numCommande"]) . '</h2>';
            foreach ($request1 as $produit) {
                $reference = $produit['refProduit'];
                $quantity = $produit['quantite'];
                $prix = $produit['prixActuel'];
                $nom = $produit['nom'];
                $couleur = $produit['couleur'];
                $total += $prix * $quantity;


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
                        <input type="hidden" name="numCommande" value="' . $produit['numCommande'] . '" readonly>
                        <input type="hidden" name="refProduit" value="' . $produit['refProduit'] . '" readonly>
                        <input type="hidden" name="couleur" value="' . $produit['couleur'] . '" readonly>
                        <div class="quantities">
                            <label for="quantity">Quantity: &nbsp; </label>
                            <span>' . $quantity . '&nbsp;</span>
                        </div>
                        <div class="price">
                            <label for="price">Price:</label>
                            <span> ' . $prix . '</span>
                            <input type="hidden" name="price" value="' . $prix . '" readonly>
                            <br>
                            <label for="total">Total: </label>
                            <span class="totalValue">' . $quantity * $prix . ' </span>
                        </div>
                        <div class="buttons">
                            <input type="submit" id="submit" name="submit" class="saveButton" style="display: none;" value="Save">
                            <input type="button" class="cancelButton" style="display: none;" value="Cancel" onclick="resetForm(this)">
                        </div>
                    </div>
                </div>';
            }
            echo '<div class="total">
            <h2>Total: ' . $total . '</h2>
            </div>
            </div>';
        }
        ?>


        <div class="adresse">

            <!--Adresse facturation si elle existe-->
            <form class="no"  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <label for="existingAddress">Sélectionnez une adresse :</label>
                <select name="existingAddress" id="existingAddress" onchange="toggleNewAddress()" required>
                    <?php foreach ($userDefaultAddress as $address) : ?>
                        <option name="default" value="<?php echo $address['idAdresse']; ?>" selected>
                            <?php echo "{$address['rue']}, {$address['ville']}, {$address['codePostal']}, {$address['pays']}"; ?>
                        </option>
                    <?php endforeach; ?>
                    <option value="">+ Ajouter une nouvelle adresse</option>
                </select>

                <br>

                <!-- Option pour entrer une nouvelle adresse -->
                <div id="newAddressDiv">
                    <h3>Entrez une nouvelle adresse :</h3>
                    <label for="newRue">Rue :</label>
                    <input type="text" name="newAddress[rue]" id="newRue">

                    <br>

                    <label for="newVille">Ville :</label>
                    <input type="text" name="newAddress[ville]" id="newVille">

                    <br>

                    <label for="newCodePostal">Code postal :</label>
                    <input type="text" name="newAddress[codePostal]" id="newCodePostal">

                    <br>

                    <label for="newComplement">Complément d'adresse :</label>
                    <input type="text" name="newAddress[complement]" id="newComplement">

                    <br>

                    <label for="newPays">Pays :</label>
                    <input type="text" name="newAddress[pays]" id="newPays" value="France">

                    <br>

                    <input type="submit" value="Procéder au paiement">
                </div>
            </form>

            <div class="Modes payement">

            </div>

        </div>

    </div>
    <?php
    include_once("include/footer.php");
    ?>

    <div class="adresse">
        <!-- I want a form with 2 options either we use the default address of the client or the client enters a new address-->

    </div>





    <script src="./js/panier.js"></script>
</body>


</html>