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
class TIG_Buckaroo3Extended_Test_Unit_Block_PaymentMethods_Klarna_Adminhtml_System_Config_AdvancedbtnTest
    extends TIG_Buckaroo3Extended_Test_Framework_TIG_Test_TestCase
{
    /** @var null|TIG_Buckaroo3Extended_Block_PaymentMethods_Klarna_Adminhtml_System_Config_Advancedbtn */
    protected $_instance = null;

    /**
     * @return null|TIG_Buckaroo3Extended_Block_PaymentMethods_Klarna_Adminhtml_System_Config_Advancedbtn
     */
    protected function _getInstance()
    {
        if ($this->_instance === null) {
            $className = 'TIG_Buckaroo3Extended_Block_PaymentMethods_Klarna_Adminhtml_System_Config_Advancedbtn';
            $this->_instance = new $className();
        }

        return $this->_instance;
    }

    public function testRender()
    {
        $instance = $this->_getInstance();
        $result = $instance->toHtml();

        $this->assertInternalType('string', $result);
    }
}
