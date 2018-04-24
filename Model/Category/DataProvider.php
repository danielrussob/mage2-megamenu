<?php

namespace DNAFactory\Megamenu\Model\Category;

class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{
    /**
     * @return array
     */
    protected function getFieldsMap()
    {
        return [
            'general' =>
                [
                    'parent',
                    'path',
                    'is_active',
                    'include_in_menu',
                    'name',
                ],
            'content' =>
                [
                    'image',
                    'description',
                    'landing_page',
                ],
            'display_settings' =>
                [
                    'display_mode',
                    'is_anchor',
                    'available_sort_by',
                    'use_config.available_sort_by',
                    'default_sort_by',
                    'use_config.default_sort_by',
                    'filter_price_range',
                    'use_config.filter_price_range',
                ],
            'search_engine_optimization' =>
                [
                    'url_key',
                    'url_key_create_redirect',
                    'use_default.url_key',
                    'url_key_group',
                    'meta_title',
                    'meta_keywords',
                    'meta_description',
                ],
            'assign_products' =>
                [
                ],
            'design' =>
                [
                    'custom_use_parent_settings',
                    'custom_apply_to_products',
                    'custom_design',
                    'page_layout',
                    'custom_layout_update',
                ],
            'schedule_design_update' =>
                [
                    'custom_design_from',
                    'custom_design_to',
                ],
            'dnafactory-megamenu' =>
                [
                    'dna_menu_hide_item',
                    'dna_menu_type',
                    'dna_menu_static_width',
                    'dna_menu_cat_columns',
                    'dna_menu_float_type',
                    'dna_menu_icon_img',
                    'dna_menu_font_icon',
                    'dna_menu_block_top_content',
                    'dna_menu_block_left_width',
                    'dna_menu_block_left_content',
                    'dna_menu_block_right_width',
                    'dna_menu_block_right_content',
                    'dna_menu_block_bottom_content',
                    'dna_menu_block_border_style',
                    'dna_menu_block_border_width',
                    'dna_menu_block_border_color',
                    'dna_menu_block_box_shadow',
                ],
            'category_view_optimization' =>
                [
                ],
            'category_permissions' =>
                [
                ],
        ];
    }
}
