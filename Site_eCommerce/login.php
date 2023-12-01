<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/login.css">
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
  <div id='login'>
  <a href="index.php" id="maly"> MALYART </a>

    <form action="traitementLogin.php" method="POST" id='form'>
      <h2>Connexion</h2>
      <input type="text" name="login" id="login" placeholder="Username" required <?php if (!empty($_COOKIE["Login"])) {
                                                                                    echo "value=" . $_COOKIE["Login"] . "";
                                                                                  } ?>>
      <input type="password" name="password" id="password" placeholder="Password" required>
      <label for="remember">Se souvenir de moi</label>
      <input type="checkbox" name="remember" id="remember" value="remember">
      <input type="submit" value="Connexion">
      <p>Pas encore de compte ? <a href="inscription.php">Inscrivez-vous</a></p>

    </form>
    <br>
  </div>
</body>
</html>