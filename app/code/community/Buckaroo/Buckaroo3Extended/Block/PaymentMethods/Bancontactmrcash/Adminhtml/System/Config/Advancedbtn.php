<?php
class Buckaroo_Buckaroo3Extended_Block_PaymentMethods_Bancontactmrcash_Adminhtml_System_Config_Advancedbtn
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_template = 'buckaroo3extended/bancontactmrcash_system/config/advancedbtn.phtml';

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    // @codingStandardsIgnoreLine
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->toHtml();
    }
}
