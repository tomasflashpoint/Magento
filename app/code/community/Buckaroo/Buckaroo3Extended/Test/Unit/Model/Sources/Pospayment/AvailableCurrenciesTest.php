<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * It is available through the world-wide-web at this URL:
 * https://tldrlegal.com/license/mit-license
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to support@buckaroo.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@buckaroo.nl for more information.
 *
 * @copyright Copyright (c) Buckaroo B.V.
 * @license   https://tldrlegal.com/license/mit-license
 */
class Buckaroo_Buckaroo3Extended_Test_Unit_Model_Sources_Pospayment_AvailableCurrenciesTest
    extends Buckaroo_Buckaroo3Extended_Test_Framework_Buckaroo_Test_TestCase
{
    /** @var null|Buckaroo_Buckaroo3Extended_Model_Sources_Pospayment_AvailableCurrencies */
    protected $_instance = null;

    /**
     * @return Buckaroo_Buckaroo3Extended_Model_Sources_Pospayment_AvailableCurrencies
     */
    protected function _getInstance()
    {
        if ($this->_instance === null) {
            $this->_instance = new Buckaroo_Buckaroo3Extended_Model_Sources_Pospayment_AvailableCurrencies();
        }

        return $this->_instance;
    }

    public function testToOptionArray()
    {
        $expectedOptions = array('EUR');

        $instance = $this->_getInstance();
        $result = $instance->toOptionArray();

        $this->assertInternalType('array', $result);

        foreach ($result as $currency) {
            $this->assertContains($currency['value'], $expectedOptions);
        }
    }
}
