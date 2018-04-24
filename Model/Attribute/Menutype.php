<?php

namespace DNAFactory\Megamenu\Model\Attribute;

class Menutype extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['value' => '', 'label' => __('Default')],
                ['value' => 'fullwidth', 'label' => __('Full Width')],
                ['value' => 'staticwidth', 'label' => __('Static Width')],
                ['value' => 'classic', 'label' => __('Classic')],
                ['value' => 'nodropdown', 'label' => __('No Dropdown')],
            ];
        }

        return $this->_options;
    }
}
