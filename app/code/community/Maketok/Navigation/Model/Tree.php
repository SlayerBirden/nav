<?php
/**
 * Maketok Navigation module
 *
 * @category    Maketok
 * @package     Maketok_Navigation
 * @copyright   Copyright (c) 2013 Maketok (http://www.maketok.com)
 * @license     http://www.gnu.org/licenses/gpl.html  Open Source GPL 3.0 license
 */
class Maketok_Navigation_Model_Tree extends Mage_Core_Model_Abstract
{
    public function getTreeNode($node)
    {
        // create a node from data array
        if (is_array($node)) {
            $node = new Varien_Data_Tree_Node($node, 'entity_id', new Varien_Data_Tree);
        }
        $item = array();
        if ($node instanceof Maketok_Navigation_Model_Menuitem) {
            // revers mapping
            $mappingArray = Maketok_Navigation_Model_Menuitem::$mapArray;
            foreach ($mappingArray as $key => $newKey) {
                $item[$key] = $node->getData($newKey);
            }
        } else {
            $item['text'] = $node->getData('name');
        }
        $item['store']  = (int) Mage::app()->getStore()->getId();
        $item['path'] = $node->getData('path');

        $item['cls'] = 'folder';
        $item['allowDrop'] = true;
        $item['allowDrag'] = true;

        if (!$node->hasChildren()) {
            $item['children'] = array();
        }
        if ($node instanceof Mage_Catalog_Model_Category) {
            $item['category_id']  = $node->getId();
            $item['url'] = $node->getUrlPath();
        } elseif ($node instanceof Mage_Cms_Model_Page) {
            $item['page_id']  = $node->getId();
            $item['url'] = '/'.$node->getData('identifier');
        } else {
            $item['id']  = $node->getId();
            $item['url'] = $node->getData('url');
            $item['category_id']  = $node->getData('category_id');
            $item['page_id']  = $node->getData('page_id');
            if (!$item['category_id'] && !$item['page_id']) {
                $item['url_editable'] = true;
            }
            $item['class'] = $node->getData('class');
        }
        return $item;
    }
}