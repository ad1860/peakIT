<?php

use Page\Search;

use Behat\Behat\Context\Context;
class SearchContext implements Context
{
    private $search;

    public function __construct(Search $search)
    {
        $this->search = $search;
    }

    /**
     * @When /^I search for (.*)$/
     */
    public function iSearchForLuma($term)
    {
        $this->search->searchFor($term);
    }

    /**
     * @Then /^the search page should contain (.*)$/
     */
    public function theSearchPageShouldContainBreadcrumbWithSearchTerm($validation)
    {
        $this->search->hasNoSearchResults();

    }

}