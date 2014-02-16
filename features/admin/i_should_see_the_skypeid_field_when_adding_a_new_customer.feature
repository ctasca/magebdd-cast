Feature: As as store administrator
  I want to be able to assign a skype id to a customer
  So that it can be registered in customer data

  Background: The RedboxDigital module is active
    Given that the module "RedboxDigital_Skypeid" is active

  @mink:sahi
  Scenario: I am logged in as administrator
    Given I am on the dashboard after logging in with "admin" and "what3v3r"
    Then I should see "Dashboard"

  @mink:sahi
  Scenario: I want to see the skype id required field when adding a new customer
    Given I follow "Manage Customers"
    When I press "Add New Customer"
    Then I should see "Skype Id *"
