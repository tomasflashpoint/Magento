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
class Buckaroo_Buckaroo3Extended_Block_PaymentMethods_Creditcard_Checkout_Form extends Buckaroo_Buckaroo3Extended_Block_PaymentMethods_Checkout_Form_Abstract
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->setTemplate('buckaroo3extended/creditcard/checkout/form.phtml');
        parent::_construct();
    }

    public function designValue()
    {
        $storeId = Mage::app()->getStore()->getStoreId();

        if(Mage::getStoreConfig('buckaroo/buckaroo3extended_creditcard/design', $storeId)) {
            return 'styled';
        }

        return 'blank';
    }

    public function getIssuers()
    {
        return Mage::getStoreConfig(
            'buckaroo/' . $this->getMethodCode() . '/issuers', Mage::app()->getStore()->getStoreId()
        );
    }

    public function selectedIssuers()
    {
       $issuers = Buckaroo_Buckaroo3Extended_Model_Sources_CreditcardIssuers::toOptionArray();
       $allowed = explode(',', $this->getIssuers());

       foreach ($issuers as $key => $issuer) {
           if (!in_array($issuer['value'], $allowed)) {
               unset($issuers[$key]);
           }
       }

       return $issuers;
    }
}