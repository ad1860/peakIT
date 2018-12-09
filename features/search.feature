@javascript @insulated @search
Feature: Search


  Scenario: Search using a term that return no results
    And I have a new user
    Given I am on homepage
    When I search for ww()
    Then the search page should contain no search results warning