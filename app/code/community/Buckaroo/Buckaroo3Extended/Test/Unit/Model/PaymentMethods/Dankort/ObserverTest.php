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
class Buckaroo_Buckaroo3Extended_Test_Unit_Model_PaymentMethods_Dankort_ObserverTest
    extends Buckaroo_Buckaroo3Extended_Test_Framework_Buckaroo_Test_TestCase
{
    /** @var null|Buckaroo_Buckaroo3Extended_Model_PaymentMethods_Dankort_Observer */
    protected $_instance = null;

    public function setUp()
    {
        $this->registerMockSessions('checkout');
    }

    /**
     * @return null|Buckaroo_Buckaroo3Extended_Model_PaymentMethods_Dankort_Observer
     */
    protected function _getInstance()
    {
        if ($this->_instance === null) {
            $this->_instance = new Buckaroo_Buckaroo3Extended_Model_PaymentMethods_Dankort_Observer();
        }

        return $this->_instance;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Varien_Event_Observer
     */
    protected function getObserverMock()
    {
        $paymentMock = $this->getMockBuilder('Mage_Sales_Model_Order_Payment')
            ->setMethods(array('getMethod'))
            ->getMock();
        $paymentMock->expects($this->once())->method('getMethod')->willReturn('buckaroo3extended_dankort');

        $orderMock = $this->getMockBuilder('Mage_Sales_Model_Order')
            ->setMethods(
                array(
                    'getPayment', 'getTransactionKey', 'getBuckarooSecureEnrolled', 'getBuckarooSecureAuthenticated',
                    'setBuckarooSecureEnrolled', 'setBuckarooSecureAuthenticated', 'save'
                )
            )->getMock();
        $orderMock->expects($this->once())->method('getPayment')->willReturn($paymentMock);

        $requestMock = $this->getMockBuilder('Buckaroo_Buckaroo3Extended_Model_Request_Abstract')
            ->setMethods(array('getOrder', 'getBillingInfo'))
            ->getMock();

        $observerMock = $this->getMockBuilder('Varien_Event_Observer')
            ->setMethods(array('getOrder', 'getRequest'))
            ->getMock();
        $observerMock->method('getOrder')->willReturn($orderMock);
        $observerMock->method('getRequest')->willReturn($requestMock);

        return $observerMock;
    }

    public function testBuckaroo3extended_request_addservices()
    {
        $observerMock = $this->getObserverMock();
        $instance = $this->_getInstance();

        $result = $instance->buckaroo3extended_request_addservices($observerMock);

        $expectedVars = array(
            'services' => array(
                'dankort' => array(
                    'action' => 'Pay',
                    'version' => 1
                )
            )
        );

        $this->assertInstanceOf('Buckaroo_Buckaroo3Extended_Model_PaymentMethods_Dankort_Observer', $result);
        $this->assertEquals($expectedVars, $observerMock->getRequest()->getVars());
    }

    public function testBuckaroo3extended_request_addcustomvars()
    {
        $billingInfoData = array('street' => 'Kabelweg 37', 'city' => 'Amsterdam', 'country' => 'NL');

        $observerMock = $this->getObserverMock();
        $requestMock = $observerMock->getRequest();
        $requestMock->expects($this->once())->method('getBillingInfo')->willReturn($billingInfoData);
        $requestMock->expects($this->exactly(2))->method('getOrder')->willReturn($observerMock->getOrder());

        $instance = $this->_getInstance();
        $result = $instance->buckaroo3extended_request_addcustomvars($observerMock);

        $this->assertInstanceOf('Buckaroo_Buckaroo3Extended_Model_PaymentMethods_Dankort_Observer', $result);
        $this->assertInstanceOf('Mage_Sales_Model_Order', $instance->getOrder());
        $this->assertEquals($requestMock->getOrder(), $instance->getOrder());
        $this->assertEquals($billingInfoData, $instance->getBillingInfo());
    }

    public function testBuckaroo3extended_request_setmethod()
    {
        $observerMock = $this->getObserverMock();

        $instance = $this->_getInstance();
        $result = $instance->buckaroo3extended_request_setmethod($observerMock);

        $this->assertInstanceOf('Buckaroo_Buckaroo3Extended_Model_PaymentMethods_Dankort_Observer', $result);
        $this->assertEquals('dankort', $observerMock->getRequest()->getMethod());
    }

    public function testBuckaroo3extended_refund_request_addservices()
    {
        $observerMock = $this->getObserverMock();

        $instance = $this->_getInstance();
        $result = $instance->buckaroo3extended_refund_request_addservices($observerMock);

        $expectedVars = array(
            'services' => array(
                'dankort' => array(
                    'action' => 'Refund',
                    'version' => 1
                )
            )
        );

        $this->assertInstanceOf('Buckaroo_Buckaroo3Extended_Model_PaymentMethods_Dankort_Observer', $result);
        $this->assertEquals($expectedVars, $observerMock->getRequest()->getVars());
    }

    public function testBuckaroo3extended_refund_request_addcustomvars()
    {
        $observerMock = $this->getMockBuilder('Varien_Event_Observer')->getMock();
        $instance = $this->_getInstance();

        $result = $instance->buckaroo3extended_refund_request_addcustomvars($observerMock);
        $this->assertInstanceOf('Buckaroo_Buckaroo3Extended_Model_PaymentMethods_Dankort_Observer', $result);
    }

    public function testBuckaroo3extended_return_custom_processing()
    {
        $responseArray = array(
            'brq_SERVICE_dankort_Enrolled' => 'Y',
            'brq_SERVICE_dankort_Authentication' => 'A'
        );

        $observerMock = $this->getObserverMock();
        $observerMock->setPostArray($responseArray);

        $orderMock = $observerMock->getOrder();
        $orderMock->expects($this->once())->method('setBuckarooSecureEnrolled')->with(true)->willReturnSelf();
        $orderMock->expects($this->once())->method('setBuckarooSecureAuthenticated')->with(true)->willReturnSelf();
        $orderMock->expects($this->once())->method('getTransactionKey')->willReturn('abc123');

        $instance = $this->_getInstance();

        $result = $instance->buckaroo3extended_return_custom_processing($observerMock);
        $this->assertInstanceOf('Buckaroo_Buckaroo3Extended_Model_PaymentMethods_Dankort_Observer', $result);
    }

    public function testbuckaroo3extended_push_custom_processing_after()
    {
        $observerMock = $this->getObserverMock();

        $orderMock = $observerMock->getOrder();
        $orderMock->expects($this->once())->method('getBuckarooSecureEnrolled')->willReturn(true);
        $orderMock->expects($this->once())->method('getBuckarooSecureAuthenticated')->willReturn(true);

        $instance = $this->_getInstance();

        $result = $instance->buckaroo3extended_push_custom_processing_after($observerMock);
        $this->assertInstanceOf('Buckaroo_Buckaroo3Extended_Model_PaymentMethods_Dankort_Observer', $result);
    }
}
