<?php
#src/MyMagentoContext.php

use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use MageTest\MagentoExtension\Context\MagentoContext;
use Behat\MinkExtension\Context\MinkContext;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

class MyMagentoContext extends MagentoContext
{
    private $output;
    public static $_initSetValue = "256M";


    public static function isJpeg($file)
    {
        return preg_match('~.jpg|.JPG|.jpeg|.JPEG~', $file);
    }

    public function __construct(array $parameters)
    {
        $this->useContext('mink', new MinkContext);
    }

    /**
     * @Given /^I visit "([^"]*)"$/
     */
    public function iVisit($uri)
    {
        $this->getSession()->visit($this->locatePath($uri));
    }

    /**
     * @Then /^I should see output "([^"]*)"$/
     */
    public function iShouldSeeOutput($string)
    {
        try {
            //assertContains((string) $string, (string) $this->output);
        } catch (Exception $e) {
            $diff = PHPUnit_Framework_TestFailure::exceptionToString($e);
            throw new \Exception($diff, $e->getCode(), $e);
        }
    }


    /**
     * @Given /^that the module "([^"]*)" is active$/
     */
    public function thatTheModuleIsActive($module)
    {
        $modules = \Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array) $modules;

        if (!$modulesArray[$module]->is('active')) {
            throw new \Exception($module . ' is not active');
        }
    }

    /**
     * @Then /^I should have the "([^"]*)" folder$/
     */
    public function iShouldHaveTheFolder($folder)
    {
        if (!is_dir(\Mage::getBaseDir() . $folder)) {
            throw new \Exception($folder . ' does not exist');
        }
    }

    /**
     * @Given /^that the simple product with sku "([^"]*)" exists$/
     */
    public function thatTheSimpleProductWithSkuExists($sku)
    {
        ini_set("memory_limit", self::$_initSetValue);
        $product = \Mage::getModel('catalog/product');
        $id = $product->getIdBySku((string) $sku);
        if (!$product->load((int) $id)->getEntityId()) {
            try {
                $this->_createNewProduct($product, $sku);
            } catch (Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }
    }

    /**
     * @When /^I run "([^"]*)"$/
     */
    public function iRun($arg1)
    {
        ini_set("memory_limit", self::$_initSetValue);
        $output = array();
        exec($arg1, $output);
        $this->output = trim(implode("\n", $output));
    }

    /**
     * @Then /^I should get:$/
     */
    public function iShouldGet($string)
    {
        if ((string) $string !== $this->output) {
            throw new \Exception(
                "Actual output is:\n" . $this->output
            );
        }
    }

    /**
     * @Then /^I should see:$/
     */
    public function iShouldSee($string)
    {
        try {
            assertContains((string) $string, (string) $this->output);
        } catch (Exception $e) {
            $diff = PHPUnit_Framework_TestFailure::exceptionToString($e);
            throw new \Exception($diff, $e->getCode(), $e);
        }
    }
}

