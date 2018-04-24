<?php

namespace DNAFactory\Megamenu\Block\Megamenu\Dropdown;

use \Magento\Framework\View\Element\Template;

class Style extends Template
{
    protected $_category;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context
    ) {
        parent::__construct($context);
    }

    public function getStyle($category)
    {
        $this->_category = $category;
        $style = '';

        if ($borderStyle = $this->getBorderStyle()) {
            $style .= 'border-style: ' . $borderStyle . ';';
        }

        if ($borderWidth = $this->getBorderWidth()) {
            $style .= 'border-width: '. $borderWidth . ';';
        }

        if ($borderColor = $this->getBorderColor()) {
            $style .= 'border-color: '. $borderColor . ';';
        }

        if ($boxShadow = $this->getBoxShadow()) {
            $style .= 'box-shadow: '. $boxShadow . ';';
        }

        if ($style === '') {
            return false;
        } else {
            return $style;
        }
    }

    public function getBorderStyle()
    {
        return $this->_category->getData('dna_menu_block_border_style');
    }

    public function getBorderWidth()
    {
        return $this->_category->getData('dna_menu_block_border_width');
    }

    public function getBorderColor()
    {
        return $this->_category->getData('dna_menu_block_border_color');
    }

    public function getBoxShadow()
    {
        return $this->_category->getData('dna_menu_block_border_box_shadow');
    }

}
