<?php
/**
 * Maketok Navigation module
 *
 * @category    Maketok
 * @package     Maketok_Navigation
 * @copyright   Copyright (c) 2013 Maketok (http://www.maketok.com)
 * @license     http://www.gnu.org/licenses/gpl.html  Open Source GPL 3.0 license
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/** @var $adapter Varien_Db_Adapter_Pdo_Mysql */
$adapter = $installer->getConnection();

/**
 * Create table 'maketok_nav/menuitem'
 */
$table = $adapter->newTable($installer->getTable('maketok_nav/menuitem'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary'  => true,
), 'Entity Id')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned'  => true,
    'nullable'  => false,
    'default'   => '0',
), 'Parent Node ID')
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned'  => true,
    'nullable'  => false,
    'default'   => '0',
), 'Category ref ID')
    ->addColumn('page_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned'  => true,
    'nullable'  => false,
    'default'   => '0',
), 'CMS Page ref ID')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
), 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
), 'Update Time')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'nullable'  => false,
), 'Position')
    ->addColumn('level', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'nullable'  => false,
    'default'   => '0',
), 'Tree Level')
    ->addColumn('children_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'nullable'  => false,
), 'Child Count')
    ->addIndex($installer->getIdxName('maketok_nav/menuitem', array('level')), array('level'))
    ->setComment('Menu Items');
$adapter->createTable($table);

/**
 * Create table 'maketok_nav/menuitem_store'
 */
$table = $adapter->newTable($installer->getTable('maketok_nav/menuitem_store'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary'  => true,
), 'Value Id')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => false
), 'Title of menu item')
    ->addColumn('url', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => false
), 'url of menu item')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned'  => true,
    'nullable'  => false,
    'default'   => '0',
), 'Parent Node ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
    'unsigned'  => true,
    'nullable'  => false,
    'default'   => '0',
), 'Store ID')
    ->addIndex($installer->getIdxName('maketok_nav/menuitem_store', array('entity_id')),
    array('entity_id'))
    ->addIndex($installer->getIdxName('maketok_nav/menuitem_store', array('store_id')),
    array('store_id'))
    ->addForeignKey(
    $installer->getFkName('maketok_nav/menuitem_store', 'entity_id', 'maketok_nav/menuitem', 'entity_id'),
    'entity_id', $installer->getTable('maketok_nav/menuitem'), 'entity_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
    $installer->getFkName('maketok_nav/menuitem_store', 'store_id', 'core/store', 'store_id'),
    'store_id', $installer->getTable('core/store'), 'store_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Menu Items Store scoped values');
$adapter->createTable($table);

$installer->endSetup();