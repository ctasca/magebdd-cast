<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Mage_Catalog_Model_ProductSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Mage_Catalog_Model_Product');
    }
}
