@mod @mod_mubook @MuTMS @javascript
Feature: Generators for mod_mubook tests

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |

  Scenario: Teachers can view the list of data activities and their formatted descriptions
    When the following "activities" exist:
      | activity | name        | intro             | course | idnumber |
      | mubook   | Test book 1 | This is an intro  | C1     | mubook1  |
    And I am on the "Test book 1" "mubook activity" page logged in as admin
    Then I should see "Turn on edit mode to create book chapters"
