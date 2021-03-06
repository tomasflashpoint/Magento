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
class Buckaroo_Buckaroo3Extended_Block_PaymentMethods_Capayable_Checkout_Form
    extends Buckaroo_Buckaroo3Extended_Block_PaymentMethods_Checkout_Form_Abstract
{
    /**
     * Buckaroo_Buckaroo3Extended_Block_PaymentMethods_Capayable_Checkout_Form constructor.
     */
    public function __construct()
    {
        $this->setTemplate('buckaroo3extended/capayable/checkout/form.phtml');
        parent::_construct();
    }

    /**
     * @return string|int
     */
    public function getOrderAs()
    {
        return $this->getSession()->getData($this->getMethodCode() . '_BPE_OrderAs');
    }

    /**
     * @return string|int
     */
    public function getCompanyCOCRegistration()
    {
        return $this->getSession()->getData($this->getMethodCode() . '_BPE_CompanyCOCRegistration');
    }

    /**
     * @return string
     */
    public function getCompanyName()
    {
        return $this->getSession()->getData($this->getMethodCode() . '_BPE_CompanyName');
    }
}
