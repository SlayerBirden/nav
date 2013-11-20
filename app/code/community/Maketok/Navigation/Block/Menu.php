<?php
/**
 * Maketok Navigation module
 *
 * @category    Maketok
 * @package     Maketok_Navigation
 * @copyright   Copyright (c) 2013 Maketok (http://www.maketok.com)
 * @license     http://www.gnu.org/licenses/gpl.html  Open Source GPL 3.0 license
 */
class Maketok_Navigation_Block_Menu extends Mage_Core_Block_Template
{
    /** @var Varien_Data_Tree_Node */
    protected $_tree;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('maketok/navigation/menu.phtml');
        $this->_buildTree();
    }

    protected function _buildTree()
    {
        $this->_tree = new Varien_Data_Tree_Node(array(), 'root', new Varien_Data_Tree());
        $this->_addChildrenToTree(0, $this->_tree);
    }

    protected function _addChildrenToTree($parent = 0, Varien_Data_Tree_Node $parentNode)
    {
        /** @var $items Maketok_Navigation_Model_Resource_Menuitem_Collection */
        $items = Mage::getResourceModel('maketok_nav/menuitem_collection');
        $items->addParentFilter($parent)
            ->sort();
        $i=0;
        foreach ($items as $item) {
            $hasChildren = $item->hasChildren();
            $data = array(
                'id' => $item->getId(),
                'title' => $item->getTitle(),
                'url' => $item->getRealUrl(),
                'class' => $item->getClass(),
                'level' => $item->getLevel(),
                'position' => $item->getPosition(),
                'has_children' => $hasChildren,
                'is_first' => $i == 0,
                'is_last' => $i == (count($items) - 1),
            );
            $node = new Varien_Data_Tree_Node($data, 'id', $parentNode->getTree(), $parentNode);
            $parentNode->addChild($node);
            if ($hasChildren) {
                $this->_addChildrenToTree($item->getId(), $node);
            }
            ++$i;
        }
    }

    /**
     * @return Varien_Data_Tree_Node
     */
    public function getTree()
    {
        return $this->_tree;
    }

    public function renderMenu()
    {
        $html = $this->renderItems($this->_tree->getChildren());
        return $html;
    }

    public function renderItems(Varien_Data_Tree_Node_Collection $items)
    {
        $html = '';
        foreach($items as $child) {
            $itemBlock = $this->getLayout()
                ->createBlock('maketok_nav/menu_item', 'item-'.$child->getId(), array('menu' => $this));
            $itemBlock->assignItem($child);
            $html .= $itemBlock->toHtml();
        }
        return $html;
    }

    public function getIsEmpty()
    {
        return !$this->_tree->hasChildren();
    }

    /**
     * @return Varien_Data_Tree_Node|bool
     */
    public function getActiveItem()
    {
        $currentUrl = Mage::helper("core/url")->getCurrentUrl();
        foreach ($this->getTree()->getAllChildNodes() as $node) {
            $url = $node->getUrl();
            $ex = explode('?', $url);
            $url = $ex[0]; // strip query part
            if ($url == $currentUrl) {
                return $node;
            }
        }
        return false;
    }

    public function getActiveIds()
    {
        $node = $this->getActiveItem();
        if (!$node) {
            return array();
        }
        $ids = array($node->getId());
        while ($node->getLevel() > 1) {
            $node = $node->getParent();
            if ($node) {
                $ids[] = $node->getId();
            } else {
                // if no node found break from the cycle
                break;
            }
        }
        return $ids;
    }
}