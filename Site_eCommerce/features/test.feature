Feature: Website URL Check
  In order to verify that my website is accessible
  As a user
  I need to be able to access the website URL

  Scenario: Check if the website URL is accessible
    Given I am on the website "http://193.54.227.208/~saephp05/index.php"
    Then I should see the website loaded successfully


