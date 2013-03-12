<?php
/**
 * MageUp Navigation module
 * @method MageUp_Navigation_Block_Menu getMenu
 *
 * @category    MageUp
 * @package     MageUp_Navigation
 * @copyright   Copyright (c) 2013 MageUp (http://www.mageup.com)
 * @license     http://www.gnu.org/licenses/gpl.html  Open Source GPL 3.0 license
 */
class MageUp_Navigation_Block_Menu_Item extends Mage_Core_Block_Template
{
    /**
     * menu item model
     * @var Varien_Data_Tree_Node
     */
    protected $_item;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mageup/navigation/menu/item.phtml');
    }

    /**
     * @param Varien_Data_Tree_Node $item
     * @return MageUp_Navigation_Block_Menu_Item
     */
    public function assignItem(Varien_Data_Tree_Node $item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     * @return string
     */
    public function getLinkUrl()
    {
        $url = $this->_item->getUrl();
        return $url;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        $url = $this->_item->getTitle();
        return $url;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return $this->_item->hasChildren();
    }

    /**
     * @return string
     */
    public function getChildrenListClass()
    {
        $level = $this->_item->getLevel() - 1;
        return "level$level";
    }

    /**
     * @param
     * @return string
     */
    public function getChildrenHtml()
    {
        return $this->getMenu()->renderItems($this->_item->getChildren());
    }

    /**
     * @return string
     */
    public function getLiClass()
    {
        $classes = array();
        $classes[] = $this->_item->getClass();
        $level = $this->_item->getLevel() - 1;
        $classes[] = "level$level";
        if ($this->_item->getHasChildren()) {
            $classes[] = 'parent';
        }
        if ($level == 0) {
            $classes[] = 'level-top';
        }
        if ($this->isItemActive($this->_item)) {
            $classes[] = 'active';
        }
        if ($this->_item->getIsFirst()) {
            $classes[] = 'first';
        }
        if ($this->_item->getIsLast()) {
            $classes[] = 'last';
        }
        return implode(' ', $classes);
    }

    /**
     * @return string
     */
    public function getAClass()
    {
        $level = $this->_item->getLevel() - 1;
        if ($level == 0) {
            return 'level-top';
        }
        return '';
    }

    /**
     * Is link active?
     *
     * @param   Varien_Object $item
     * @return  bool
     */
    public function isItemActive($item)
    {
        return in_array($item->getId(), $this->getMenu()->getActiveIds());
    }
}