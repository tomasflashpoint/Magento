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
$installer = $this;

$installer->startSetup();

if ($installer->tableExists('tig_buckaroo_giftcard')) {
    $installer->run(
        "RENAME TABLE tig_buckaroo_giftcard TO buckaroo_giftcard;"
    );

    $installer->run(
        "ALTER TABLE buckaroo_giftcard DROP KEY UNQ_TIG_BUCKAROO_GIFTCARD_SERVICECODE, ADD UNIQUE KEY UNQ_BUCKAROO_GIFTCARD_SERVICECODE (servicecode);"
    );

    $installer->run(
        "ALTER TABLE buckaroo_giftcard COMMENT = 'Buckaroo Giftcard';"
    );
}

$installer->endSetup();
