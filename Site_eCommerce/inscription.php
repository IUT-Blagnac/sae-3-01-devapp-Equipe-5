<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription</title>
  <link rel="stylesheet" href="./css/register.css">
  <script src="./js/functions.js"></script>
</head>

<body>
  <div class="Inscription">
  <a href="index.php" id="maly"> MALYART </a>
    <form action="traitementInscription.php" method="POST" id='form' onsubmit="return validateForm()">
      <h1 class="center">Formulaire d'inscription</h1>
      <div class="fields">
        <div class="column">
          <h3>Informations personnelles</h3>
          <label for="nom">Nom :</label>
          <input type="text" id="nom" name="nom" required>

          <label for="prenom">Prénom :</label>
          <input type="text" id="prenom" name="prenom" required>

          <label for="mail">Adresse Mail :</label>
          <input type="text" id="mail" name="mail" required>

          <label for="tel">Téléphone :</label>
          <input type="text" id="tel" name="tel" required>

          <label for="dtN">Date de naissance :</label>
          <input type="date" id="dtN" name="dtN" required>

        </div>
        <!-- PARTIE ADRESSES 
        <div class="column">
          <h3>Adresse </h3>
          <label for="input8">Rue :</label>
          <input type="text" id="input8" name="input8">

          <label for="input9">Ville :</label>
          <input type="text" id="input9" name="input9">

          <label for="input10">Code postal</label>
          <input type="number" id="input10" name="input10">

          <label for="input11">Complément :</label>
          <input type="text" id="input11" name="input11">

          <label for="input12">Pays :</label>
          <input type="text" id="input12" name="input12">
        </div>
        -->
        <div class="column">
          <h3>Informations de connexion</h3>
          <label for="username">Nom d'utilisateur :</label>
          <input type="text" id="username" name="username" required>

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