<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\MinkExtension\Context\MinkContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given I am on the website :url
     */
    public function iAmOnTheWebsite($url)
    {
        $this->visit($url);
    }

    /**
     * @Then I should see the website loaded successfully
     */
    public function iShouldSeeTheWebsiteLoadedSuccessfully()
    {
        $statusCode = $this->getSession()->getStatusCode();
        if ($statusCode != 200) {
            throw new Exception("Website did not load successfully. Status code: $statusCode");
        }
    }

    /**
     * @When I click on :url link
     */
    public function iClickOnLink($url)
    {
        $this->clickLink($url);
    }

    /**
     * @Then I should be on :url page
     */
    public function iShouldBeOnPage($url)
    {
        $expectedUrl = "http://193.54.227.208/~saephp05/produits.php?categorie=MieuxNotés";
        if ($url !== $expectedUrl) {
            throw new \Exception("L'URL de la page ne correspond pas à l'URL attendue '$expectedUrl'.");
        }
    }
}   
