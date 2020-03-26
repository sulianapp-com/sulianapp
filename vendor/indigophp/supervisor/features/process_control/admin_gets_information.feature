Feature: Admin gets information
  In order to know what processes are running
  As an Admin
  I should be able to get information about them

  Scenario:
    Given I have a process called "cat"
    And I have Supervisor running
    When I wait for start
    And I get information about the processes
    Then I should see running

  Scenario:
    Given I have a process called "cat"
    And I have a process called "tee"
    And I have Supervisor running
    When I wait for start
    And I get information about the processes
    Then I should see running
