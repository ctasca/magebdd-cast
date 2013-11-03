Feature: In order to be able to run a store
  As as store administrator
  I should be able to login to backend and
  view/edit system configuration variables


  @mink:sahi
  Scenario: I can log in to backend
    Given I am on the dashboard after logging in with "admin" and "dummypass1"
    Then I should see "Dashboard"

  @mink:sahi
  Scenario: I can view unsecure url
    Given I follow "Configuration"
    When I follow "Web"
    Then I should see "http://magebdd.dev/"
