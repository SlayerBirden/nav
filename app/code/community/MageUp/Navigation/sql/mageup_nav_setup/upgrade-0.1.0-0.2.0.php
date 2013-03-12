<?php
/**
 * MageUp Navigation module
 *
 * @category    MageUp
 * @package     MageUp_Navigation
 * @copyright   Copyright (c) 2013 MageUp (http://www.mageup.com)
 * @license     http://www.gnu.org/licenses/gpl.html  Open Source GPL 3.0 license
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/** @var $adapter Varien_Db_Adapter_Pdo_Mysql */
$adapter = $installer->getConnection();

$adapter->addColumn(
    $installer->getTable('mageup_nav/menuitem'),
    'class',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment' => 'Custom Class',
        'default' => '',
    )
);

$installer->endSetup();