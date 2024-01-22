<?php

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
    case 'usernameUsed':
      echo "<script language='JavaScript' type='text/javascript'>
          alert('Le nom d\'utilisateur est déjà utilisé');
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription</title>
  <link rel="stylesheet" href="./css/compte.css">
  <script src="./js/register.js"></script>
    <link rel="icon" href="include/logoRond.png" type="image/x-icon">
</head>

<body>
  <div class="global-container">
    <a href="index.php" id="maly"> MALYART </a>
    <form action="traitementInscription.php<?php if (isset($_GET['redirect'])) echo "?redirect=" . $_GET['redirect']; ?>" method="POST" id='register-form' onsubmit="return validateForm()">
      <h1 class="center">Formulaire d'inscription</h1>
      <div class="fields">
        <div class="column">
          <h3>Informations personnelles</h3>
          <label for="nom">Nom :</label>
          <input type="text" id="nom" name="nom" required maxlength="40">

          <label for="prenom">Prénom :</label>
          <input type="text" id="prenom" name="prenom" required maxlength="40">

          <label for="mail">Adresse Mail :</label>
          <input type="text" id="mail" name="mail" required maxlength="40">

          <label for="tel">Téléphone :</label>
          <input type="text" id="tel" name="tel" required maxlength="15">

          <label for="dtN">Date de naissance :</label>
          <input type="date" id="dtN" name="dtN" required>

        </div>
        <div class="column">
          <h3>Adresse </h3>
          <br>
          <label for="rue">Rue :</label>
          <input type="text" id="rue" name="rue" required maxlength="40">

          <label for="ville">Ville :</label>
          <input type="text" id="ville" name="ville" required maxlength="40">

          <label for="codePostal">Code postal</label>
          <input type="number" id="codePostal" name="codePostal" required min="1" max="100000">

          <label for="complement">Complément :</label>
          <input type="text" id="complement" name="complement" maxlength="100">

          <label for="pays">Pays :</label>
          <input type="text" id="pays" name="pays" value="France" required maxlength="40">
        </div>

        <div class="column">
          <h3>Informations de connexion</h3>
          <label for="username">Nom d'utilisateur :</label>
          <input type="text" id="username" name="username" required maxlength="20">

          <label for="password">Mot de passe :</label>
          <input type="password" id="password" name="password" required>

          <label for="ConfirmPassword">Confirmer le mot de passe :</label>
          <input type="password" id="ConfirmPassword" name="ConfirmPassword" required>
        </div>
        <button type="submit" onclick="submit_form();">S'inscrire</button>
        <p class="center">Déjà un compte ? <a href="login.php">Connectez-vous</a></p>
    </form>
  </div>


</body>

</html>