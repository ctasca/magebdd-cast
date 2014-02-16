<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RedboxDigital_Skypeid_Model_SetupSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith("core_setup");
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('RedboxDigital_Skypeid_Model_Setup');
    }

    function it_defines_a_costant_holding_the_attribute_code_that_should_be_skype_underscore_id()
    {
        $this->getAttributeCode()->shouldBe("skype_id");
    }

    function it_returns_the_customer_entity_id()
    {
        $this->getEntityTypeId('customer')->shouldBeNumeric();
    }

    function it_determines_whether_the_skypeid_customer_attribute_already_exists()
    {
        $this->isSkypeidAnExistingCustomerAttribute()->shouldBeBool();
    }

    function it_creates_the_skype_id_customer_attribute_if_does_not_exist()
    {
        $this->createSkypeidCustomerAttribute()->shouldReturn(true);
    }

    function it_adds_skypeid_to_customer_registration_form()
    {
        $this->addSkypeidToCustomerRegistrationForm()->shouldReturn(true);
    }

    function it_adds_skypeid_to_adminhtml_customer_form()
    {
        $this->addSkypeidToAdminhtmlCustomerForm()->shouldReturn(true);
    }

    function it_adds_skypeid_to_customer_account_edit_form()
    {
        $this->addSkypeidToCustomerAccountEditForm()->shouldReturn(true);
    }

    function it_sets_skype_id_attribute_used_in_forms_data()
    {
        $this->setSkypeidAttributeUsedInFormsData()->shouldReturnAnInstanceOf("Mage_Core_Model_Abstract");
    }
}
