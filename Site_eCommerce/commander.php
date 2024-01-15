<?php
session_start();
//check si l'utilisateur est connecté sinon le redirige vers la page de connexion
require_once('include/loginCheck.php');
require_once('include/connect.inc.php');
#si pas de get numcommande renvoi vers page d'avant ça peut être la page de compte ou la page panier
if (!isset($_GET['numCommande']) && !isset($_SESSION['id'])) {
    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
}
if (!isset($_GET['numCommande'])) {
    header('Location: panier.php');
}

//check si le statut de la commande est panier, sinon tous les boutons sont désactivés et le selecteur d'adresse est désactivé

$statut = "";
try {
    $request = $conn->prepare('SELECT statut FROM Commande WHERE numero = :numCommande');
    $request->bindParam(':numCommande', $_GET['numCommande']);
    $request->execute();
    $statut = $request->fetch()['statut'];
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage() . "<BR>";
    die();
}

//echo '<script>console.log("Statut: ' . $statut . '")</script>';


if (isset($_POST['rue']) && isset($_POST['ville']) && isset($_POST['codePostal']) && isset($_POST['complement']) && isset($_POST['pays'])) {

    
    //recuperer les valeurs des champs avec htmlentities avec utf8
    $numCommande = htmlentities($_GET['numCommande'], ENT_QUOTES | ENT_HTML5, "UTF-8");
    $rue = htmlentities($_POST['rue'], ENT_QUOTES | ENT_HTML5, "UTF-8");
    $ville = htmlentities($_POST['ville'], ENT_QUOTES | ENT_HTML5, "UTF-8");
    $codePostal = htmlentities($_POST['codePostal'], ENT_QUOTES | ENT_HTML5, "UTF-8");
    $complement = htmlentities($_POST['complement'], ENT_QUOTES | ENT_HTML5, "UTF-8");
    $pays = htmlentities($_POST['pays'], ENT_QUOTES | ENT_HTML5, "UTF-8");


    //regex pour verifier que les champs sont remplis correctement (complement n'est pas obligatoire)
    $regexRue = "/^[a-zA-Z0-9\s]{1,40}$/";
    $regexVille = "/^[a-zA-Z\s]{1,40}$/";
    $regexCodePostal = "/^[0-9]{5}$/";
    $regexComplement = "/^[a-zA-Z0-9\s]{0,40}$/";
    $regexPays = "/^[a-zA-Z\s]{1,40}$/";


    if (preg_match($regexRue, $rue) && preg_match($regexVille, $ville) && preg_match($regexCodePostal, $codePostal) && preg_match($regexComplement, $complement) && preg_match($regexPays, $pays)) {
        // Appel de la procédure stockée
        $request = $conn->prepare('CALL UpdateCommandeAdresse(:idClient, :numCommande, :rue, :ville, :codePostal, :complement, :pays, :existingAddress)');
        $request->bindParam(':idClient', $_SESSION['id']);
        $request->bindParam(':numCommande', $numCommande);
        $request->bindParam(':rue', $rue);
        $request->bindParam(':ville', $ville);
        $request->bindParam(':codePostal', $codePostal);
        $request->bindParam(':complement', $complement);
        $request->bindParam(':pays', $pays);
        $request->bindParam(':existingAddress', $_POST['existingAddress']);
        try {
            $request->execute();
            echo '<script>alert("Adresse sauvegardée")</script>';
        } catch (PDOException $e) {
            echo "Erreur: " . $e->getMessage() . "<BR>";
            echo '<script>alert("Erreur: ' . $e->getMessage() . '")</script>';
            die();
        }
    } else {
        echo '<script>alert("Veuillez remplir tous les champs correctement")</script>';
    }
}



// ID de l'utilisateur 
$userId = $_SESSION['id'];

// Inclure votre logique de connexion à la base de données ici
require_once("include/connect.inc.php");



// Fonction pour obtenir les adresses de l'utilisateur depuis la base de données
function getUserAddresses($userId, $conn)
{
    // recuperer les adresses de la commande avec status panier et de l'utilisateur 
    $request1 = $conn->prepare('SELECT DISTINCT Adresses.* FROM Adresses, Commande, Clients WHERE (Adresses.idAdresse = Commande.adrLivraison AND Commande.idClient = :idClient AND Commande.statut = "panier") OR (Adresses.idAdresse= Clients.adresse AND Clients.idClient = :idClient)');
    $request1->bindParam(':idClient', $userId);
    $request1->execute();
    $userAddresses = $request1->fetchAll();
    return $userAddresses;
}



