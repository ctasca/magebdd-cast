<?php
# features/bootstrap/AdminUserContext.php

use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use MageTest\MagentoExtension\Context\MagentoContext;

class AdminUserContext extends MyMagentoContext
{
    /**
     * @Given /^I am on the dashboard after logging in with "([^"]*)" and "([^"]*)"$/
     */
    public function iAmOnTheDashboardAfterLoggingInWithAnd($username, $password)
    {
        $this->getSession()->visit($this->getMinkParameter('base_url') . '/admin');
        $this->getSession()->getPage()->fillField('username', $username);
        $this->getSession()->getPage()->fillField('login', $password);
        $this->getSession()->getPage()->pressButton('Login');
        $this->iCloseMessagePopUp();
    }


    /**
     * @Given /^I close message pop up$/
     */
    public function iCloseMessagePopUp()
    {
        $this->getSession()->wait(2000);
        $function = <<<JS
(function(){
    closeMessagePopup();
})()
JS;
        return $this->getSession()->getDriver()->evaluateScript($function);
    }
}
