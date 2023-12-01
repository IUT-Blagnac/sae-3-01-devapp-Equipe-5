<header>
    <div>
        <a href="index.php" id="maly"> MALYART </a>
        <a href="index.php"> <img id='logoMalyart' src="./include/logoRond.png" width=150px> </a>
        
    </div>
    <br>
    <form action="produits.php">
        <nav>
            <a href="lesMieuxNotes.php"> Les mieux notés</a></li>
            <a href="promotions.php"> Promotions</a></li>
            <a href="produits.php"> Peintures</a></li>
            <a href="produits.php"> Dessins</a></li>
            <a href="produits.php"> Matériels d'art</a></li>
            <input id="searchbar" type="text" name="recherche" placeholder="Rechercher un produit">
            <input id="searchbutton" src="./include/recherche.png" name="submit" value="Se connecter" type="image" width=40px ></li>             
        </nav>
    </form>

    <input type="checkbox" id="menu-toggle">
    <label for="menu-toggle" class="menu-icon">&#9776;</label>
    

    <nav class="menu">
        <ul>
            <li><a href="index.php">Accueil</a></li>
            <li><a href="compte.php">Compte</a></li>
            <li><a href="panier.php">Panier</a></li>
        </ul>
    </nav>

    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const menu = document.querySelector('.menu');

        menuToggle.addEventListener('click', function() {
        menu.classList.toggle('active');
        });
    </script>
    
</header>