<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/admin.css">
  
  <link rel="icon" href="include/logoRond.png" type="image/x-icon">
  <title>Ajout Comopsition</title>
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
      <h2>Ajout Composition</h2>
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
      <select class="input" name="categ" id="categ" required>
        <?php
        $sql5 = "SELECT categorie FROM Categories WHERE parent IS NOT NULL";
        $pdostat5 = $conn->prepare($sql5);
        $pdostat5->execute();
        foreach ($pdostat5 as $categ) {
            $categorie = htmlspecialchars($categ['categorie'], ENT_QUOTES, 'UTF-8');
            echo "<option value='" . $categorie . "' data-value='" . $categorie . "'>" . $categorie . "</option>";
        }
        ?>
      </select>
        <label class="label" for="categ">Categorie</label>
      </div>
      <br>
      <div class="input-group">
      <select class="input" name="color" id="color" required>
        <?php 
         $sql4 = "SELECT couleur FROM Coloris";
         $pdostat4 = $conn->prepare($sql4);
         $pdostat4->execute();
          foreach ($pdostat4 as $couleurs) {
            echo "<option value=" . $couleurs['couleur'] . ">" . $couleurs['couleur'] . "</option>";
          }
        ?>
        </select>
        <label class="label" for="color">Couleur</label>
      </div>
      <br>
      <div class="input-group">
        <input class="input" type="text" name="descrip" id="descrip" required>
        <label class="label" for="descrip">Description</label>
      </div>
      <br>
      <div class="input-group">
        <input class="input" type="text" name="prixO" id="prixO"  required>
        <label class="label" for="prixO">Prix Original</label>
      </div>
      <br>
      <div class="input-group">
        <input class="input" type="text" name="prixA" id="prixA"  required>
        <label class="label" for="prixA">Prix Actuel</label>
      </div>
      <br>
      <div class="input-group">
        <input class="input" type="text" name="stock" id="stock" required>
        <label class="label" for="stock">Nombre de produits en stock</label>
      </div>
      <br>
      <div class="input-group">
        <input class="input" type="file" name="image" required>
        <label class="label" for="image">Image</label>
      </div>
      <button type="submit" name="submit" id='submit'>Ajouter</button>
    </form>
    <?php
    if (isset($_POST['ref']) && isset($_POST['nom']) && isset($_POST['categ']) && isset($_POST['color']) && isset($_POST['descrip']) && isset($_POST['prixO']) && isset($_POST['prixA']) && isset($_POST['stock']) && isset($_POST['submit'])) {
      $uploadedFilePath = $_FILES['image']['tmp_name'];
      // Obtenez les informations sur le fichier
      $infoFichier = pathinfo($_FILES['image']['name']);
      // Récupérez l'extension du fichier
      $extension = $infoFichier['extension'];
      // Extensions autorisées
      $extensionsAutorisees = array('jpg');

      $sql6 = "SELECT reference FROM ProduitsFinaux";
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
        if (preg_match('/^.{1,40}$/', $_POST['nom'])) {
            if (preg_match('/^.{1,250}$/', $_POST['descrip'])) {
              if (preg_match('/^(?:\d{1,4}(?:\.\d{1,2})?|\d{1,4})$/', $_POST['prixO'])){
                if (preg_match('/^(?:\d{1,4}(?:\.\d{1,2})?|\d{1,4})$/', $_POST['prixA'])){
                  if (preg_match('/^\d{1,}$/',  $_POST['stock'])) {
                    if (in_array($extension, $extensionsAutorisees)) { 
                      $newFileName = $_POST['ref'] . '.jpg';
                      // Chemin où le fichier doit être enregistré
                      $destination = 'imgProduits/' . $newFileName;
                      
                      // Vérifier si le fichier existe déjà
                      if (file_exists($destination)) {
                          // Supprimer le fichier existant
                          unlink($destination);
                      }
                      
                      // Copier le fichier téléchargé vers son nouvel emplacement avec le nouveau nom
                      copy($uploadedFilePath, $destination);
                      
                      // Supprimer le fichier temporaire après avoir fait la copie
                      unlink($uploadedFilePath);
                      
                      $ref = $_POST['ref'];
                      $nom = $_POST['nom'];
                      $categorie = $_POST['categ']; 
                      $sql1 = "INSERT INTO ProduitsFinaux VALUES ('$ref', '$nom', '$categorie')";
                      $pdostat1 = $conn->prepare($sql1);
                      $pdostat1->execute();
                      $des = $_POST['descrip'];
                      $prixO = $_POST['prixO'];
                      $prixA = $_POST['prixA'];
                      $sto = $_POST['stock'];
                      $col = $_POST['color'];
                      $sql3 = "INSERT INTO Coloriser VALUES ('$ref', '$col', '$des', '$prixO', '$prixA', '$sto')";
                      $pdostat3 = $conn->prepare($sql3);
                      $pdostat3->execute();
                      echo '<script>
                      window.alert("Ajout effectuée");

                      window.location.href = "index.php";
                      </script>';

                    }
                    else{
                        echo "<br>Le fichier doit être au format jpg";
                    }
                    

                  }
                  else{
                    echo " <br>Le stock est trop élevé";
                  }
                }
                else{
                  echo " <br>Le prix Actuel est trop élevé ou ne correspond pas à la forme ci contre : 25.15 ";
                }
              }
              else{
                echo " <br>Le prix Original est trop élevé ou ne correspond pas à la forme ci contre : 25.15 ";
              }
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
        echo " <br>La référence du produit existe déjà ou est trop longue (plus de 40 caractères).";
      }
      
    }
    ?>
  </div>
</body>
</html>