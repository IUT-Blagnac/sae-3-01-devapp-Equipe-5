Feature: Website URL Check
  In order to verify that my website is accessible
  As a user
  I need to be able to access the website URL

  Scenario: Check if the website URL is accessible
    Given I am on the website "http://193.54.227.208/~saephp05/index.php"
    Then I should see the website loaded successfully

  Scenario: Check if the recommandation page works
    Given I am on the website "http://193.54.227.208/~saephp05/index.php"
    When I click on the "Les mieux notés" link
    Then I should be on the page "http://193.54.227.208/~saephp05/produits.php?categorie=MieuxNotés"

  Scenario: Navigate to the Mentions Légales page
    Given I am on the website "http://193.54.227.208/~saephp05/index.php"
    When I click on the "Mentions légales" link
    Then I should be on the page "http://193.54.227.208/~saephp05/mentionsLegales.php"

  Scenario: Search for a product
    Given I am on the website "http://193.54.227.208/~saephp05/index.php"
    When I fill in "searchbar" with "pinceaux"
    And I press the search button
    Then I should see "Lot de pinceaux" in the search results

  Scenario: Successful login
    Given I am on the website "http://193.54.227.208/~saephp05/login.php?redirect=compte.php?"
    When I fill in "login" with "test"
    And I fill in "password" with "test"
    And I press the login button
    Then I should be redirected to the account page
