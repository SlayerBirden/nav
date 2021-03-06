<?php
/**
 * Maketok Navigation module
 *
 * @category    Maketok
 * @package     Maketok_Navigation
 * @copyright   Copyright (c) 2013 Maketok (http://www.maketok.com)
 * @license     http://www.gnu.org/licenses/gpl.html  Open Source GPL 3.0 license
 */
class Maketok_Navigation_Block_Adminhtml_Menu extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('maketok/navigation/menu.phtml');
    }

    public function getLoadTreeUrl()
    {
        return $this->getUrl('*/*/categoriesJson', array(
            '_current' => true,
            'id' => null,
            'store' => null,
            '_query' => array('isAjax' => 1)
        ));
    }

    public function getLoadCmsUrl()
    {
        return $this->getUrl('*/*/cmsJson', array(
            '_current' => true,
            'id' => null,
            'store' => null,
            '_query' => array('isAjax' => 1)
        ));
    }

    public function getMenuItemsUrl()
    {
        return $this->getUrl('*/*/getMenuItems', array(
            '_current' => true,
            'id' => null,
            'store' => null,
            '_query' => array('isAjax' => 1)
        ));
    }

    public function getSyncAddUpdateUrl()
    {
        return $this->getUrl('*/*/syncMenuItem', array(
            '_current' => true,
            'id' => null,
            'store' => null,
            '_query' => array('isAjax' => 1)
        ));
    }

    public function getSyncDeleteUrl()
    {
        return $this->getUrl('*/*/syncMenuItem', array(
            '_current' => true,
            'id' => null,
            'store' => null,
            'isDelete' => true,
            '_query' => array('isAjax' => 1)
        ));
    }
}