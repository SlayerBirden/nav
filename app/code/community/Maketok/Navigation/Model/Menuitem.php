<?php
/**
 * Maketok Navigation module
 *
 * @category    Maketok
 * @package     Maketok_Navigation
 * @copyright   Copyright (c) 2013 Maketok (http://www.maketok.com)
 * @license     http://www.gnu.org/licenses/gpl.html  Open Source GPL 3.0 license
 */
class Maketok_Navigation_Model_Menuitem extends Mage_Core_Model_Abstract
{
    public static $mapArray = array(
        'text' => 'title',
        'parentId' => 'parent_id',
        'index' => 'position',
        'depth' => 'level',
    );

    protected function _construct() {
        $this->_init('maketok_nav/menuitem');
    }

    protected function _beforeLoad($id, $field = null) {
        $itemStoreData = $this->getResource()->getItemStoreData($id);
        $this->addData($itemStoreData);
        return parent::_beforeLoad($id, $field);
    }

    protected function _beforeSave()
    {
        $this->_getResource()->addCommitCallback(array($this, 'saveStoreValues'));
        return parent::_beforeSave();
    }

    public function map()
    {
        foreach (self::$mapArray as $key => $newKey) {
            $data = $this->getData($key);
            if (!is_null($data)) {
                $this->setData($newKey, $data);
            }
        }
        return $this;
    }

    public function saveStoreValues()
    {
        $storeId = $this->getStoreId() ? $this->getStoreId() : Mage::app()->getStore()->getId();
        /** @var $read Varien_Db_Adapter_Interface */
        $read = Mage::getModel('core/resource')->getConnection('core_read');
        /** @var $write Varien_Db_Adapter_Interface */
        $write = Mage::getModel('core/resource')->getConnection('core_write');
        $storeTableName = $this->getResource()->getTable('maketok_nav/menuitem_store');
        $select = $read->select()->from($storeTableName, array('COUNT(*)'))->where('entity_id=?', $this->getId())->group('entity_id');
        $count = $read->fetchOne($select);
        if ($count) {
            $write->update($storeTableName, array(
                'url' => $this->getUrl(),
                'title' => $this->getTitle(),
            ), 'entity_id='.$this->getId());
        } else {
            $write->insert($this->getResource()->getTable('maketok_nav/menuitem_store'), array(
                'entity_id' => $this->getId(),
                'url' => $this->getUrl(),
                'title' => $this->getTitle(),
                'store_id' => $storeId,
            ));
        }
        return $this;
    }

    public function hasChildren()
    {
        /** @var $read Varien_Db_Adapter_Interface */
        $read = Mage::getModel('core/resource')->getConnection('core_read');
        $select = $read->select()
            ->from($this->getResource()->getMainTable(),
            array('COUNT(*)'))->where('parent_id=?', $this->getId())
            ->group('entity_id');
        $count = $read->fetchOne($select);
        return (bool) $count;
    }

    protected function _afterDelete()
    {
        parent::_afterDelete();
        if ($this->hasChildren()) {
            $children = $this->getCollection()
                ->addFieldToFilter('parent_id', $this->getId());
            foreach ($children as $child) {
                $child->delete();
            }
        }
    }

    public function getRealUrl()
    {
        $url = $this->getData('url');
        if (!$url || !strlen($url)) {
            $url = $this->getData('default_url');
        }
        if (preg_match('#^http://#', $url)) {
            // we have full url passed, leave it as it is
            return $url;
        }
        $url = preg_replace('#^/#', '', $url);
        $realUrl = Mage::getUrl($url);
        if ($categoryId = $this->getCategoryId()) {
            $realUrl = Mage::helper('catalog/category')->getCategoryUrl(new Varien_Object(array('entity_id' => $categoryId)));
        }
        return $realUrl;
    }

    public function getTitle()
    {
        $title = $this->getData('title');
        if ($title && strlen($title)) {
            return $title;
        } else {
            return $this->getData('default_title');
        }
    }
}