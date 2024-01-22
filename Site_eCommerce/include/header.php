<header>
    <div>
        <a href="index.php" id="maly"> MALYART </a>
        <a href="index.php"> <img id='logoMalyart' src="./include/logoRond.png" width=150px> </a>

    </div>
    <br>
    <form action="produits.php">
        <nav>
            <a href="produits.php?categorie=MieuxNotes"> Les mieux notés</a></li>
            <a href="produits.php?categorie=Promotions"> Promotions</a></li>
            <div class="dropdown">
                <span><a href="produits.php?categorie=Peintures"> Peintures</a></li></span>
                <div class="dropdown-content">
                    <a href="produits.php?categorie=Peintures lhuile">Peintures à l'huile</a>
                    <a href="produits.php?categorie=Peintures acryliques">Peintures acryliques</a>
                    <a href="produits.php?categorie=Peintures laquarelle">Peintures à l'aquarelle</a>
                </div>
            </div>
            <div class="dropdown">
                <span><a href="produits.php?categorie=Dessins"> Dessins</a></li></span>
                <div class="dropdown-content">
                    <a href="produits.php?categorie=Dessins au crayon">Dessins au crayon</a>
                    <a href="produits.php?categorie=Dessins au fusain">Dessins au fusain</a>
                    <a href="produits.php?categorie=Croquis artistiques">Croquis artistiques</a>
                </div>
            </div>
            <div class="dropdown">
                <span><a href="produits.php?categorie=Materiel dart"> Matériels d'art</a></li></span>
                <div class="dropdown-content">
                    <a href="produits.php?categorie=Toiles et support">Toiles et supports</a>
                    <a href="produits.php?categorie=Pinceaux et outils">Pinceaux et outils</a>
                    <a href="produits.php?categorie=Peintures et couleurs">Peintures et couleurs</a>
                </div>
            </div>
            <input id="searchbar" type="text" name="recherche" placeholder="Rechercher un produit">
            <input id="searchbutton" src="./include/recherche.png" name="submit" value="Se connecter" type="image" width=40px></li>
        </nav>
    </form>

    <input type="checkbox" id="menu-toggle">
    <label for="menu-toggle" class="menu-icon">&#9776;</label>


    <nav class="menu">
        <ul>
            <?php
                $path = $_SERVER['PHP_SELF'];
                $page = basename($path);
                if ($page == "index.php"){}
                else{
                    echo "<li><a href='index.php'>Accueil</a></li>";
                }
            ?>
            <li><a href="compte.php">Compte</a></li>
            <li><a href="panier.php">Panier</a></li>
            <!--if connected add deconnect -->
            <?php if (isset($_SESSION['logged'])) { 
                if ($_SESSION['logged'] == true) {
                echo "<li><a href='deconnexion.php'>Déconnexion</a></li>";
            }} ?>
        </ul>
    </nav>
    <br>

    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const menu = document.querySelector('.menu');

        menuToggle.addEventListener('click', function() {
            menu.classList.toggle('active');
        });
    </script>

    <div id="bandeau">

    </div>
</header>