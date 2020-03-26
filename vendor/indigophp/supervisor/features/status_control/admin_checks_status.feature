Feature: Admin checks status
  In order to display details
  As an Admin
  I should be able to get information about Supervisor

  Scenario:
    Given I have Supervisor running
    When I ask for the API version
    Then I should get at least "3.0" version

  Scenario:
    Given I have Supervisor running
    When I ask for Supervisor version
    Then I should get at least "3.0" version

  Scenario:
    Given my Supervisor instance is called "supervisor"
    And I have Supervisor running
    When I ask for Supervisor identification
    Then I should get "supervisor" as identifier

  Scenario:
    Given I have Supervisor running
    When I ask for the state
    Then I should get "1" as statecode and "RUNNING" as statename

  Scenario:
    Given I have Supervisor running
    When I ask for the PID
    Then I should get the real PID

  Scenario:
    Given I have Supervisor running
    When I ask for the log
    Then I should get an INFO about supervisord started
