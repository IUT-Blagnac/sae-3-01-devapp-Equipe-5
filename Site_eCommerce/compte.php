<?php
session_start();

require_once('include/connect.inc.php');

if (!isset($_SESSION["login"])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['erreur'])) {
  switch ($_GET['erreur']) {
    case 'sql':
      echo "<script language='JavaScript' type='text/javascript'>
          alert('Erreur SQL');
          </script>";
      break;
    case 'mdp':
      echo "<script language='JavaScript' type='text/javascript'>
          alert('Le mot de passe doit contenir au moins 8 caractères dont une majuscule, une minuscule et un chiffre');
          </script>";
      break;
    case 'mail':
      echo "<script language='JavaScript' type='text/javascript'>
          alert('L\'adresse mail n\'est pas valide');
          </script>";
      break;
    case 'tel':
      echo "<script language='JavaScript' type='text/javascript'>
          alert('Le numéro de téléphone n\'est pas valide');
          </script>";
      break;
    case 'username':
      echo "<script language='JavaScript' type='text/javascript'>
          alert('Le nom d\'utilisateur est déjà utilisé');
          </script>";
      break;
    case 'mailUsed':
      echo "<script language='JavaScript' type='text/javascript'>
          alert('L\'adresse mail est déjà utilisée');
          </script>";
      break;
    case 'telUsed':
      echo "<script language='JavaScript' type='text/javascript'>
          alert('Le numéro de téléphone est déjà utilisé');
          </script>";
      break;
    case 'dtN':
      echo "<script language='JavaScript' type='text/javascript'>
          alert('La date de naissance n\'est pas valide');
          </script>";
      break;
    default:
      echo "<script language='JavaScript' type='text/javascript'>
          alert('Erreur inconnue, veuillez réessayer! ');
          </script>";
      break;
  }
}

$request = $conn->prepare('SELECT * FROM Clients, Adresses WHERE idClient = :idClient');
$request->bindParam(':idClient', $_SESSION['id']);
$request->execute();
$userInfo = $request->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="./css/voirCompte.css">
    <title>Mon compte</title>
    <script src="./js/functions.js"></script>
</head>
<body>
    <?php 
        include_once("./include/header.php");
    ?>    

    <br>
    <h1 class="title">Mon compte</h1>
    <div class="form">
        <div class="fields">
          <div class="column">
                <form action="traitementCompte.php" method="POST">
                    <label for="nomC">Nom :</label> <br>
                    <input type="text" id="nomC" name="nom" value="<?php echo $userInfo['nom']; ?>" required> <br>
                
                    <label for="prenom">Prénom :</label> <br>
                    <input type="prenom" id="prenom" name="prenom" value="<?php echo $userInfo['prenom']; ?>" required> <br>
                
                    <label for="mail">Adresse Mail :</label> <br>
                    <input type="text" id="mail" name="mail" value="<?php echo $userInfo['adresseMail']; ?>" required> <br>
                
                    <label for="tel">Téléphone :</label> <br>
                    <input type="text" id="tel" name="tel" value="<?php echo $userInfo['tel']; ?>" required> <br>
                
                    <label for="dtN">Date de naissance :</label> <br>
                    <input type="date" id="dtN" name="dtN" value="<?php echo $userInfo['dateNaissance']; ?>" required> <br>
                </div>
            
                <div class="column">
                    <label for="rue">Rue :</label> <br>
                    <input type="text" id="rue" name="rue" value="<?php echo $userInfo['rue']; ?>" <br>
                
                    <label for="ville">Ville :</label> <br>
                    <input type="text" id="ville" name="ville" value="<?php echo $userInfo['ville']; ?>" <br>
                
                    <label for="codeP">Code postal :</label> <br>
                    <input type="text" id="codeP" name="codeP" value="<?php echo $userInfo['codePostal']; ?>" <br>
                
                    <label for="compl">Complément :</label> <br>
                    <input type="text" id="compl" name="compl" value="<?php echo $userInfo['complement']; ?>" <br>
                
                    <label for="pays">Pays :</label> <br>
                    <input type="text" id="pays" name="pays" value="<?php echo $userInfo['pays']; ?>" <br>
                
                    <label for="username">Nom d'utilisateur :</label> <br>
                    <input type="text" id="username" name="username" value="<?php echo $userInfo['pseudo']; ?>" required> <br>
                
                    <button type="submit" onclick="submit_form();" id="confirmer">Confirmer</button>
                </form>
            </div>
        </div>
    </div>


    <br><br><br>
    <?php
        include_once("./include/footer.php");
    ?>
</body>
</html>
