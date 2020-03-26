Feature: Admin starts process
  In order to make some processes running
  As an Admin
  I should be able to start them various ways

  Scenario:
    Given I have a process called "cat"
    And autostart is disabled
    And I have Supervisor running
    When I get information about the processes before action
    And I "start" the process
    And I get information about the processes
    Then I should see not running first
    Then I should get a success response
    And I should see running

  Scenario:
    Given I have a process called "cat"
    And autostart is disabled
    And I have a process called "tee"
    And autostart is disabled
    And I have Supervisor running
    When I get information about the processes before action
    And I "start" the processes
    And I get information about the processes
    Then I should see not running first
    Then I should get a success response for all
    And I should see running

  Scenario:
    Given I have a process called "cat"
    And autostart is disabled
    And it is part of group called "test"
    And I have a process called "tee"
    And autostart is disabled
    And it is part of group called "test"
    And I have Supervisor running
    When I get information about the processes before action
    And I "start" the processes in the group
    And I get information about the processes
    Then I should see not running first
    But I should see them as part of the group
    Then I should get a success response for all
    And I should see running
