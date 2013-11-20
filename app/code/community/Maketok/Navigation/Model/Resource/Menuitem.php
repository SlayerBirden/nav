<?php
/**
 * Maketok Navigation module
 *
 * @category    Maketok
 * @package     Maketok_Navigation
 * @copyright   Copyright (c) 2013 Maketok (http://www.maketok.com)
 * @license     http://www.gnu.org/licenses/gpl.html  Open Source GPL 3.0 license
 */
class Maketok_Navigation_Model_Resource_Menuitem extends Mage_Core_Model_Resource_Db_Abstract
{
    protected $_storeTable;

    protected function _construct()
    {
        $this->_init('maketok_nav/menuitem', 'entity_id');
        $this->_storeTable = $this->getTable('maketok_nav/menuitem_store');
    }

    public function getItemStoreData($itemId, $storeId = false) {
        if (!$storeId) {
            $storeId = Mage::app()->getStore()->getId();
        }
        $select = $this->_getReadAdapter()->select()
            ->from($this->_storeTable, array('url', 'title'))
            ->where("entity_id=:entity_id")
            ->where("store_id=:store_id");
        return $this->_getReadAdapter()->fetchPairs($select,
            array(':entity_id' => $itemId, ':store_id' => $storeId));
    }
}