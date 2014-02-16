<?php

class RedboxDigital_Skypeid_Model_Setup extends Mage_Customer_Model_Entity_Setup
{
    const ENTITY = "customer";
    const SKYPEID_ATTRIBUTE_CODE = "skype_id";
    const SKYPEID_ATTRIBUTE_LABEL = "Skype Id";
    const CUSTOMER_ACCOUNT_CREATE_FORM = "customer_account_create";
    const CUSTOMER_ACCOUNT_EDIT_FORM = "customer_account_edit";
    const ADMINHTML_CUSTOMER_FORM = "adminhtml_customer";
    private $_usedInForms = array();


    /**
     * @return bool
     */
    public function isSkypeidAnExistingCustomerAttribute()
    {
        return $this->getAttribute(static::ENTITY, static::SKYPEID_ATTRIBUTE_CODE) === false ? false : true;
    }

    /**
     * Creates the skype_id attributes if it does not exist, and returns true.
     * Returns false otherwise.
     *
     * If skype_id is an existing customer attribute, it gets deleted before re-creating it.
     *
     * @return bool
     */
    public function createSkypeidCustomerAttribute()
    {
        if ($this->isSkypeidAnExistingCustomerAttribute() === true) {
            $this->removeAttribute($this->getEntityTypeId(static::ENTITY), static::SKYPEID_ATTRIBUTE_CODE);
        }
        return $this->createSkypeIdCustomerAttributeIfItDoesNotExist();
    }

    /**
     * @return string
     */
    public function getAttributeCode()
    {
        return static::SKYPEID_ATTRIBUTE_CODE;
    }

    /**
     * @return bool
     */
    public function addSkypeidToCustomerRegistrationForm()
    {
        $this->_usedInForms[] = static::CUSTOMER_ACCOUNT_CREATE_FORM;
        return in_array(static::CUSTOMER_ACCOUNT_CREATE_FORM, $this->_usedInForms);
    }

    /**
     * @return bool
     */
    public function addSkypeidToAdminhtmlCustomerForm()
    {
        $this->_usedInForms[] = static::ADMINHTML_CUSTOMER_FORM;
        return in_array(static::ADMINHTML_CUSTOMER_FORM, $this->_usedInForms);
    }

    /**
     * @return bool
     */
    public function addSkypeidToCustomerAccountEditForm()
    {
        $this->_usedInForms[] = static::CUSTOMER_ACCOUNT_EDIT_FORM;
        return in_array(static::CUSTOMER_ACCOUNT_EDIT_FORM, $this->_usedInForms);
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    public function setSkypeidAttributeUsedInFormsData()
    {
        $this->addSkypeidToAdminhtmlCustomerForm();
        $this->addSkypeidToCustomerRegistrationForm();
        $this->addSkypeidToCustomerAccountEditForm();
        $skypeidAttribute = $this->getSkypeidAttribute();
        $skypeidAttribute->setData('used_in_forms', $this->_usedInForms);
        return $skypeidAttribute->save();
    }

    /**
     * @return bool
     */
    protected function createSkypeIdCustomerAttributeIfItDoesNotExist()
    {
        if (!$this->isSkypeidAnExistingCustomerAttribute()) {
            $this->addSkypeidAttribute();
            $this->addSkypeidAttributeToDefaultSetAndGroup();
            $this->setSkypeidAttributeUsedInFormsData();
            return true;
        }
        return false;
    }

    private function addSkypeidAttribute()
    {
        $this->addAttribute(static::ENTITY, static::SKYPEID_ATTRIBUTE_CODE, array(
            'type' => 'varchar',
            'label' => static::SKYPEID_ATTRIBUTE_LABEL,
            'input' => 'text',
            'visible' => 1,
            'required' => 1,
            'user_defined' => 1,
            'used_for_price_rules' => 0,
        ));
    }

    private function addSkypeidAttributeToDefaultSetAndGroup()
    {
        $attrSetId = $this->getDefaultAttributeSetId(static::ENTITY);
        $attrGroupId = $this->getDefaultAttributeGroupId(static::ENTITY, $attrSetId);
        $this->addAttributeToSet(static::ENTITY, $attrSetId, $attrGroupId, static::SKYPEID_ATTRIBUTE_CODE);
    }

    /**
     * @return false|Mage_Eav_Model_Entity_Attribute_Abstract
     */
    private function  getSkypeidAttribute()
    {
        return Mage::getSingleton('eav/config')->getAttribute(static::ENTITY, static::SKYPEID_ATTRIBUTE_CODE);
    }
}