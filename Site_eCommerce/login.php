<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/compte.css">
  <title>Connexion</title>
    <link rel="icon" href="include/logoRond.png" type="image/x-icon">
</head>

<body>
  <?php
  if (isset($_GET['erreur']) && $_GET['erreur'] == true) {
    echo "<script language='JavaScript' type='text/javascript'>
          alert('Wrong LOGIN or PASSWORD');
          </script>";
  }

  if (isset($_GET['redirect']) && isset($_GET['couleur'])) {
    $redirect = $_GET['redirect'] . "&couleur=".  $_GET['couleur'];
    
  } else if (isset($_GET['redirect']) && !isset($_GET['couleur'])) {
    $redirect = $_GET['redirect'];
  } 

  
  ?>
  <div class="global-container">
    <a href="index.php" id="maly"> MALYART </a>
    <!--Redirection modulaire -->
    <form action="traitementLogin.php<?php if (isset($redirect)) echo "?redirect=" . $redirect; ?>" method="POST" id='login-form' >
      <h2>Connexion</h2>
      <input type="text" name="login" id="login" placeholder="Username" required 
      <?php if (!empty($_COOKIE["Login"])) {
        echo "value=" . $_COOKIE["Login"] . "";
        } 
      ?>>
      <input type="password" name="password" id="password" placeholder="Password" required>
      <div class="remember-me">
      <label for="remember" class="remember-me">Se souvenir de moi</label>
      <input type="checkbox" name="remember" id="remember" value="remember">
      </div>
      <button type="submit" id='submit'>Connexion</button>
      <p>Pas encore de compte ? <a href="inscription.php<?php if (isset($_GET['redirect'])) echo "?redirect=" . $_GET['redirect']; ?>">Inscrivez-vous</a></p>
    </form>
    <br>
  </div>
</body>
</html>