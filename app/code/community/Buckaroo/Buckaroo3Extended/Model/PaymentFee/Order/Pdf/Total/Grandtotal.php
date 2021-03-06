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
 *
 * @method Mage_Sales_Model_Order                                                                  getOrder()
 * @method Mage_Sales_Model_Order|Mage_Sales_Model_Order_Invoice|Mage_Sales_Model_Order_Creditmemo getSource()
 * @method int|string                                                                              getFontSize()
 * @method string                                                                                  getAmountPrefix()
 */
class Buckaroo_Buckaroo3Extended_Model_PaymentFee_Order_Pdf_Total_Grandtotal extends Mage_Tax_Model_Sales_Pdf_Grandtotal
{
    /**
     * Get array of arrays with tax information for display in PDF
     * array(
     *  $index => array(
     *      'amount'   => $amount,
     *      'label'    => $label,
     *      'font_size'=> $font_size
     *  )
     * )
     * @return array
     */
    public function getFullTaxInfo()
    {
        $fontSize       = $this->getFontSize() ? $this->getFontSize() : 7;

        if (method_exists($this, '_getCalculatedTaxes')) {
            $taxClassAmount = $this->_getCalculatedTaxes();
        } else {
            $taxClassAmount = Mage::helper('tax')->getCalculatedTaxes($this->getOrder());
        }

        if (method_exists($this, '_getShippingTax')) {
            $shippingTax = $this->_getShippingTax();
        } else {
            $shippingTax = Mage::helper('tax')->getShippingTax($this->getOrder());
        }

        $taxClassAmount = array_merge($taxClassAmount, $shippingTax);

        /**
         * Add the Buckaroo Payment fee tax info.
         */
        $taxClassAmount = Mage::helper('buckaroo3extended')->addBuckarooFeeTaxInfo(
            $taxClassAmount,
            $this->getSource(),
            $this->getOrder()
        );

        if (!empty($taxClassAmount)) {
            foreach ($taxClassAmount as &$tax) {
                $percent          = $tax['percent'] ? ' (' . $tax['percent']. '%)' : '';
                $tax['amount']    = $this->getAmountPrefix() . $this->getOrder()->formatPriceTxt($tax['tax_amount']);
                $tax['label']     = $this->_getTaxHelper()->__($tax['title']) . $percent . ':';
                $tax['font_size'] = $fontSize;
            }
        } else {
            $fullInfo = $this->_getFullRateInfo();
            $taxInfo = array();

            if ($fullInfo) {
                foreach ($fullInfo as $info) {
                    if (isset($info['hidden']) && $info['hidden']) {
                        continue;
                    }

                    $_amount = $info['amount'];

                    foreach ($info['rates'] as $rate) {
                        $percent = $rate['percent'] ? ' (' . $rate['percent']. '%)' : '';

                        $taxInfo[] = array(
                            'amount'    => $this->getAmountPrefix() . $this->getOrder()->formatPriceTxt($_amount),
                            'label'     => $this->_getTaxHelper()->__($rate['title']) . $percent . ':',
                            'font_size' => $fontSize
                        );
                    }
                }
            }

            $taxClassAmount = $taxInfo;
        }

        return $taxClassAmount;
    }

    /**
     * @return Mage_Tax_Helper_Data
     */
    protected function _getTaxHelper()
    {
        return Mage::helper('tax');
    }
}
