<?php
/**
 * Maketok Navigation module
 *
 * @category    Maketok
 * @package     Maketok_Navigation
 * @copyright   Copyright (c) 2013 Maketok (http://www.maketok.com)
 * @license     http://www.gnu.org/licenses/gpl.html  Open Source GPL 3.0 license
 */
class Maketok_Navigation_Adminhtml_NavController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Get tree nodes
     */
    public function categoriesJsonAction()
    {
        if (!$category = $this->_initCategory()) {
            return;
        }
        $children = array();
        foreach ($category->getChildrenCategories() as $childCategory) {
            $children[] = Mage::getModel('maketok_nav/tree')->getTreeNode($childCategory);
        }
        $this->getResponse()->setBody(
            Zend_Json::encode($children)
        );
    }

    /**
     * @return Mage_Catalog_Model_Category
     */
    protected function _initCategory()
    {
        $categoryId = (int) $this->getRequest()->getParam('category_id',false);
        $category = Mage::getModel('catalog/category');
        if ($categoryId !== false) {
            $category->load($categoryId);
        }
        Mage::register('category', $category);
        Mage::register('current_category', $category);
        return $category;
    }

    /**
     * Get tree nodes
     */
    public function cmsJsonAction()
    {
        $children = array();
        /** @var $collection Mage_Cms_Model_Resource_Page_Collection */
        $collection = Mage::getResourceModel('cms/page_collection');
        $collection->addStoreFilter($this->getStore());
        foreach ($collection as $page) {
            $page->setName($page->getTitle())->setIsActive(true);
            $children[] = Mage::getModel('maketok_nav/tree')->getTreeNode($page);
        }
        $this->getResponse()->setBody(
            Zend_Json::encode($children)
        );
    }

    public function getMenuItemsAction()
    {
        $id = $this->getRequest()->getParam('id',false);
        $collection = Mage::getResourceModel('maketok_nav/menuitem_collection')->addParentFilter($id)->sort();
        $children = array();
        foreach ($collection as $child) {
            $children[] = Mage::getModel('maketok_nav/tree')->getTreeNode($child);
        }
        $this->getResponse()->setBody(
            Zend_Json::encode($children)
        );
    }

    /**
     * @return int
     */
    protected function getStore()
    {
        $storeId = $this->getRequest()->getParam('store', 0);
        return (int) $storeId;
    }

    public function syncMenuItemAction()
    {
        // get Request Body json
        $requestBody = file_get_contents('php://input');
        $isDelete = $this->getRequest()->getParam('isDelete');
        $returnData = new Varien_Object(
            array(
                'success' => true,
                'children' => array(),
                'message' => '',
            )
        );
        if (!empty($requestBody)) {
            // commit changes
            $data = Zend_Json::decode($requestBody);
            // determine what kind of data we got
            // there may be one node or multiple nodes array
            // since one node is an array as well, we need somehow to distinguish them
            if (isset($data['text'])) {
                // single item array
                /** @var $item Maketok_Navigation_Model_Menuitem */
                $item = Mage::getModel('maketok_nav/menuitem');
                if ($data['id']) {
                    $item->load($data['id']);
                }
                try {
                    if ($isDelete) {
                        if (!$item->getId()) {
                            throw new Exception($this->__('Can not find the record to delete'));
                        }
                        $item->delete();
                    } else {
                        $item->addData($data)
                            ->map()
                            ->save();
                        $returnData->setChildren(array(Mage::getModel('maketok_nav/tree')->getTreeNode($item)));
                    }
                } catch (Exception $e) {
                    $returnData->setSuccess(false)->setMessage($e->getMessage());
                }
            } else {
                $items = array();
                $errors = array();

                foreach ($data as $singleItem) {
                    /** @var $item Maketok_Navigation_Model_Menuitem */
                    $item = Mage::getModel('maketok_nav/menuitem');
                    if ($singleItem['id']) {
                        $item->load($singleItem['id']);
                    }
                    $item->addData($singleItem)->map();
                    $items[] = $item;
                }
                try {
                    $updatedItems = array();
                    foreach ($items as $i) {
                        if ($isDelete) {
                            $i->delete();
                        } else {
                            $i->save();
                            $updatedItems[] = Mage::getModel('maketok_nav/tree')->getTreeNode($i);
                        }
                    }
                    if (count($updatedItems)) {
                        $returnData->setChildren($updatedItems);
                    }
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
                if (count($errors)) {
                    $returnData->setSuccess(false)->setMessage(implode("<br/>", $errors));
                }
            }
        }
        $this->getResponse()->setBody(
            Zend_Json::encode($returnData)
        );
        return;
    }
}