Feature: ls
  In order to see the directory structure
  As a UNIX user
  I need to be able to list the current directory's contents

  Scenario: List 2 files in a directory
    Given I have a file named "john"
    And I have a file named "hammond"
    When I run "dir"
    Then I should see "john" in the output
    And I should see "hammond" in the output