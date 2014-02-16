Feature: As a registering customer
  I want to be able to enter my skype id
  So that it is saved in the system

Background: The RedboxDigital module is active
  Given that the module "RedboxDigital_Skypeid" is active

@mink:sahi
Scenario: I see the skype id field in customer registration form as a required field
  Given I am on "customer/account/create/"
  Then I should see "*Skype Id"