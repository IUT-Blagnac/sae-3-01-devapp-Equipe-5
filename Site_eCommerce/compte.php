<?php

session_start();

if (!isset($_SESSION["login"])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Mon compte</title>
</head>
<body>
    <?php 
        include_once("./include/header.php");
    ?>    

    <br>
    <div style="padding-left: 550px;">
        <h1> Mes informations </h1>
        <form method='POST'>
            Nom : <br> <input type='text' name='nom' value='test' required/> <br>
            Prénom : <br> <input type='text' name='prénom' value='test' required/> <br>
            Adresse mail <br> <input type='text' name='mail' value='test' required/> <br>
            Téléphone : <br> <input type='text' name='tel' value='test' required/> <br>
            Date de naissance : <br> <input type='text' name='dateN' value='test' required/> <br>
            Rue : <br> <input type='text' name='rue' value='test'/> <br>
            Ville : <br> <input type='text' name='ville' value='test'/> <br>
            Code postal : <br> <input type='text' name='codeP' value='test'/> <br>
            Complément : <br> <input type='text' name='compl' value='test'/> <br>
            Pays : <br> <input type='text' name='pays' value='test'/> <br>
            Nom d'utilisateur : <br> <input type='text' name='nomU' value='test' required/> <br>
            <br><input type='button' name='valider' value="Confirmer"/>
        </form>
    </div>


    <br><br><br>
    <?php
        include_once("./include/footer.php");
    ?>
</body>
</html>