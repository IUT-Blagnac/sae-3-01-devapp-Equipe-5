<!DOCTYPE html>
<html>

<head>
    <title>Page d'accueil</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="include/logoRond.png" type="image/x-icon">
    <style>

    </style>
</head>

<body>
    <?php
    session_start();
    include_once("include/header.php");

    ?>
    <!-- Conteneur principal pour le carrousel -->
    <div class="container">
        <!-- Élément carrousel -->
        <div class="carousel">
            <!-- Conteneur interne pour les diapositives -->
            <div class="carousel-inner">
                <!-- Première diapositive -->
                <div class="slide">
                    <!-- Image de la première diapositive -->
                    <img src="images/carousel1.jpg" alt="Image 1">
                </div>
                <!-- Deuxième diapositive -->
                <div class="slide">
                    <!-- Image de la deuxième diapositive -->
                    <img src="images/carousel2.jpeg" alt="Image 2">
                </div>
                <!-- Troisième diapositive -->
                <div class="slide">
                    <!-- Image de la troisième diapositive -->
                    <img src="images/carousel3.png" alt="Image 3">
                </div>
                <!-- Quatrième diapositive -->
                <div class="slide">
                    <!-- Image de la quatrième diapositive -->
                    <img src="images/carousel4.png" alt="Image 4">
                </div>
                <!-- Cinquième diapositive -->
                <div class="slide">
                    <!-- Image de la cinquième diapositive -->
                    <img src="images/carousel5.png" alt="Image 5">
                </div>
            </div>
            <!-- Conteneur pour les boutons de navigation -->
            <div class="carousel-controls">
                <!-- Bouton pour passer à la diapositive précédente -->
                <button id="prev"><</button>
                        <!-- Bouton pour passer à la diapositive suivante -->
                        <button id="next">></button>
            </div>
            <!-- Conteneur pour les points de navigation -->
            <div class="carousel-dots"></div>
        </div>
    </div>
    <script>
        (function() {
            // Utilisation de la directive "use strict" pour activer le mode strict en JavaScript
            // Cela implique une meilleure gestion des erreurs et une syntaxe plus stricte pour le code
            "use strict"

            // Déclare la constante pour la durée de chaque slide
            const slideTimeout = 5000;

            // Récupère les boutons de navigation
            const prev = document.querySelector('#prev');
            const next = document.querySelector('#next');

            // Récupère tous les éléments de type "slide"
            const $slides = document.querySelectorAll('.slide');

            // Initialisation de la variable pour les "dots"
            let $dots;

            // Initialisation de la variable pour l'intervalle d'affichage des slides
            let intervalId;

            // Initialisation du slide courant à 1
            let currentSlide = 1;

            // Fonction pour afficher un slide spécifique en utilisant un index
            function slideTo(index) {
                // Vérifie si l'index est valide (compris entre 0 et le nombre de slides - 1)
                currentSlide = index >= $slides.length || index < 1 ? 0 : index;

                // Boucle sur tous les éléments de type "slide" pour les déplacer
                $slides.forEach($elt => $elt.style.transform = `translateX(-${currentSlide * 100}%)`);

                // Boucle sur tous les "dots" pour mettre à jour la couleur par la classe "active" ou "inactive"
                $dots.forEach(($elt, key) => $elt.classList = `dot ${key === currentSlide? 'active': 'inactive'}`);
            }

            // Fonction pour afficher le prochain slide
            function showSlide() {
                slideTo(currentSlide);
                currentSlide++;
            }

            // Boucle pour créer les "dots" en fonction du nombre de slides
            for (let i = 1; i <= $slides.length; i++) {
                let dotClass = i == currentSlide ? 'active' : 'inactive';
                let $dot = `<span data-slidId="${i}" class="dot ${dotClass}"></span>`;
                document.querySelector('.carousel-dots').innerHTML += $dot;
            }

            // Récupère tous les "dots"
            $dots = document.querySelectorAll('.dot');

            // Boucle pour ajouter des écouteurs d'événement "click" sur chaque "dot"
            $dots.forEach(($elt, key) => $elt.addEventListener('click', () => slideTo(key)));

            // Ajout d'un écouteur d'événement "click" sur le bouton "prev" pour afficher le slide précédent
            prev.addEventListener('click', () => slideTo(--currentSlide))

            // Ajout d'un écouteur d'événement "click" sur le bouton "next" pour afficher le slide suivant
            next.addEventListener('click', () => slideTo(++currentSlide))

            // Initialisation de l'intervalle pour afficher les slides
            intervalId = setInterval(showSlide, slideTimeout)
            // Boucle sur tous les éléments de type "slide" pour ajouter des écouteurs d'événement pour les interactions avec la souris et le toucher
            $slides.forEach($elt => {
                let startX;
                let endX;
                // Efface l'intervalle d'affichage des slides lorsque la souris passe sur un slide
                $elt.addEventListener('mouseover', () => {
                    clearInterval(intervalId);
                }, false)
                // Réinitialise l'intervalle d'affichage des slides lorsque la souris sort d'un slide
                $elt.addEventListener('mouseout', () => {
                    intervalId = setInterval(showSlide, slideTimeout);
                }, false);
                // Enregistre la position initiale du toucher lorsque l'utilisateur touche un slide
                $elt.addEventListener('touchstart', (event) => {
                    startX = event.touches[0].clientX;
                });
                // Enregistre la position finale du toucher lorsque l'utilisateur relâche son doigt
                $elt.addEventListener('touchend', (event) => {
                    endX = event.changedTouches[0].clientX;
                    // Si la position initiale est plus grande que la position finale, affiche le prochain slide
                    if (startX > endX) {
                        slideTo(currentSlide + 1);
                        // Si la position initiale est plus petite que la position finale, affiche le slide précédent
                    } else if (startX < endX) {
                        slideTo(currentSlide - 1);
                    }
                });
            })
        })()
    </script>
    <?php
    include_once("include/footer.php");
    ?>
</body>

</html>