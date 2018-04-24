<?php

namespace DNAFactory\Megamenu\Block;

use \Magento\Framework\View\Element\Template;

class Megamenu extends Template
{
    protected $_categoryHelper;
    protected $_categoryFlatConfig;
    protected $_topMenu;
    protected $_categoryFactory;
    protected $_helper;
    protected $_filterProvider;
    protected $_blockFactory;
    protected $_megamenuConfig;
    protected $_storeManager;
    protected $_dropDownStyle;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \DNAFactory\Megamenu\Helper\Data $helper,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Theme\Block\Html\Topmenu $topMenu,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \DNAFactory\Megamenu\Block\Megamenu\Dropdown\Style $dropDownStyle
    ) {
        $this->_categoryHelper = $categoryHelper;
        $this->_categoryFlatConfig = $categoryFlatState;
        $this->_categoryFactory = $categoryFactory;
        $this->_topMenu = $topMenu;
        $this->_helper = $helper;
        $this->_filterProvider = $filterProvider;
        $this->_blockFactory = $blockFactory;
        $this->_storeManager = $context->getStoreManager();
        $this->_dropDownStyle = $dropDownStyle;

        parent::__construct($context);
    }

    public function getCategoryHelper()
    {
        return $this->_categoryHelper;
    }

    public function getCategoryModel($id)
    {
        $_category = $this->_categoryFactory->create();
        $_category->load($id);

        return $_category;
    }

    public function getHtml($outermostClass = '', $childrenWrapClass = '', $limit = 0)
    {
        return $this->_topMenu->getHtml($outermostClass, $childrenWrapClass, $limit);
    }

    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        return $this->_categoryHelper->getStoreCategories($sorted, $asCollection, $toLoad);
    }

    public function getChildCategories($category)
    {
        if ($this->_categoryFlatConfig->isFlatEnabled() && $category->getUseFlatResource()) {
            $subCategories = (array)$category->getChildrenNodes();
        } else {
            $subCategories = $category->getChildren();
        }

        return $subCategories;
    }

    public function getActiveChildCategories($category)
    {
        $children = [];
        $subCategories = $this->getChildCategories($category);

        foreach ($subCategories as $category) {
            if (!$category->getIsActive()) {
                continue;
            }
            $children[] = $category;
        }

        return $children;
    }

    public function getBlockContent($content = '')
    {
        if (!$this->_filterProvider)
            return $content;
        return $this->_filterProvider->getBlockFilter()->filter(trim($content));
    }

    public function getCustomMenuBlockHtml($type = 'after')
    {
        $html = '';
        $blockIds = $this->_megamenuConfig['custom_links']['staticblock_' . $type];

        if (!$blockIds) return '';

        $blockIds = preg_replace('/\s/', '', $blockIds);
        $ids = explode(',', $blockIds);
        $storeId = $this->_storeManager->getStore()->getId();

        foreach ($ids as $blockId) {
            $block = $this->_blockFactory->create();
            $block->setStoreId($storeId)->load($blockId);

            if (!$block) continue;

            $blockContent = $block->getContent();

            if (!$blockContent) continue;

            $content = $this->_filterProvider->getBlockFilter()->setStoreId($storeId)->filter($blockContent);

            if (substr($content, 0, 4) == '<ul>')
                $content = substr($content, 4);
            if (substr($content, strlen($content) - 5) == '</ul>')
                $content = substr($content, 0, -5);

            $html .= $content;
        }

        return $html;
    }

    public function getSubmenuItemsHtml($children, $level = 1, $maxLevel = 0, $columnWidth = 12, $menuType = 'fullwidth', $columns = null)
    {
        $html = '';

        if(!$maxLevel || ($maxLevel && $maxLevel == 0) || ($maxLevel && $maxLevel > 0 && $maxLevel-1 >= $level)) {
            $columnClass = "";

            if ($level == 1 && $columns && ($menuType == 'fullwidth' || $menuType == 'staticwidth')) {
                $columnClass = "col-sm-".$columnWidth." ";
                $columnClass .= "mega-columns columns".$columns;
            }

            $html = '<ul class="subchildmenu ' . $columnClass . '">';

            foreach ($children as $child) {
                $categoryModel = $this->getCategoryModel($child->getId());

                $dnaMenuHideItem = $categoryModel->getData('dna_menu_hide_item');

                if (!$dnaMenuHideItem) {
                    $subChildren = $this->getActiveChildCategories($child);
                    $countChildren = count($subChildren);

                    $dnaMenuIconImg = $categoryModel->getData('dna_menu_icon_img');
                    $dnaMenuFontIcon = $categoryModel->getData('dna_menu_font_icon');
                    $itemClass = 'level' . $level . ' ';

                    if ($countChildren > 0)
                        $itemClass .= 'parent ';

                    $html .= '<li class="ui-menu-item ' . $itemClass . '">';

                    if ($countChildren > 0)
                        $html .= '<div class="open-children-toggle"></div>';

                    if ($level == 1 && $dnaMenuIconImg) {
                        $html .= '<div class="menu-thumb-img"><a class="menu-thumb-link" href="' . $this->_categoryHelper->getCategoryUrl($child) . '">';
                        $html .= '<img src="' . $this->_helper->getBaseUrl().'catalog/category/' . $dnaMenuIconImg . '" alt="' . $child->getName() . '" title="' . $child->getName() . '" />';
                        $html .= '</a></div>';
                    }

                    $html .= '<a href="' . $this->_categoryHelper->getCategoryUrl($child) . '">';

                    if ($level > 1 && $dnaMenuIconImg) {
                        $html .= '<img src="' . $this->_helper->getBaseUrl().'catalog/category/' . $dnaMenuIconImg . '" alt="' . $child->getName() . '" title="' . $child->getName() . '" />';
                    } elseif ($dnaMenuFontIcon) {
                        $html .= '<em class="menu-thumn-icon ' . $dnaMenuFontIcon . '"></em>';
                    }

                    $html .= '<span>' . $child->getName() . '</span>';
                    $html .= '</a>';

                    if ($countChildren > 0) {
                        $html .= $this->getSubmenuItemsHtml($subChildren, $level + 1, $maxLevel, $columnWidth, $menuType);
                    }

                    $html .= '</li>';
                }
            }
            $html .= '</ul>';
        }

        return $html;
    }

    public function getMegamenuHtml()
    {
        $html = '';
        $categories = $this->getStoreCategories(true, false, true);
        $this->_megamenuConfig = $this->_helper->getConfig('dnafactory_megamenu');
        $maxLevel = $this->_megamenuConfig['general']['max_level'];

        $html .= $this->getCustomMenuBlockHtml('before');

        foreach ($categories as $category) {
            if (!$category->getIsActive()) {
                continue;
            }

            $categoryModel = $this->getCategoryModel($category->getId());
            $dnaMenuHideItem = $categoryModel->getData('dna_menu_hide_item');

            if (!$dnaMenuHideItem) {
                $children = $this->getActiveChildCategories($category);
                $dnaMenuIconImg = $categoryModel->getData('dna_menu_icon_img');
                $dnaMenuFontIcon = $categoryModel->getData('dna_menu_font_icon');
                $dnaMenuCatColumns = $categoryModel->getData('dna_menu_cat_columns');
                $dnaMenuFloatType = $categoryModel->getData('dna_menu_float_type');
                $dnaMenuStaticWidth = $categoryModel->getData('dna_menu_static_width');
                $menuType = $categoryModel->getData('dna_menu_type');
                $dnaMenuTopContent = $categoryModel->getData('dna_menu_block_top_content');
                $dnaMenuLeftContent = $categoryModel->getData('dna_menu_block_left_content');
                $dnaMenuLeftWidth = $categoryModel->getData('dna_menu_block_left_width');
                $dnaMenuRightContent = $categoryModel->getData('dna_menu_block_right_content');
                $dnaMenuRightWidth = $categoryModel->getData('dna_menu_block_right_width');
                $dnaMenuBottomContent = $categoryModel->getData('dna_menu_block_bottom_content');

                if (!$dnaMenuCatColumns) {
                    $dnaMenuCatColumns = 4;
                }

                if (!$menuType) {
                    $menuType = $this->_megamenuConfig['general']['menu_type'];
                }

                // start attribute style
                $customStyle = 'style="';

                if ($menuType == "staticwidth") {
                    $customStyle .= 'width: 500px;';
                }

                if ($menuType == "staticwidth" && $dnaMenuStaticWidth) {
                    $customStyle .= 'width: ' . $dnaMenuStaticWidth . ';';
                }

                if ($this->_dropDownStyle->getStyle($categoryModel) !== false) {
                    $customStyle .= $this->_dropDownStyle->getStyle($categoryModel);
                }

                // end attribute style
                $customStyle .= '"';

                $itemClass = 'level0 ';
                $itemClass .= $menuType . ' ';

                if (!$dnaMenuLeftContent || !$dnaMenuLeftWidth)
                    $dnaMenuLeftWidth = 0;
                if (!$dnaMenuRightContent || !$dnaMenuRightWidth)
                    $dnaMenuRightWidth = 0;
                if ($dnaMenuFloatType)
                    $dnaMenuFloatType = 'fl-' . $dnaMenuFloatType . ' ';

                if (count($children) > 0 || (($menuType == "fullwidth" || $menuType == "staticwidth") && ($dnaMenuTopContent || $dnaMenuLeftContent || $dnaMenuRightContent || $dnaMenuBottomContent))) {
                    $itemClass .= ' parent ';
                }

                $html .= '<li class="ui-menu-item ' . $itemClass . $dnaMenuFloatType . '">';

                if (count($children) > 0) {
                    $html .= '<div class="open-children-toggle"></div>';
                }

                $html .= '<a href="' . $this->_categoryHelper->getCategoryUrl($category) . '" class="level-top">';

                if ($dnaMenuIconImg)
                    $html .= '<img class="menu-thumb-icon" src="' . $this->_helper->getBaseUrl().'catalog/category/' . $dnaMenuIconImg . '" alt="' . $category->getName() . '" title="' . $category->getName() . '" />';
                elseif ($dnaMenuFontIcon)
                    $html .= '<em class="menu-thumb-icon ' . $dnaMenuFontIcon . '"></em>';

                $html .= '<span>' . $category->getName() . '</span>';
                $html .= '</a>';

                if ($menuType != "nodropdown") {
                    if (count($children) > 0 || (($menuType == "fullwidth" || $menuType == "staticwidth") && ($dnaMenuTopContent || $dnaMenuLeftContent || $dnaMenuRightContent || $dnaMenuBottomContent))) {
                        $html .= '<div class="level0 submenu" ' . $customStyle . '>';

                        if (($menuType == "fullwidth" || $menuType == "staticwidth")) {
                            $html .= '<div class="container-submenu">';
                        }

                        if (($menuType == "fullwidth" || $menuType == "staticwidth") && $dnaMenuTopContent) {
                            $html .= '<div class="menu-top-block">' . $this->getBlockContent($dnaMenuTopContent) . '</div>';
                        }

                        if (count($children) > 0 || (($menuType == "fullwidth" || $menuType == "staticwidth") && ($dnaMenuLeftContent || $dnaMenuRightContent))) {
                            $html .= '<div class="row">';

                            if (($menuType == "fullwidth" || $menuType == "staticwidth") && $dnaMenuLeftContent && $dnaMenuLeftWidth > 0) {
                                $html .= '<div class="menu-left-block col-sm-' . $dnaMenuLeftWidth . '">' . $this->getBlockContent($dnaMenuLeftContent) . '</div>';
                            }

                            $html .= $this->getSubmenuItemsHtml($children, 1, $maxLevel, 12 - $dnaMenuLeftWidth - $dnaMenuRightWidth, $menuType, $dnaMenuCatColumns);

                            if (($menuType == "fullwidth" || $menuType == "staticwidth") && $dnaMenuRightContent && $dnaMenuRightWidth > 0) {
                                $html .= '<div class="menu-right-block col-sm-' . $dnaMenuRightWidth . '">' . $this->getBlockContent($dnaMenuRightContent) . '</div>';
                            }

                            $html .= '</div>';
                        }

                        if (($menuType == "fullwidth" || $menuType == "staticwidth") && $dnaMenuBottomContent) {
                            $html .= '<div class="menu-bottom-block">' . $this->getBlockContent($dnaMenuBottomContent) . '</div>';
                        }

                        if (($menuType == "fullwidth" || $menuType == "staticwidth")) {
                            $html .= '</div>';
                        }

                        $html .= '</div>';
                    }
                }
                $html .= '</li>';
            }
        }

        $html .= $this->getCustomMenuBlockHtml('after');

        return $html;
    }
}
