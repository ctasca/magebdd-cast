Feature: As a registered customer
  I want to be able to edit my skype id
  So that I can change it if I need to

Background: The RedboxDigital module is active
  Given that the module "RedboxDigital_Skypeid" is active

  @mink:sahi
Scenario: I see the skype id field in customer registration form as a required field
  Given I am on "customer/account/login/"
  And I fill in "email" with "carlo.tasca.mail@gmail.com"
  And I fill in "pass" with "what3v3r"
  When I press "Login"
  And I follow "Account Information"
  Then I should see "*Skype Id"