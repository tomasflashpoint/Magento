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
class Buckaroo_Buckaroo3Extended_Block_PaymentMethods_Afterpay_Checkout_FormTest
    extends Buckaroo_Buckaroo3Extended_Test_Framework_Buckaroo_Test_TestCase
{
    /** @var null|Buckaroo_Buckaroo3Extended_Block_PaymentMethods_Afterpay_Checkout_Form */
    protected $_instance = null;

    /** @var Mage_Sales_Model_Quote|PHPUnit_Framework_MockObject_MockObject */
    protected $_quoteMock;

    public function setUp()
    {
        $this->registerMockSessions('checkout');

        $this->_quoteMock = $this->getMockBuilder('Mage_Sales_Model_Quote')->disableOriginalConstructor()->getMock();

        $checkoutSession = Mage::getSingleton('checkout/session');
        $checkoutSession->method('getQuote')->willReturn($this->_quoteMock);
    }

    /**
     * @return Buckaroo_Buckaroo3Extended_Block_PaymentMethods_Afterpay_Checkout_Form
     */
    protected function _getInstance()
    {
        if ($this->_instance === null) {
            $this->_instance = new Buckaroo_Buckaroo3Extended_Block_PaymentMethods_Afterpay_Checkout_Form();
        }

        return $this->_instance;
    }

    /**
     * @return array
     */
    public function getB2CUrlProvider()
    {
        return array(
            'BE' => array(
                'BE',
                'https://www.afterpay.be/be/footer/betalen-met-afterpay/betalingsvoorwaarden'
            ),
            'NL' => array(
                'NL',
                'https://www.afterpay.nl/nl/algemeen/betalen-met-afterpay/betalingsvoorwaarden'
            ),
            'DE' => array(
                'DE',
                'https://www.afterpay.nl/nl/algemeen/betalen-met-afterpay/betalingsvoorwaarden'
            )
        );
    }

    /**
     * @param $country
     * @param $expected
     *
     * @dataProvider getB2CUrlProvider
     */
    public function testGetB2CUrl($country, $expected)
    {
        $addressMock = $this->getMockBuilder('Mage_Customer_Model_Address')->setMethods(array('getCountry'))->getMock();
        $addressMock->expects($this->once())->method('getCountry')->willReturn($country);

        $this->_quoteMock->expects($this->atLeastOnce())->method('getBillingAddress')->willReturn($addressMock);

        $instance = $this->_getInstance();
        $result = $instance->getB2CUrl();
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getB2BUrlProvider()
    {
        return array(
            'BE' => array(
                'BE',
                'https://www.afterpay.nl/nl/algemeen/betalen-met-afterpay/betalingsvoorwaarden'
            ),
            'NL' => array(
                'NL',
                'https://www.afterpay.nl/nl/algemeen/zakelijke-partners/betalingsvoorwaarden-zakelijk'
            )
        );
    }

    /**
     * @param $country
     * @param $expected
     *
     * @dataProvider getB2BUrlProvider
     */
    public function testGetB2BUrl($country, $expected)
    {
        $addressMock = $this->getMockBuilder('Mage_Customer_Model_Address')->setMethods(array('getCountry'))->getMock();
        $addressMock->expects($this->once())->method('getCountry')->willReturn($country);

        $this->_quoteMock->expects($this->atLeastOnce())->method('getBillingAddress')->willReturn($addressMock);

        $instance = $this->_getInstance();
        $result = $instance->getB2BUrl();
        $this->assertEquals($expected, $result);
    }
}
