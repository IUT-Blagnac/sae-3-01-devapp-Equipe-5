<?php
try {
  $user = 'X';
  $pass = 'X';
  $conn = new PDO(
    'mysql:host=localhost;dbname=X;charset=UTF8',
    $user,
    $pass,
    array(PDO::ATTR_ERRMODE
    => PDO::ERRMODE_EXCEPTION)
  );
} catch (PDOException $e) {
  echo "Erreur: " . $e->getMessage() . "<BR>";
  die();
}
