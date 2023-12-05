<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/compte.css">
  <title>Login Page</title>
</head>

<body>
  <?php
  if (isset($_GET['erreur']) && $_GET['erreur'] == true) {
    echo "<script language='JavaScript' type='text/javascript'>
          alert('Wrong LOGIN or PASSWORD');
          </script>";
  }

  ?>
  <div class="global-container">
  <a href="index.php" id="maly"> MALYART </a>

    <form action="traitementLogin.php" method="POST" id='login-form' >
      <h2>Connexion</h2>
      <input type="text" name="login" id="login" placeholder="Username" required <?php if (!empty($_COOKIE["Login"])) {
                                                                                    echo "value=" . $_COOKIE["Login"] . "";
                                                                                  } ?>>
      <input type="password" name="password" id="password" placeholder="Password" required>
      <div class="remember-me">
      <label for="remember" class="remember-me">Se souvenir de moi</label>
      <input type="checkbox" name="remember" id="remember" value="remember">
      </div>
      <button type="submit" id='submit'>Connexion</button>
      <p>Pas encore de compte ? <a href="inscription.php">Inscrivez-vous</a></p>
    </form>
    <br>
  </div>
</body>
</html>