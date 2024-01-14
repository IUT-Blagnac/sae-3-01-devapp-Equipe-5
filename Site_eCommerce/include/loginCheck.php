<?php
//take the current page name and put it in the variable $page
$path = $_SERVER['PHP_SELF'];
$page = basename($path);
$get = $_SERVER['QUERY_STRING'];

//if the user is not logged in redirect him to the login page with the current page name in the url
if ($_SESSION['logged'] != true) {
    //the redirect can be ?redirect="ajout.php?reference=ART26&couleur=yellow "
    //$_GET['redirect'] will be "ajout.php?reference=ART26&couleur=yellow "
    header('Location: login.php?redirect=' . $page . '?' . $get);
}
