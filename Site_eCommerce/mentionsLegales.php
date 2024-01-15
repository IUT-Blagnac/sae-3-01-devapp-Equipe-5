<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentions légales</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="include/logoRond.png" type="image/x-icon">
</head>

<body>
    <?php
    include_once("include/header.php");
    ?>

    <div style="padding-left: 7px;">
        <h1>Mentions légales</h1><br><br>

        <div id="summary">
            <!-- list html underlined with the text in black -->
            <ul>
                <li><a href="#editeur" style="color:black">Editeur</a></li>
                <li><a href="#hebergeur" style="color:black">Hébergeur</a></li>
                <li><a href="#directeur" style="color:black">Directeur de la publication</a></li>
            </ul>
        </div>
        <br><br>


        <h1 id="editeur">1. Editeur</h1><br><br>

        <p>Le site MalyArt.com est édité par :</p><br><br>
        <ul style="list-style-type:none">
            <p>
                La société MalyArt, société par actions simplifiée au capital de 10.000 euros, dont le siège social est
                situé 1 rue de la Paix, 75000 Paris, immatriculée au registre du commerce et des sociétés de Paris.
            </p><br><br>
        </ul>
        <p>Numéro de téléphone : 01 23 45 67 89</p><br><br>
        <p>Adresse électronique : info@malyart.fr</p><br><br>
        <p>Numéro de TVA intracommunautaire FR 001 234 567 89</p><br><br>


        <h1 id="hebergeur">2. Hébergeur</h1><br><br>

        <p>Le site MalyArt.com est hébergé par : </p><br><br>
        <ul style="list-style-type: dash">
            <p>
                L'IUT Blagnac,
            </p><br><br>
        </ul>
        <p>Lieu de l'hébergement : 1 Pl. Georges Brassens, 31700 Blagnac, France</p><br><br>
        <p>Numéro de téléphone : 05 61 71 24 00 (9:00-18:00, du lundi au vendredi)</p><br></br>


        <h1 id="directeur">3. Directeur de la publication</h1><br><br>

        <p>Le directeur de la publication du site MalyArt.com est Alexis Christaud-Braize</p><br><br>
    </div>


    <?php
    include_once("include/footer.php");
    ?>

    <!-- Script pour slide lorsqu'on clique sur un bouton du sommaire -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sélectionne tous les liens dans le sommaire
            var links = document.querySelectorAll('#summary a');

            // Ajoute un gestionnaire d'événement à chaque lien
            links.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Récupère le nom de l'ancre du lien
                    var targetId = this.getAttribute('href').substring(1);

                    // Trouve l'élément cible
                    var targetElement = document.getElementById(targetId);

                    // Fait défiler en douceur vers l'élément cible
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>
</body>

</html>