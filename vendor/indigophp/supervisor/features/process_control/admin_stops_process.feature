Feature: Admin starts process
  In order to make some processes stopped
  As an Admin
  I should be able to stop them various ways

  Scenario:
    Given I have a process called "cat"
    And I have Supervisor running
    When I wait for start
    And I get information about the processes before action
    And I "stop" the process
    And I get information about the processes
    Then I should see running first
    Then I should get a success response
    And I should see not running

  Scenario:
    Given I have a process called "cat"
    And I have a process called "tee"
    And I have Supervisor running
    When I wait for start
    And I get information about the processes before action
    And I "stop" the processes
    And I get information about the processes
    Then I should see running first
    Then I should get a success response for all
    And I should see not running

  Scenario:
    Given I have a process called "cat"
    And it is part of group called "test"
    And I have a process called "tee"
    And it is part of group called "test"
    And I have Supervisor running
    When I wait for start
    And I get information about the processes before action
    And I "stop" the processes in the group
    And I get information about the processes
    Then I should see running first
    And I should see them as part of the group
    Then I should get a success response for all
    And I should see not running
