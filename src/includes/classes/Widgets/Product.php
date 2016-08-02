<?php
/**
 * Product widget.
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
 * Product widget.
 *
 * @since 160801 Product widget.
 */
class Product extends SCoreClasses\SCore\Base\Widget
{
    /**
     * Class constructor.
     *
     * @since 160801 Product widget.
     */
    public function __construct()
    {
        $App  = c::app();
        $args = [
            'slug'        => 'product',
            'class'       => 'woocommerce widget_products',
            'name'        => __('KB: Back to Product Page', 'woocommerce-kb-articles'),
            'description' => __('Link back to current KB product page.', 'woocommerce-kb-articles'),
        ];
        $default_options = [
            // None at this time.
        ];
        parent::__construct($App, $args, $default_options);
    }

    /**
     * Outputs the options form on admin.
     *
     * @since 160801 Product widget.
     *
     * @param SCoreClasses\SCore\WidgetForm $Form    Instance.
     * @param array                         $options Options.
     *
     * @return string Form content markup.
     */
    protected function formContent(SCoreClasses\SCore\WidgetForm $Form, array $options): string
    {
        return $markup = '';
    }

    /**
     * Widget content markup.
     *
     * @since 160801 Product widget.
     *
     * @param array $options Options.
     *
     * @return string Widget content markup.
     */
    protected function widgetContent(array $options): string
    {
        if (!is_singular('kb_article') && !is_post_type_archive('kb_article')) {
            return ''; // Not applicable.
        } elseif (!($product_id = s::wcProductIdBySlug((string) get_query_var('kb_product')))) {
            return ''; // Not possible; unable to acquire ID.
        } elseif (!($WC_Product = wc_get_product($product_id)) || !$WC_Product->exists()) {
            return ''; // Not possible; unable to acquire product.
        }
        $markup = ''; // Initialize markup.

        $markup .= '<ul class="product_list_widget">';
        $markup .=      '<li>';
        $markup .=          '<a href="'.esc_url(get_permalink($WC_Product->get_id())).'" title="'.esc_attr($WC_Product->get_title()).'">';
        $markup .=              $WC_Product->get_image().'<span class="product-title">'.$WC_Product->get_title().'</span>';
        $markup .=          '</a>';
        $markup .=          '<i class="fa fa-shopping-cart"></i> '.$WC_Product->get_price_html();
        $markup .=      '</li>';
        $markup .= '</ul>';

        return $markup;
    }
}
