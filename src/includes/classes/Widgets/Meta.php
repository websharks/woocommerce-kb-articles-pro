<?php
/**
 * Meta widget.
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
 * Meta widget.
 *
 * @since 160805.29085 Initial release.
 */
class Meta extends SCoreClasses\SCore\Base\Widget
{
    /**
     * Class constructor.
     *
     * @since 160805.29085 Initial release.
     */
    public function __construct()
    {
        $App  = c::app();
        $args = [
            'slug'        => 'meta',
            'name'        => __('KB: Article Meta', 'woocommerce-kb-articles'),
            'description' => __('Displays single article meta.', 'woocommerce-kb-articles'),
        ];
        $default_options = [
            'show_author'     => true,
            'show_product'    => true,
            'show_categories' => true,
            'show_tags'       => true,
        ];
        parent::__construct($App, $args, $default_options);
    }

    /**
     * Outputs the options form on admin.
     *
     * @since 160805.29085 Initial release.
     *
     * @param SCoreClasses\SCore\WidgetForm $Form    Instance.
     * @param array                         $options Options.
     *
     * @return string Form content markup.
     */
    protected function formContent(SCoreClasses\SCore\WidgetForm $Form, array $options): string
    {
        $markup = ''; // Initialize.

        $markup .= $Form->selectRow([
            'name'    => 'show_author',
            'label'   => __('Show Author?', 'woocommerce-kb-articles'),
            'value'   => $options['show_author'],
            'options' => [
                '0' => __('No', 'woocommerce-kb-articles'),
                '1' => __('Yes', 'woocommerce-kb-articles'),
            ],
        ]);
        $markup .= $Form->selectRow([
            'name'    => 'show_product',
            'label'   => __('Show Product?', 'woocommerce-kb-articles'),
            'value'   => $options['show_product'],
            'options' => [
                '0' => __('No', 'woocommerce-kb-articles'),
                '1' => __('Yes', 'woocommerce-kb-articles'),
            ],
        ]);
        $markup .= $Form->selectRow([
            'name'    => 'show_categories',
            'label'   => __('Show Categories?', 'woocommerce-kb-articles'),
            'value'   => $options['show_categories'],
            'options' => [
                '0' => __('No', 'woocommerce-kb-articles'),
                '1' => __('Yes', 'woocommerce-kb-articles'),
            ],
        ]);
        $markup .= $Form->selectRow([
            'name'    => 'show_tags',
            'label'   => __('Show Tags?', 'woocommerce-kb-articles'),
            'value'   => $options['show_tags'],
            'options' => [
                '0' => __('No', 'woocommerce-kb-articles'),
                '1' => __('Yes', 'woocommerce-kb-articles'),
            ],
        ]);
        return $markup;
    }

    /**
     * Widget content markup.
     *
     * @since 160805.29085 Initial release.
     *
     * @param array $options Options.
     *
     * @return string Widget content markup.
     */
    protected function widgetContent(array $options): string
    {
        if (!is_singular('kb_article')) {
            return ''; // Not applicable.
        }
        $markup = ''; // Initialize.

        $post_id    = (int) get_the_ID();
        $product_id = s::wcProductIdBySlug((string) get_query_var('kb_product'));
        $WC_Product = $product_id ? wc_get_product($product_id) : null;

        $categories = $options['show_categories'] ? get_the_term_list($post_id, 'kb_category', '', __(', ', 'woocommerce-kb-articles')) : [];
        $tags       = $options['show_tags'] ? get_the_term_list($post_id, 'kb_tag', '', __(', ', 'woocommerce-kb-articles')) : [];

        $markup .= '<style type="text/css">';
        $markup .=      '.widget.'.$this->App->Config->©brand['©slug'].'-meta-widget a { ';
        $markup .=          'color: '.get_theme_mod('storefront_accent_color').';';
        $markup .=      '}';
        $markup .= '</style>';

        if ($options['show_author']) {
            $markup .= '<div class="-author">';
            $markup .=      get_avatar(get_the_author_meta('ID'), 128);
            $markup .=      '<div class="-label">'.__('Written By', 'woocommerce-kb-articles').'</div>';
            $markup .=      get_the_author_posts_link();
            $markup .= '</div>';
        }
        if ($options['show_product'] && $product_id && $WC_Product && $WC_Product->exists()) {
            $markup .= '<div class="-product">';
            $markup .=      '<div class="-label">'.__('For Product', 'woocommerce-kb-articles').'</div>';
            $markup .=      '<a href="'.esc_url(get_permalink($product_id)).'">'.$WC_Product->get_title().'</a> <i class="fa fa-shopping-cart"></i>';
            $markup .= '</div>';
        }
        if ($options['show_categories'] && $categories) {
            $markup .=      '<div class="-categories">';
            $markup .=          '<div class="-label">'.__('Posted in', 'woocommerce-kb-articles').'</div>';
            $markup .=          $categories;
            $markup .=      '</div>';
        }
        if ($options['show_tags'] && $tags) {
            $markup .=      '<div class="-tags">';
            $markup .=          '<div class="-label">'.__('Tagged', 'woocommerce-kb-articles').'</div>';
            $markup .=          $tags;
            $markup .=      '</div>';
        }
        return $markup;
    }
}