// Récupérer l'adresse par défaut de l'utilisateur depuis la base de données
$userDefaultAddress = getUserAddresses($userId, $conn);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Commander</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="./css/panier.css">
    <style>

    </style>
    <link rel="icon" href="include/logoRond.png" type="image/x-icon">

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
            $request1 = $conn->prepare('SELECT * FROM Commande,Panier, ProduitsFinaux, Coloriser where Commande.numero=Panier.numCommande and Panier.refProduit = ProduitsFinaux.reference and Coloriser.refProduit = ProduitsFinaux.reference and idClient = :idClient and Panier.couleur=Coloriser.couleur  and Commande.numero = :numCommande ');
            $request1->bindParam(':idClient', $_SESSION['id']);
            $request1->bindParam(':numCommande', $_GET['numCommande']);
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
            echo '<h2>Numéro de ma commande:  ' . htmlentities($_GET["numCommande"]) . '</h2>';
            echo '<h3> Statut de ma commande: ' . $statut . '</h3>';
            foreach ($request1 as $produit) {
                $reference = $produit['refProduit'];
                $quantity = $produit['quantite'];
                $prix = $produit['prixActuel'];
                $nom = $produit['nom'];
                $couleur = $produit['couleur'];
                $total += $prix * $quantity;

                //si le produit n'a pas assez de stock on affiche un message et on desactive le bouton de commande
                if ($produit['nbStock'] < $quantity) {
                    echo '<script>alert("Le produit ' . $nom . ' n\'a pas assez de stock (Stock disponible = ' . $produit['nbStock'] . ')");
                    document.getElementById("submit").disabled = true;
                    window.location.href = "panier.php";
                    </script>
                    ';
                }



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
                            <label for="quantity">Quantité: &nbsp; </label>
                            <span>' . $quantity . '&nbsp;</span>
                        </div>
                        <div class="price">
                            <label for="price">Prix unitaire:</label>
                            <span> ' . $prix . '€</span>
                            <input type="hidden" name="price" value="' . $prix . '" readonly>
                            <br>
                            <label for="total">Total: </label>
                            <span class="totalValue">' . $quantity * $prix . ' €</span>
                        </div>
                        <div class="buttons">
                            <input type="submit" id="submit" name="submit" class="saveButton" style="display: none;" value="Save">
                            <input type="button" class="cancelButton" style="display: none;" value="Cancel" onclick="resetForm(this)">
                        </div>
                    </div>
                </div>';
            }
            echo '<div class="total">
            <h2>Total: ' . $total . '€</h2>
            </div>
            </div>';
        }
        ?>


        <div id="addresseDiv" class="ChoixAdresse">
            <!--Adresse facturation si elle existe-->
            <form id="addForm" class="no" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?" . $_SERVER['QUERY_STRING'];; ?>" method="post">
                <label for="existingAddress">Sélectionnez une adresse :</label>
                <select name="existingAddress" id="existingAddress" onchange="toggleNewAddress()" required>
                    <?php foreach ($userDefaultAddress as $address) : ?>
                        <option name="default" value="<?php echo $address['idAdresse']; ?>">
                            <?php echo "{$address['rue']}, {$address['ville']}, {$address['codePostal']}, {$address['pays']}"; ?>
                        </option>
                    <?php endforeach; ?>
                    <option name="other" value="">+ Ajouter une nouvelle adresse</option>
                </select>

                <br>

                <!-- Option pour entrer une nouvelle adresse -->
                <div id="newAddressDiv">
                    <h3>Entrez une nouvelle adresse :</h3>
                    <label for="rue">Rue :</label>
                    <input type="text" id="rue" name="rue" required maxlength="40">
                    <BR>
                    <label for="ville">Ville :</label>
                    <input type="text" id="ville" name="ville" required maxlength="40">
                    <BR>
                    <label for="codePostal">Code postal</label>
                    <input type="number" id="codePostal" name="codePostal" required min="1" max="100000">
                    <BR>
                    <label for="complement">Complément :</label>
                    <input type="text" id="complement" name="complement" maxlength="100">
                    <BR>
                    <label for="pays">Pays :</label>
                    <input type="text" id="pays" name="pays" value="France" required maxlength="40">
                    <br>

                    <input name="submit" type="submit" value="Sauvegarder adresse" form="addForm">
                </div>
            </form>
        </div>

        <form id="commande" action="traitementCommande.php?numCommande=<?php echo $_GET['numCommande']?>" method="post">
            <div class="radio">
                <h2>Choisissez votre mode de paiement :</h2>
                <div>
                    <input type="radio" id="CB" name="paiement" value="CB" checked>
                    <label for="CB">Carte Bancaire</label>
                </div>
                <div>
                    <input type="radio" id="Paypal" name="paiement" value="Paypal">
                    <label for="Paypal">Paypal</label>
                </div>
            </div>
            <div class="Modes_paiement">

                <div class="CB">
                    <h3>Carte Bancaire</h3>

                    <label for="nom">Nom du titulaire de la carte :</label>
                    <input type="text" id="nomTitulaire" name="nom" required maxlength="40">
                    <BR>
                    <label for="prenom">Prénom du titulaire de la carte :</label>
                    <input type="text" id="prenom" name="prenom" required maxlength="40">
                    <BR>
                    <label for="numCarte">Numéro de la carte :</label>
                    <input type="text" id="numCarte" name="numCarte" pattern="\d{4}\s?\d{4}\s?\d{4}\s?\d{4}" required>
                    <BR>
                    <label for="dateExp">Date d'expiration :</label>
                    <input type="date" id="dateExp" name="dateExp" required>
                    <BR>
                    <label for="codeSecu">Code de sécurité :</label>
                    <input type="text" id="codeSecu" name="codeSecu" pattern="[0-9]{3}" title="3 chiffres sont requis" required>
                    <BR>

                </div>

                <!-- A remplacer par un bouton paypal car pas sécurisé dutout-->
                <div class="Paypal">
                    <h3>Paypal</h3>
                    <label for="mail">Adresse Mail :</label>
                    <input type="email" id="mail" name="mail" maxlength="40" pattern="+@+.+" >
                    <BR>
                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password">
                    <BR>
                </div>

            </div>
            <input type="hidden" name="numCommande" value="<?php echo htmlentities($_GET["numCommande"]); ?>" readonly>
            <input type="hidden" name="total" value="<?php echo $total; ?>" readonly>
            <input type="hidden" name="statut" value="<?php echo $statut; ?>" readonly>
            <input type="hidden" id="idAdresseFinal" name="idAdresse" value="<?php echo $userDefaultAddress[0]['idAdresse']; ?>" readonly>
            <input id="btnPay" name="submit" type="submit" value="Payer" form="commande">

        </form>


    </div>

    </div>
    <?php
    if ($statut != "panier") {
        echo '<script>
        document.getElementById("submit").disabled = true;
        
        document.getElementById("addresseDiv").style.display = "none";
        document.getElementById("commande").style.display = "none";
        document.getElementById("btnPay").style.display = "none";


        </script>';
    }

    include_once("include/footer.php");
    ?>
    <script>
        document.getElementById('commande').addEventListener('submit', function(event) {
            var creditCardInput = document.getElementById('creditCard');
            var creditCardRegex = /^\d{4}\s?\d{4}\s?\d{4}\s?\d{4}$/;

            if (!creditCardRegex.test(creditCardInput.value)) {
                alert('Invalid credit card number. Please enter a valid credit card number.');
                event.preventDefault();
            }
        });

        


        function toggleNewAddress() {
            const newAddressDiv = document.getElementById("newAddressDiv");
            const existingAddress = document.getElementById("existingAddress");
            if (existingAddress.value === "") {
                newAddressDiv.style.display = "block";
            } else {
                newAddressDiv.style.display = "none";
            }

            //if the selected option is not "new address", put the value of the selected adress in the hidden input field idAdresseFinal

            console.log(existingAddress.value);
            if (existingAddress.value !== "") {
                document.getElementById("idAdresseFinal").value = existingAddress.value;
            }
        }

        var cbRadio = document.getElementById('CB');
        var paypalRadio = document.getElementById('Paypal');
        var cbDiv = document.querySelector('.CB');
        var paypalDiv = document.querySelector('.Paypal');

        function updatePaymentMode() {
            if (cbRadio.checked) {
                //change the display property to block

                paypalDiv.style.display = 'none';
                cbDiv.style.display = 'block';
                //remove the required attribute from the paypal inputs
                document.getElementById('mail').removeAttribute('required');
                document.getElementById('password').removeAttribute('required');

                //add the required attribute to the credit card inputs
                document.getElementById('nomTitulaire').setAttribute('required', 'required');
                document.getElementById('prenom').setAttribute('required', 'required');
                document.getElementById('numCarte').setAttribute('required', 'required');
                document.getElementById('dateExp').setAttribute('required', 'required');
                document.getElementById('codeSecu').setAttribute('required', 'required');



            } else if (paypalRadio.checked) {
                //change the display property to none
                cbDiv.style.display = 'none';
                paypalDiv.style.display = 'block';

                //add the required attribute to the paypal inputs
                document.getElementById('mail').setAttribute('required', 'required');
                document.getElementById('password').setAttribute('required', 'required');

                //remove the required attribute from the credit card inputs
                document.getElementById('nomTitulaire').removeAttribute('required');
                document.getElementById('prenom').removeAttribute('required');
                document.getElementById('numCarte').removeAttribute('required');
                document.getElementById('dateExp').removeAttribute('required');
                document.getElementById('codeSecu').removeAttribute('required');

            }
        }

        cbRadio.addEventListener('change', updatePaymentMode);
        paypalRadio.addEventListener('change', updatePaymentMode);

        // Initial update based on default checked radio button
        updatePaymentMode();
    </script>
    <script src="./js/panier.js"></script>
</body>


</html>