<?php
/**
 * Maketok Navigation module
 *
 * @category    Maketok
 * @package     Maketok_Navigation
 * @copyright   Copyright (c) 2013 Maketok (http://www.maketok.com)
 * @license     http://www.gnu.org/licenses/gpl.html  Open Source GPL 3.0 license
 */
class Maketok_Navigation_Model_Resource_Menuitem_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct(){
        $this->_init('maketok_nav/menuitem');
    }

    protected function _beforeLoad()
    {
        parent::_beforeLoad();
        $this->getSelect()->joinLeft(
            array(
                'ms' => $this->getTable('maketok_nav/menuitem_store')
            ), "main_table.entity_id = ms.entity_id and ms.store_id=". Mage::app()->getStore()->getId(),
            array('ms.title','ms.url')
        )->joinLeft(
            array(
                'ms_default' => $this->getTable('maketok_nav/menuitem_store')
            ), "main_table.entity_id = ms_default.entity_id and ms_default.store_id=". Mage_Core_Model_App::ADMIN_STORE_ID,
            array('default_title'=>'ms_default.title','default_url'=>'ms_default.url')
        );
        return $this;
    }

    public function addParentFilter($parentId)
    {
        $this->addFieldToFilter('parent_id', $parentId);
        return $this;
    }

    /**
     * @return Maketok_Navigation_Model_Resource_Menuitem_Collection
     */
    public function sort()
    {
        $this->setOrder('position', self::SORT_ORDER_ASC);
        return $this;
    }

}