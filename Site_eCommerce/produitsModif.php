<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/admin.css">
  <title>Modification Produit</title>
    <link rel="icon" href="include/logoRond.png" type="image/x-icon">
</head>

<body>
  <?php
  session_start();
  include_once("include/connect.inc.php");
  if (isset($_SESSION['login']) && isset($_GET['reference']) && isset($_GET['couleur'])) {
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
  $ref = htmlentities($_GET['reference']);
  $couleur = htmlentities($_GET['couleur']);
  $sql = "SELECT DISTINCT ProduitsFinaux.reference, ProduitsFinaux.categorie, ProduitsFinaux.nom, Coloriser.prixActuel, Coloriser.prixOriginal, Coloriser.nbStock, Coloriser.description, Produits.aspectTech  FROM ProduitsFinaux, Coloriser, Produits, Composition WHERE ProduitsFinaux.reference = Coloriser.refProduit AND Composition.refProduit = Produits.reference AND Composition.refProduitFinal = ProduitsFinaux.reference AND ProduitsFinaux.reference = '$ref' AND Coloriser.couleur = '$couleur'";
  $pdostat = $conn->prepare($sql);
  $pdostat->execute();
  $ligne = $pdostat->fetch(PDO::FETCH_ASSOC);

  ?>
  <div class="global-container">
  <a href="index.php" id="maly"> MALYART </a>
  <form action="" method="POST" id='modif-form' enctype="multipart/form-data">
      <h2>Modification</h2>
      Reference
      <input type="text" readonly name="ref" id="ref" placeholder="Reference du produit" value="<?php echo $ligne['reference'] ?>" required>
      Nom
      <input type="text" name="nom" id="nom" placeholder="Nom du produit" value="<?php echo $ligne['nom'] ?>" required>
      Aspect Technique
      <input type="text" name="aspect" id="aspect" placeholder="Aspect Technique du produit" value="<?php echo $ligne['aspectTech'] ?>" required>
      Categorie
      <input type="text" name="categ" id="categ" placeholder="Categorie du produit" value="<?php echo $ligne['categorie'] ?>" required>
      Couleur
      <input type="text" readonly name="color" id="color" placeholder="Couleur du produit" value="<?php echo $couleur ?>" required>
      Description
      <input type="text" name="descrip" id="descrip" placeholder="Description du produit" value="<?php echo $ligne['description'] ?>" required>
      Prix non soldé
      <input type="text" name="prixO" id="prixO" placeholder="Prix Non Soldé" value="<?php echo $ligne['prixOriginal'] ?>" required>
      Prix soldé
      <input type="text" name="prixA" id="prixA" placeholder="Prix Soldé" value="<?php echo $ligne['prixActuel'] ?>" required>
      Nombre dans le stock
      <input type="text" name="stock" id="stock" placeholder="Nombre de produit en stock" value="<?php echo $ligne['nbStock'] ?>" required>
      Image
      <input type="image" src="imgProduits/<?php echo $ligne["reference"]?>.jpg">
      Nouvelle image 
      <input type="file" name="image">
      <button type="submit" name="submit" id='submit'>Modifier</button>
    </form>
    <?php
    if (isset($_POST['ref']) && isset($_POST['nom']) && isset($_POST['aspect']) && isset($_POST['categ']) && isset($_POST['color']) && isset($_POST['descrip']) && isset($_POST['prixO']) && isset($_POST['prixA']) && isset($_POST['stock']) && isset($_POST['submit'])) {
      if ($_FILES['image']['size'] != 0) {
        $uploadedFilePath = $_FILES['image']['tmp_name'];
    
        // Obtenez les informations sur le fichier
        $infoFichier = pathinfo($_FILES['image']['name']);
        
        // Récupérez l'extension du fichier
        $extension = $infoFichier['extension'];
    
        // Extensions autorisées
        $extensionsAutorisees = array('jpg');
        
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
        } else {
            echo "<br>Le fichier doit être au format jpg";
        }
    
    }
      if (preg_match('/^.{1,40}$/', $_POST['nom'])) {
        if (preg_match('/^.{1,250}$/', $_POST['aspect'])) {
          $sql5 = "SELECT categorie FROM Categories";
          $pdostat5 = $conn->prepare($sql5);
          $pdostat5->execute();
          $categorieBonne = false;
          $c = $_POST['categ'];
          foreach ($pdostat5 as $categ) {
            if ($categ['categorie'] == $c){
              $categorieBonne = true;
              break;
            }
          }
          if ($categorieBonne == true) {
            $sql4 = "SELECT couleur FROM Coloris";
            $pdostat4 = $conn->prepare($sql4);
            $pdostat4->execute();
            $couleurBonne = false;
            foreach ($pdostat4 as $couleurs) {
              if ($couleurs['couleur'] == $_POST['color']){
                $couleurBonne = true;
                break;
              }
            }
            if ($couleurBonne == true) {
              if (preg_match('/^.{1,250}$/', $_POST['descrip'])) {
                if (preg_match('/^(?:\d{1,4}(?:\.\d{1,2})?|\d{1,4})$/', $_POST['prixO'])){
                  if (preg_match('/^(?:\d{1,4}(?:\.\d{1,2})?|\d{1,4})$/', $_POST['prixA'])){
                    if (preg_match('/^\d{1,}$/',  $_POST['stock'])) {
                      $nom = $_POST['nom'];
                      $categorie = $_POST['categ'];
                      $sql1 = "UPDATE ProduitsFinaux SET nom = :nom, categorie = :categorie WHERE reference = :ref";
                      $pdostat1 = $conn->prepare($sql1);
                      $pdostat1->bindParam(':nom', $nom);
                      $pdostat1->bindParam(':categorie', $categorie);
                      $pdostat1->bindParam(':ref', $ref);
                      $pdostat1->execute();
                      $asp = $_POST['aspect'];
                      $sql2 = "UPDATE Produits SET nom = :nom, aspectTech = :asp WHERE reference = :ref";
                      $pdostat2 = $conn->prepare($sql2);
                      $pdostat2->bindParam(':nom', $nom);
                      $pdostat2->bindParam(':asp', $asp);
                      $pdostat2->bindParam(':ref', $ref);
                      $pdostat2->execute();
                      $des = $_POST['descrip'];
                      $prixO = $_POST['prixO'];
                      $prixA = $_POST['prixA'];
                      $sto = $_POST['stock'];
                      $col = $_POST['color'];
                      $sql3 = "UPDATE Coloriser SET description = :des, prixOriginal = :prixO, prixActuel = :prixA, nbStock = :sto WHERE refProduit = :ref AND couleur = :col";
                      $pdostat3 = $conn->prepare($sql3);
                      $pdostat3->bindParam(':des', $des);
                      $pdostat3->bindParam(':prixO', $prixO);
                      $pdostat3->bindParam(':prixA', $prixA);
                      $pdostat3->bindParam(':sto', $sto);
                      $pdostat3->bindParam(':ref', $ref);
                      $pdostat3->bindParam(':col', $col);
                      $pdostat3->execute();
                      echo '<script>
                      window.alert("Modification effectuée");

                      window.location.href = "index.php";
                      </script>';
                      

                    }
                    else{
                      echo " <br>Le stock est trop élevé";
                    }
                  }
                  else{
                    echo " <br>Le prix Actuel est trop élevé ou ne correspond pas à : 25.15 ";
                  }
                }
                else{
                  echo " <br>Le prix Original est trop élevé ou ne correspond pas à : 25.15 ";
                }
              }
              else {
                echo " <br>La description est trop long (plus de 250 caractères).";
              }
            } 
            else {
              echo " <br>La couleur n'est pas bonne.";
            }
          }
          else {
            echo " <br>La categorie n'est pas bonne.";
          }
        } 
        else {
          echo " <br>L'aspect technique est trop long (plus de 250 caractères).";
        }
      } 
      else {
        echo " <br>Le nom est trop long (plus de 40 caractères).";
      }
    }
    ?>
  </div>
</body>
</html>