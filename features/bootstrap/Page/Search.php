<?php
namespace Page;


class Search extends BasePage
{
    private $term;


    public function searchFor($term) {
        $this->term = $term;
        $this->fillWith("#search", $term);
        $this->waitForElement("#search")->submit();
    }

    public function hasNoSearchResults() {
        $this->assertIsVisible("#maincontent .message.notice");
    }
}