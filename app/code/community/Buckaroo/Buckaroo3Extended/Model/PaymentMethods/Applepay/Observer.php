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
class Buckaroo_Buckaroo3Extended_Model_PaymentMethods_Applepay_Observer extends Buckaroo_Buckaroo3Extended_Model_Observer_Abstract
{
    protected $_code = 'buckaroo3extended_applepay';

    protected $_method = 'applepay';

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function buckaroo3extended_request_addservices(Varien_Event_Observer $observer)
    {
        if ($this->_isChosenMethod($observer) === false) {
            return $this;
        }

        $request = $observer->getRequest();
        $vars = $request->getVars();

        $array = array(
            $this->_method => array(
                'action'  => 'Pay',
                'version' => 0
            )
        );

        /** @var Mage_Sales_Model_Order $order */
        $order = $request->getOrder();

        /** @var Mage_Sales_Model_Order_Payment $payment */
        $payment = $order->getPayment();
        $payment->setAdditionalInformation('skip_push', 1);
        $payment->save();

        if (array_key_exists('services', $vars) && is_array($vars['services'])) {
            $vars['services'] = array_merge($vars['services'], $array);
        } else {
            $vars['services'] = $array;
        }

        $request->setVars($vars);

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function buckaroo3extended_request_addcustomvars(Varien_Event_Observer $observer)
    {
        if ($this->_isChosenMethod($observer) === false) {
            return $this;
        }
        $request = $observer->getRequest();
        $vars = $request->getVars();

        $this->_billingInfo = $request->getBillingInfo();
        $this->_order = $request->getOrder();

        $_method = $this->getMethod();
        $payment = $this->_order->getPayment();

        $applepayResponse = base64_encode($payment->getAdditionalInformation()['buckaroo3extended_applepay_response']);

        $array = array(
            'PaymentData' => $applepayResponse,
        );

        if (array_key_exists('customVars', $vars) && array_key_exists($_method, $vars['customVars']) && is_array($vars['customVars'][$_method])) {
            $vars['customVars'][$_method] = array_merge($vars['customVars'][$_method], $array);
        } else {
            $vars['customVars'][$_method] = $array;
        }

        $request->setVars($vars);

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function buckaroo3extended_request_setmethod(Varien_Event_Observer $observer)
    {
        if ($this->_isChosenMethod($observer) === false) {
            return $this;
        }

        $request = $observer->getRequest();

        $codeBits = explode('_', $this->_code);
        $code = end($codeBits);

        $request->setMethod($code);

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function buckaroo3extended_refund_request_addservices(Varien_Event_Observer $observer)
    {
        if ($this->_isChosenMethod($observer) === false) {
            return $this;
        }

        $request = $observer->getRequest();
        $this->_order = $request->getOrder();
        $vars = $request->getVars();

        $array = array(
            $this->_method => array(
                'action'  => 'Refund',
                'version' => 1
            )
        );

        if (array_key_exists('services', $vars) && is_array($vars['services'])) {
            $vars['services'] = array_merge($vars['services'], $array);
        } else {
            $vars['services'] = $array;
        }

        $request->setVars($vars);

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function buckaroo3extended_refund_request_addcustomvars(Varien_Event_Observer $observer)
    {
        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function buckaroo3extended_return_custom_processing(Varien_Event_Observer $observer)
    {
        if ($this->_isChosenMethod($observer) === false) {
            return $this;
        }

        $response = $observer->getPostArray();

        $order = $observer->getOrder();
        $enrolled = false;
        $authenticated = false;

        if (isset($response['brq_SERVICE_applepay_Enrolled']) && isset($response['brq_SERVICE_applepay_Authentication'])) {
            $enrolled = $response['brq_SERVICE_applepay_Enrolled'];
            $enrolled = ($enrolled == 'Y') ? true : false;

            /**
             * The status selected below determines how the payment or authorize is processed.
             * Attempt (A) and Yes (Y) will lead to a successful transaction/payment.
             * No (N) / Unknown (U) will lead to a failure.
             */
            $authenticated = $response['brq_SERVICE_applepay_Authentication'];
            $authenticated = ($authenticated == 'Y' || $authenticated == 'A') ? true : false;
        }

        $order->setBuckarooSecureEnrolled($enrolled)->setBuckarooSecureAuthenticated($authenticated)->save();
        if ($order->getTransactionKey()) {
            $this->_updateSecureStatus($enrolled, $authenticated, $order);
        }

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function buckaroo3extended_push_custom_processing_after(Varien_Event_Observer $observer)
    {
        if ($this->_isChosenMethod($observer) === false) {
            return $this;
        }

        $order = $observer->getOrder();
        $enrolled = $order->getBuckarooSecureEnrolled();
        $authenticated = $order->getBuckarooSecureAuthenticated();

        if (is_null($enrolled) || is_null($authenticated)) {
            return $this;
        }

        $this->_updateSecureStatus($enrolled, $authenticated, $order);

        return $this;
    }
}