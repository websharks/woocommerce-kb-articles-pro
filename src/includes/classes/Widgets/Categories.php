<?php
/**
 * Categories widget.
 *
 * @author @jaswsinc
 * @copyright WP Sharks™
 */
declare (strict_types = 1);
namespace WebSharks\WpSharks\WooCommerceKBArticles\Pro\Classes\Widgets;

use WebSharks\WpSharks\WooCommerceKBArticles\Pro\Classes;
use WebSharks\WpSharks\WooCommerceKBArticles\Pro\Interfaces;
use WebSharks\WpSharks\WooCommerceKBArticles\Pro\Traits;
#
use WebSharks\WpSharks\WooCommerceKBArticles\Pro\Classes\AppFacades as a;
use WebSharks\WpSharks\WooCommerceKBArticles\Pro\Classes\SCoreFacades as s;
use WebSharks\WpSharks\WooCommerceKBArticles\Pro\Classes\CoreFacades as c;
#
use WebSharks\WpSharks\Core\Classes as SCoreClasses;
use WebSharks\WpSharks\Core\Interfaces as SCoreInterfaces;
use WebSharks\WpSharks\Core\Traits as SCoreTraits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * Categories widget.
 *
 * @since 16xxxx Initial release.
 */
class Categories extends SCoreClasses\SCore\Base\Widget
{
    /**
     * Class constructor.
     *
     * @since 16xxxx Initial release.
     */
    public function __construct()
    {
        $App  = c::app();
        $args = [
            'slug'        => 'categories',
            'class'       => 'widget_categories',
            'name'        => __('KB Categories', 'woocommerce-kb-articles'),
            'description' => __('Display a list of KB categories.', 'woocommerce-kb-articles'),
        ];
        $default_options = [
            'depth' => 0,
        ];
        parent::__construct($App, $args, $default_options);
    }

    /**
     * Outputs the options form on admin.
     *
     * @since 16xxxx Initial release.
     *
     * @param SCoreClasses\SCore\WidgetForm $Form    Instance.
     * @param array                         $options Options.
     *
     * @return string Form content markup.
     */
    protected function formContent(SCoreClasses\SCore\WidgetForm $Form, array $options): string
    {
        $markup = $Form->inputRow([
            'type'  => 'number',
            'name'  => 'depth',
            'label' => __('Depth:', 'woocommerce-kb-articles'),
            'value' => $options['depth'],
            'tip'   => __('e.g., Setting this to <code>1</code> will show parent categories only. <code>0</code> shows all categories.', 'woocommerce-kb-articles'),
        ]);
        return $markup;
    }

    /**
     * Widget content markup.
     *
     * @since 16xxxx Initial release.
     *
     * @param array $options Options.
     *
     * @return string Widget content markup.
     */
    protected function widgetContent(array $options): string
    {
        if (is_singular('product')) {
            $show_count = false; // Product-specific.
        } elseif (is_singular('kb_article') || is_post_type_archive('kb_article')) {
            if (get_query_var('kb_product')) {
                $show_count = false;
            } // Do now show in these special cases.
        } // Counts will be off for product-specific terms.

        $category_args['title_li']     = '';
        $category_args['hierarchical'] = true;
        $category_args['depth']        = $options['depth'];
        $category_args['show_count']   = $show_count ?? true;
        $category_args['orderby']      = 'name';
        // Set this to `1` to show top-level categories only.

        $category_args             = s::applyFilters('widget_category_args', $category_args);
        $category_args['taxonomy'] = 'kb_category';
        $category_args['echo']     = false;

        return $markup = '<ul>'.wp_list_categories($category_args).'</ul>';
    }
}
