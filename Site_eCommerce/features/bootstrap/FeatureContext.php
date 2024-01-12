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
     * @When I click on the :linkText link
     */
    public function iClickOnTheLink($linkText)
    {
        $this->clickLink($linkText);
    }

    /**
     * @Then I should be on the page :url
     */
    public function iShouldBeOnThePage($url)
    {
        $currentUrl = $this->getSession()->getCurrentUrl();
        if ($currentUrl !== $url) {
            throw new Exception("Expected to be on page '$url' but found '$currentUrl' instead.");
        }
    }

    /**
     * @When I press the search button
     */
    public function iPressTheSearchButton()
    {
        $button = $this->getSession()->getPage()->find('css', '#searchbutton');
        if (null === $button) {
            throw new \Exception("Le bouton de recherche n'a pas été trouvé.");
        }
        $button->press();
    }

    /**
     * @Then I should see :text in the search results
     */
    public function iShouldSeeInTheSearchResults($text)
    {
        $page = $this->getSession()->getPage();
        $searchResults = $page->find('css', '#produits');
        if (null === $searchResults) {
            throw new \Exception("La zone de résultats de recherche n'a pas été trouvée.");
        }

        if (strpos($searchResults->getText(), $text) === false) {
            throw new \Exception("Le texte '$text' n'a pas été trouvé dans les résultats de recherche.");
        }
    }
}
