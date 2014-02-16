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
    /**
     * @var string
     */
    private $output;
    /**
     * @var string
     */
    public static $_initSetValue = "256M";


    /**
     * Returns true if file passed as argument is a jpeg file
     */
    public static function isJpeg($file)
    {
        return preg_match('~.jpg|.JPG|.jpeg|.JPEG~', $file);
    }

    /**
     * Enables module passed as argument, if module is currently disabled
     */
    public static function enableModule($moduleName)
    {
        try{
            $nodePath = "modules/$moduleName/active";

            if (!\Mage::helper('core/data')->isModuleEnabled($moduleName)) {
                \Mage::getConfig()->setNode($nodePath, 'true', true);
            }

            // Enable its output as well (which was already loaded)
            $outputPath = "advanced/modules_disable_output/$moduleName";
            if (!Mage::getStoreConfig($outputPath)) {
                Mage::app()->getStore()->setConfig($outputPath, false);
            }
        }catch(Exception $e) {
            throw new Exception($e->getMessage());
        }

    }

    /**
     * Disables module passed as argument, if module is currently enabled
     */
    public static function disableModule($moduleName)
    {
        try{
            $nodePath = "modules/$moduleName/active";

            if (\Mage::helper('core/data')->isModuleEnabled($moduleName)) {
                \Mage::getConfig()->setNode($nodePath, 'false', true);
            }

            // Disable its output as well (which was already loaded)
            $outputPath = "advanced/modules_disable_output/$moduleName";
            if (!Mage::getStoreConfig($outputPath)) {
                Mage::app()->getStore()->setConfig($outputPath, true);
            }
        }catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    /**
     * Press button by id|title|text
     */
    protected function _iPressButton($button)
    {
        $this->getSession()->getPage()->pressButton($button);
    }


    /**
     * Finds an element
     */
    protected function _findElementWithXpath($xpath)
    {
        $nodeElement = $this->getSession()->getDriver()->find($xpath);
        if (!is_array($nodeElement) && !array_key_exists(0, $nodeElement) && !($nodeElement[0] instanceof \Behat\Mink\Element\NodeElement)) {
            throw new \Behat\Mink\Exception\ElementNotFoundException(
                $this->getSession(), 'Element', 'xpath', $xpath
            );
        }

        return $nodeElement;
    }

    /**
     * Returns true if argument is an array and the first element is
     * an instance of \Behat\Mink\Element\NodeElement
     * @param $node
     * @throws Behat\Mink\Exception\ElementNotFoundException
     * @return bool
     */
    protected function _isNodeElement($node)
    {
        if(!(is_array($node)) && !(array_key_exists(0, $node)) && !($node[0] instanceof \Behat\Mink\Element\NodeElement))
        {

            throw new Behat\Mink\Exception\ElementNotFoundException(
                $this->getSession(), 'Argument passed is not a node element ', __METHOD__ , ''
            );
        }
        return true;
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
            assertContains((string) $string, (string) $this->output);
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
     * @Given /^that the module "([^"]*)" is not active$/
     */
    public function thatTheModuleIsNotActive($module)
    {
        $modules = \Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array) $modules;
        if (!array_key_exists($module, $modulesArray)) {
            return true;
        }
        if ($modulesArray[$module]->is('active')) {
            throw new \Exception($module . ' is active');
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

    /**
     * @Given /^I wait for "([^"]*)" seconds$/
     */
    public function iWaitForSeconds($seconds)
    {
        $ms = $seconds*1000;
        $this->getSession()->wait($ms);
    }

    /**
     * @Given /^I view page for "([^"]*)" seconds$/
     */
    public function iViewPageForSeconds($seconds)
    {
        $ms = $seconds*1000;
        $this->getSession()->wait($ms);
    }

    /**
     * @Given /^system config "([^"]*)" is set to "([^"]*)"$/
     */
    public function systemConfigIsSetTo($path, $value)
    {
        try{
            $config = \Mage::getStoreConfig($path);
            if ($config != $value) {
                throw new \Exception("System config value for $path is not equal to $value, but set to $config");
            }
        }catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @When /^I press button with xpath \'([^\']*)\'$/
     */
    public function iPressButtonWithXpath($xpath)
    {
        $nodeElement = $this->getSession()->getDriver()->find($xpath);

        if (!is_array($nodeElement) && !array_key_exists(0, $nodeElement) && !($nodeElement[0] instanceof \Behat\Mink\Element\NodeElement)) {
            throw new Behat\Mink\Exception\ElementNotFoundException(
                $this->getSession(), 'button', 'xpath', $xpath
            );
        }

        $nodeElement[0]->press();
    }

    /**
     * @When /^I click link with xpath \'([^\']*)\'$/
     */
    public function iClickLinkWithXpath($xpath)
    {
        $nodeElement = $this->getSession()->getDriver()->find($xpath);

        if (!is_array($nodeElement) && !array_key_exists(0, $nodeElement) && !($nodeElement[0] instanceof \Behat\Mink\Element\NodeElement)) {
            throw new \Behat\Mink\Exception\ElementNotFoundException(
                $this->getSession(), 'a', 'xpath', $xpath
            );
        }

        $nodeElement[0]->click();
    }


    /**
     * @Given /^allow shipping to multiple addresses is set to "([^"]*)"$/
     */
    public function allowShippingToMultipleAddressesIsSetTo($value)
    {
        $valueAsInt = strtolower($value) === 'yes' ? 1 : 0;
        $config = \Mage::getStoreConfig('shipping/option/checkout_multiple');

        if ($config != $valueAsInt) {
            throw new \Exception("System config value for shipping/option/checkout_multiple is not equal to $value, but set to $config");
        }
    }

    /**
     * @When /^I add the product to cart$/
     */
    public function iAddTheProductToCart()
    {
        $this->getSession()->getPage()->pressButton("Add to Cart");
    }

    /**
     * @Given /^I proceed to checkout$/
     */
    public function iProceedToCheckout()
    {
        $this->getSession()->getPage()->pressButton("Proceed to Checkout");
    }

    /**
     * @When /^I proceed to checkout by pressing "([^"]*)"$/
     */
    public function iProceedToCheckoutByPressing($button)
    {
        $this->getSession()->getPage()->pressButton($button);
    }

    /**
     * @Given /^I add to cart product with sku "([^"]*)"$/
     */
    public function iAddToCartProductWithSku($sku)
    {
        $productId = \Mage::getModel('catalog/product')->getIdBySku($sku);
        $product = \Mage::getModel('catalog/product')->load($productId);
        $this->getSession()->visit($this->getMinkParameter('base_url') . '/' . $product->getUrlPath());
        $this->iAddTheProductToCart();
    }

    /**
     * @Given /^I add to cart product with sku "([^"]*)" by pressing "([^"]*)"$/
     */
    public function iAddToCartProductWithSkuByPressing($sku, $button)
    {
        $productId = \Mage::getModel('catalog/product')->getIdBySku($sku);
        $product = \Mage::getModel('catalog/product')->load($productId);
        $this->getSession()->visit($this->getMinkParameter('base_url') . '/' . $product->getUrlPath());
        $this->_iPressButton($button);
    }

    /**
     * @Given /^I am on product "([^"]*)" page$/
     */
    public function iAmOnProductPage($sku)
    {
        try{
            $productId = \Mage::getModel('catalog/product')->getIdBySku($sku);
            $product = \Mage::getModel('catalog/product')->load($productId);
            $this->getSession()->visit($this->getMinkParameter('base_url') . '/' . $product->getUrlPath());
        }catch(Exception $e) {
            throw new \Exception('It was not possible to assert you are on page for product with sku ' . $sku);
        }
    }
}

