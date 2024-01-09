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
}
