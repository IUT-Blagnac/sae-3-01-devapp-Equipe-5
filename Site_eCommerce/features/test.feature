Feature: Website URL Check
  In order to verify that my website is accessible
  As a user
  I need to be able to access the website URL

  Scenario: Check if the website URL is accessible
    Given I am on the website "http://193.54.227.208/~saephp05/index.php"
    Then I should see the website loaded successfully

  Scenario Outline: Check if the page works
    Given I am on the website "<urlDepart>"
    When I click on the "<Lien>" link
    Then I should be on the page "<urlArriver>"

  Examples:
    | urlDepart | Lien | urlArriver |
    | http://193.54.227.208/~saephp05/index.php | Les mieux notés | http://193.54.227.208/~saephp05/produits.php?categorie=MieuxNotés |
    | http://193.54.227.208/~saephp05/index.php | Mentions légales | http://193.54.227.208/~saephp05/mentionsLegales.php |
    | http://193.54.227.208/~saephp05/index.php | Promotions | http://193.54.227.208/~saephp05/produits.php?categorie=Promotions |
    | http://193.54.227.208/~saephp05/index.php | Peintures | http://193.54.227.208/~saephp05/produits.php?categorie=Peintures |
    | http://193.54.227.208/~saephp05/index.php | Dessins | http://193.54.227.208/~saephp05/produits.php?categorie=Dessins |
    | http://193.54.227.208/~saephp05/index.php | Matériels d'art | http://193.54.227.208/~saephp05/produits.php?categorie=Materiel dart |
    | http://193.54.227.208/~saephp05/produit.php?reference=ART1&couleur=black | Matériels d'art | http://193.54.227.208/~saephp05/produits.php?categorie=Materiel dart |

  Scenario: Search for a product
    Given I am on the website "http://193.54.227.208/~saephp05/index.php"
    When I fill in "searchbar" with "pinceaux"
    And I press the search button
    Then I should see "Lot de pinceaux" in the search results
