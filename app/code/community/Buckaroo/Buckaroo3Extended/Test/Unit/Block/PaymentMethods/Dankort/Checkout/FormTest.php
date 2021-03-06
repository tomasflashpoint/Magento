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
class Buckaroo_Buckaroo3Extended_Test_Unit_Block_PaymentMethods_Dankort_Checkout_FormTest
    extends Buckaroo_Buckaroo3Extended_Test_Framework_Buckaroo_Test_TestCase
{
    /** @var null|Buckaroo_Buckaroo3Extended_Block_PaymentMethods_Dankort_Checkout_Form */
    protected $_instance = null;

    public function setUp()
    {
        $this->registerMockSessions('checkout');

        $session = Mage::getSingleton('checkout/session');
        $session->expects($this->exactly(2))->method('getQuote')->willReturnSelf();
    }

    /**
     * @return Buckaroo_Buckaroo3Extended_Block_PaymentMethods_Dankort_Checkout_Form
     */
    protected function _getInstance()
    {
        if ($this->_instance === null) {
            $this->_instance = new Buckaroo_Buckaroo3Extended_Block_PaymentMethods_Dankort_Checkout_Form();
        }

        return $this->_instance;
    }

    public function testGetTemplate()
    {
        $instance = $this->_getInstance();
        $template = $instance->getTemplate();
        $this->assertEquals('buckaroo3extended/dankort/checkout/form.phtml', $template);
    }
}
