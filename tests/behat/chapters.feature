@mod @mod_mubook @MuTMS @javascript
Feature: Editors may manage chapters in mod_mubook

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "activities" exist:
      | activity | name        | intro             | course | idnumber |
      | mubook   | Test book 1 | This is an intro  | C1     | mubook1  |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |

  Scenario: Editing teachers may create chapters without content from TOC
    Given I am on the "Test book 1" "mubook activity" page logged in as "teacher1"
    And I should see "This is an intro"

    And I should see "Turn on edit mode to create book chapters"
    And I should not see "No content has been added to this book yet."
    And I should not see "Add chapter"
    And edit mode should be available on the current page

    When I turn editing mode on
    Then I should see "No content has been added to this book yet."
    And I should not see "Turn on edit mode to create book chapters"
    And I should see "Add chapter"

    When I press "Add chapter"
    And I set the following fields in the ".modal-dialog" "css_element" to these values:
      | Chapter title | Treti kapitola |
      | Add content   | None           |
    And I click on "Add chapter" "button" in the ".modal-dialog" "css_element"
    Then I should see "This is an intro"
    And I should see "Treti kapitola"

    When I press "Add chapter"
    And I set the following fields in the ".modal-dialog" "css_element" to these values:
      | Chapter position | First chapter   |
      | Chapter title    | Prvni kapitola  |
      | Add content      | HTML text       |
    And I click on "Add chapter" "button" in the ".modal-dialog" "css_element"
    And I press "Cancel"
    Then I should see "This is an intro"
    And I should see "Prvni kapitola"
    And I should see "Treti kapitola"
    And "Treti kapitola" "text" should appear after "Prvni kapitola" "text"

    When I press "Add chapter"
    And I set the following fields in the ".modal-dialog" "css_element" to these values:
      | Chapter position | After 1 Prvni kapitola |
      | Chapter title    | Druha kapitola         |
      | Add content      | None                   |
    And I click on "Add chapter" "button" in the ".modal-dialog" "css_element"
    Then I should see "This is an intro"
    And I should see "Prvni kapitola"
    And I should see "Druha kapitola"
    And I should see "Treti kapitola"
    And "Druha kapitola" "text" should appear after "Prvni kapitola" "text"
    And "Treti kapitola" "text" should appear after "Druha kapitola" "text"

    When I press "Add chapter"
    And I set the following fields in the ".modal-dialog" "css_element" to these values:
      | Chapter title    | Pata kapitola          |
      | Add content      | None                   |
    And I click on "Add chapter" "button" in the ".modal-dialog" "css_element"
    Then I should see "This is an intro"
    And I should see "Prvni kapitola"
    And I should see "Druha kapitola"
    And I should see "Treti kapitola"
    And I should see "Pata kapitola"
    And "Druha kapitola" "text" should appear after "Prvni kapitola" "text"
    And "Treti kapitola" "text" should appear after "Druha kapitola" "text"
    And "Pata kapitola" "text" should appear after "Treti kapitola" "text"

    When I click on "Chapter actions: Treti kapitola" "link_or_button"
    And I click on "Add chapter" "link" in the ".dropdown-menu.show" "css_element"
    And I set the following fields in the ".modal-dialog" "css_element" to these values:
      | Chapter title    | Ctvrta kapitola        |
      | Add content      | None                   |
    And I click on "Add chapter" "button" in the ".modal-dialog" "css_element"
    Then I should see "This is an intro"
    And I should see "Prvni kapitola"
    And I should see "Druha kapitola"
    And I should see "Treti kapitola"
    And I should see "Ctvrta kapitola"
    And I should see "Pata kapitola"
    And "Druha kapitola" "text" should appear after "Prvni kapitola" "text"
    And "Treti kapitola" "text" should appear after "Druha kapitola" "text"
    And "Ctvrta kapitola" "text" should appear after "Treti kapitola" "text"
    And "Pata kapitola" "text" should appear after "Ctvrta kapitola" "text"

    When I click on "Chapter actions: Druha kapitola" "link_or_button"
    And I click on "Add sub-chapter" "link" in the ".dropdown-menu.show" "css_element"
    And I set the following fields in the ".modal-dialog" "css_element" to these values:
      | Sub-chapter title | Podkapitola 2         |
      | Add content       | None                  |
    And I click on "Add sub-chapter" "button" in the ".modal-dialog" "css_element"
    Then I should see "This is an intro"
    And I should see "Prvni kapitola"
    And I should see "Druha kapitola"
    And I should see "Podkapitola 2"
    And I should see "Treti kapitola"
    And I should see "Ctvrta kapitola"
    And I should see "Pata kapitola"
    And "Druha kapitola" "text" should appear after "Prvni kapitola" "text"
    And "Podkapitola 2" "text" should appear after "Druha kapitola" "text"
    And "Treti kapitola" "text" should appear after "Podkapitola 2" "text"
    And "Ctvrta kapitola" "text" should appear after "Treti kapitola" "text"
    And "Pata kapitola" "text" should appear after "Ctvrta kapitola" "text"

    When I click on "Chapter actions: Druha kapitola" "link_or_button"
    And I click on "Add sub-chapter" "link" in the ".dropdown-menu.show" "css_element"
    And I set the following fields in the ".modal-dialog" "css_element" to these values:
      | Sub-chapter position | First in 2 Druha kapitola |
      | Sub-chapter title    | Podkapitola 1             |
      | Add content          | None                      |
    And I click on "Add sub-chapter" "button" in the ".modal-dialog" "css_element"
    Then I should see "This is an intro"
    And I should see "Prvni kapitola"
    And I should see "Druha kapitola"
    And I should see "Podkapitola 1"
    And I should see "Podkapitola 2"
    And I should see "Treti kapitola"
    And I should see "Ctvrta kapitola"
    And I should see "Pata kapitola"
    And "Druha kapitola" "text" should appear after "Prvni kapitola" "text"
    And "Podkapitola 1" "text" should appear after "Druha kapitola" "text"
    And "Podkapitola 2" "text" should appear after "Podkapitola 1" "text"
    And "Treti kapitola" "text" should appear after "Podkapitola 2" "text"
    And "Ctvrta kapitola" "text" should appear after "Treti kapitola" "text"
    And "Pata kapitola" "text" should appear after "Ctvrta kapitola" "text"

    When I click on "Chapter actions: Druha kapitola" "link_or_button"
    And I click on "Add sub-chapter" "link" in the ".dropdown-menu.show" "css_element"
    And I set the following fields in the ".modal-dialog" "css_element" to these values:
      | Sub-chapter title    | Podkapitola 4             |
      | Add content          | HTML text                 |
    And I click on "Add sub-chapter" "button" in the ".modal-dialog" "css_element"
    And I press "Cancel"
    Then I should see "This is an intro"
    And I should see "Prvni kapitola"
    And I should see "Druha kapitola"
    And I should see "Podkapitola 1"
    And I should see "Podkapitola 2"
    And I should see "Podkapitola 4"
    And I should see "Treti kapitola"
    And I should see "Ctvrta kapitola"
    And I should see "Pata kapitola"
    And "Druha kapitola" "text" should appear after "Prvni kapitola" "text"
    And "Podkapitola 1" "text" should appear after "Druha kapitola" "text"
    And "Podkapitola 2" "text" should appear after "Podkapitola 1" "text"
    And "Podkapitola 4" "text" should appear after "Podkapitola 2" "text"
    And "Treti kapitola" "text" should appear after "Podkapitola 4" "text"
    And "Ctvrta kapitola" "text" should appear after "Treti kapitola" "text"
    And "Pata kapitola" "text" should appear after "Ctvrta kapitola" "text"

    When I click on "Chapter actions: Treti kapitola" "link_or_button"
    And I click on "Add sub-chapter" "link" in the ".dropdown-menu.show" "css_element"
    And I set the following fields in the ".modal-dialog" "css_element" to these values:
      | Sub-chapter position | After 2.2 Podkapitola 2 |
      | Sub-chapter title    | Podkapitola 3           |
      | Add content          | None                    |
    And I click on "Add sub-chapter" "button" in the ".modal-dialog" "css_element"
    Then I should see "This is an intro"
    And I should see "Prvni kapitola"
    And I should see "Druha kapitola"
    And I should see "Podkapitola 1"
    And I should see "Podkapitola 2"
    And I should see "Podkapitola 3"
    And I should see "Podkapitola 4"
    And I should see "Treti kapitola"
    And I should see "Ctvrta kapitola"
    And I should see "Pata kapitola"
    And "Druha kapitola" "text" should appear after "Prvni kapitola" "text"
    And "Podkapitola 1" "text" should appear after "Druha kapitola" "text"
    And "Podkapitola 2" "text" should appear after "Podkapitola 1" "text"
    And "Podkapitola 3" "text" should appear after "Podkapitola 2" "text"
    And "Podkapitola 4" "text" should appear after "Podkapitola 3" "text"
    And "Treti kapitola" "text" should appear after "Podkapitola 4" "text"
    And "Ctvrta kapitola" "text" should appear after "Treti kapitola" "text"
    And "Pata kapitola" "text" should appear after "Ctvrta kapitola" "text"

  Scenario: Editing teachers may create chapters without content from one chapter page
    Given I am on the "Test book 1" "mubook activity" page logged in as "teacher1"
    And I turn editing mode on
    And I press "Add chapter"
    And I set the following fields in the ".modal-dialog" "css_element" to these values:
      | Chapter title | Prvni kapitola |
      | Add content   | None           |
    And I click on "Add chapter" "button" in the ".modal-dialog" "css_element"
    And I press "Add chapter"
    And I set the following fields in the ".modal-dialog" "css_element" to these values:
      | Chapter title | Druha kapitola |
      | Add content   | None           |
    And I click on "Add chapter" "button" in the ".modal-dialog" "css_element"

    When I follow "Prvni kapitola"
    And I should see "Add sub-chapter"

    When I press "Add sub-chapter"
    And I set the following fields in the ".modal-dialog" "css_element" to these values:
      | Sub-chapter title    | Podkapitola 2 |
      | Add content          | None          |
    And I click on "Add sub-chapter" "button" in the ".modal-dialog" "css_element"
    Then I should see "Podkapitola 2"

    When I press "Add sub-chapter"
    And I set the following fields in the ".modal-dialog" "css_element" to these values:
      | Sub-chapter position | First in 1 Prvni kapitola |
      | Sub-chapter title    | Podkapitola 1             |
      | Add content          | HTML text                 |
    And I click on "Add sub-chapter" "button" in the ".modal-dialog" "css_element"
    And I press "Cancel"
    Then I should see "Podkapitola 1"
    And "Podkapitola 2" "text" should appear after "Podkapitola 1" "text"

    When I press "Add sub-chapter"
    And the following fields in the ".modal-dialog" "css_element" match these values:
      | Sub-chapter position | After 1.2 Podkapitola 2 |
    And I set the following fields in the ".modal-dialog" "css_element" to these values:
      | Sub-chapter title    | Podkapitola 3  |
      | Add content          | None           |
    And I click on "Add sub-chapter" "button" in the ".modal-dialog" "css_element"
    Then I should see "Podkapitola 1"
    And "Podkapitola 2" "text" should appear after "Podkapitola 1" "text"
    And "Podkapitola 3" "text" should appear after "Podkapitola 2" "text"

    When I press "Add sub-chapter"
    And I set the following fields in the ".modal-dialog" "css_element" to these values:
      | Sub-chapter position | First in 2 Druha kapitola |
      | Sub-chapter title    | Podkapitola x1            |
      | Add content          | None                      |
    And I click on "Add sub-chapter" "button" in the ".modal-dialog" "css_element"
    Then I should see "Druha kapitola"
    And I should see "Podkapitola x1"

    When I press "Add sub-chapter"
    And I set the following fields in the ".modal-dialog" "css_element" to these values:
      | Sub-chapter position | After 1.3 Podkapitola 3   |
      | Sub-chapter title    | Podkapitola 4             |
      | Add content          | HTML text                 |
    And I click on "Add sub-chapter" "button" in the ".modal-dialog" "css_element"
    And I press "Cancel"
    Then I should see "Podkapitola 1"
    And "Podkapitola 2" "text" should appear after "Podkapitola 1" "text"
    And "Podkapitola 3" "text" should appear after "Podkapitola 2" "text"
    And "Podkapitola 4" "text" should appear after "Podkapitola 3" "text"
