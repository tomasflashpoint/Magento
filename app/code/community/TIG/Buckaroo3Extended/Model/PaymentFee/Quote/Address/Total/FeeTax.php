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
class TIG_Buckaroo3Extended_Model_PaymentFee_Quote_Address_Total_FeeTax
    extends TIG_Buckaroo3Extended_Model_PaymentFee_Quote_Address_Total_Fee_Abstract
{
    /**
     * The code of this 'total'.
     *
     * @var string
     */
    protected $_totalCode = 'buckaroo_fee_tax';

    /**
     * Collect the Buckaroo Payment fee tax for the given address.
     *
     * @param Mage_Sales_Model_Quote_Address $address
     *
     * @return $this
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        /**
         * We can only add the fee to the shipping address.
         */
        if ($address->getAddressType() !='shipping') {
            return $this;
        }

        $quote = $address->getQuote();
        $store = $quote->getStore();

        if (!$quote->getId()) {
            return $this;
        }

        /**
         * First, reset the fee amounts to 0 for this address and the quote.
         */
        $address->setBuckarooFeeTax(0)
                ->setBaseBuckarooFeeTax(0);

        $quote->setBuckarooFeeTax(0)
              ->setBaseBuckarooFeeTax(0);

        $items = $address->getAllItems();
        if (empty($items)) {
            return $this;
        }

        if (!$address->getBuckarooFee() || !$address->getBaseBuckarooFee()) {
            return $this;
        }

        $items = $address->getAllItems();
        if (empty($items)) {
            return $this;
        }

        /**
         * Get the tax request and corresponding tax rate.
         */
        $taxRequest = $this->_getBuckarooFeeTaxRequest($quote);

        if (!$taxRequest) {
            return $this;
        }

        $taxRate = $this->_getBuckarooFeeTaxRate($taxRequest);

        if (!$taxRate || $taxRate <= 0) {
            return $this;
        }

        /**
         * Calculate the tax for the fee using the tax rate.
         */
        $paymentMethod = $quote->getPayment()->getMethod();

        $baseFee = $address->getBaseBuckarooFee();

        $fee     = $store->convertPrice($baseFee);

        $feeTax     = $this->_getBuckarooFeeTax($address, $taxRate, $fee, false);
        $baseFeeTax = $this->_getBaseBuckarooFeeTax($address, $taxRate, $baseFee, false);

        /**
         * Get all taxes that were applied for this tax request.
         */
        $appliedRates = Mage::getSingleton('tax/calculation')
                            ->getAppliedRates($taxRequest);

        /**
         * Save the newly applied taxes.
         */
        $this->_saveAppliedTaxes(
            $address,
            $appliedRates,
            $feeTax,
            $baseFeeTax,
            $taxRate
        );

        /**
         * Update the total amounts.
         */
        $address->setTaxAmount($address->getTaxAmount() + $feeTax)
                ->setBaseTaxAmount($address->getBaseTaxAmount() + $baseFeeTax)
                ->setBuckarooFeeTax($feeTax)
                ->setBaseBuckarooFeeTax($baseFeeTax);

        $address->addTotalAmount('nominal_tax', $feeTax);
        $address->addBaseTotalAmount('nominal_tax', $baseFeeTax);

        $quote->setBuckarooFeeTax($feeTax)
              ->setBaseBuckarooFeeTax($baseFeeTax);

        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        return $this;
    }
}
