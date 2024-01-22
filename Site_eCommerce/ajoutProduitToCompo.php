<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/admin.css">
  <title>Ajout Produit To Compo</title>
    <link rel="icon" href="include/logoRond.png" type="image/x-icon">
</head>

<body>
  <?php
  session_start();
  include_once("include/connect.inc.php");
  if (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];
    $sql3 = "SELECT DISTINCT  isAdmin FROM Clients WHERE pseudo = '$login'";
    $pdostat3 = $conn->prepare($sql3);
    $pdostat3->execute();
    $statut = $pdostat3->fetch(PDO::FETCH_ASSOC);
    if ($statut['isAdmin'] == "true") {}
  else{
    echo '<script type="text/javascript">window.location.replace("index.php");</script>';
  }
  }
  else {
    echo '<script type="text/javascript">window.location.replace("index.php");</script>';
  }

  ?>
  <div class="global-container">
  <a href="index.php" id="maly"> MALYART </a>
  <form action="" method="POST" id='ajout-form' enctype="multipart/form-data">
      <h2>Ajouter un produit à une Composition</h2>
      <br>
      <div class="input-group">
        <input class="input" type="text" name="ref" id="ref" required>
        <label class="label" for="ref">Reference</label>
      </div>
      <br>
      <div class="input-group">
        <input class="input" type="text" name="nom" id="nom" required>
        <label class="label" for="nom">Nom</label>
      </div>
      <br>
      <div class="input-group">
        <input class="input" type="text" name="descrip" id="descrip" required>
        <label class="label" for="descrip">Description</label>
    </div>
    <br>
      <div class="input-group">
        <input class="input" type="text" name="refCompo" id="refCompo" required>
        <label class="label" for="refCompo">Reference de la composition</label>
      </div>
      <button type="submit" name="submit" id='submit'>Ajouter</button>
    </form>
    <?php
    if (isset($_POST['ref']) && isset($_POST['refCompo']) &&  isset($_POST['nom']) && isset($_POST['descrip']) && isset($_POST['submit'])) {
      $sql6 = "SELECT reference FROM Produits";
      $pdostat6 = $conn->prepare($sql6);
      $pdostat6->execute();
      $referenceIdentique = false;
      $r = $_POST['ref'];
      foreach ($pdostat6 as $ref) {
        if ($ref['reference'] == $r){
          $referenceIdentique = true;
          break;
        }
      }
      if ($referenceIdentique == false) {
        $sql10 = "SELECT reference FROM ProduitsFinaux";
        $pdostat10 = $conn->prepare($sql10);
        $pdostat10->execute();
        $referenceExist = false;
        $f = $_POST['refCompo'];
        foreach ($pdostat10 as $ref) {
          if ($ref['reference'] == $f){
            $referenceExist = true;
            break;
          }
        }
        if ($referenceExist == true) {
        if (preg_match('/^.{1,40}$/', $_POST['nom'])) {
            if (preg_match('/^.{1,250}$/', $_POST['descrip'])) {
                      $ref = $_POST['ref'];
                      $nom = $_POST['nom'];
                      $descrip = $_POST['descrip'];
                      $sql2 = "INSERT INTO Produits VALUES ('$ref', '$nom', '$descrip')";
                      $pdostat2 = $conn->prepare($sql2);  
                      $pdostat2->execute();
                      $refCompo = $_POST['refCompo'];
                      $sql7 = "INSERT INTO Composition VALUES ('$ref', '$refCompo')";
                      $pdostat7 = $conn->prepare($sql7);
                      $pdostat7->execute(); 
                      echo '<script>
                      window.alert("Ajout effectuée");

                      window.location.href = "index.php";
                      </script>';
                 
            }
            else {
              echo " <br>La description est trop long (plus de 250 caractères).";
            } 
        } 
        else {
          echo " <br>Le nom est trop long (plus de 40 caractères).";
        }
      }
      else{
        echo " <br>La référence de la composition n'existe pas.";
      }
      }
      else{
        echo " <br>La référence du produit existe déjà ou est trop longue (plus de 40 caractères).";
      }
      
    }
    ?>
  </div>
</body>
</html>