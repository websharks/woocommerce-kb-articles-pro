<?php
/**
 * Search widget.
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
 * Search widget.
 *
 * @since 160731.38548 Initial release.
 */
class Search extends SCoreClasses\SCore\Base\Widget
{
    /**
     * Class constructor.
     *
     * @since 160731.38548 Initial release.
     */
    public function __construct()
    {
        $App  = c::app();
        $args = [
            'slug'        => 'search',
            'class'       => 'widget_search',
            'name'        => __('KB: Article Search', 'woocommerce-kb-articles'),
            'description' => __('Search box for KB articles.', 'woocommerce-kb-articles'),
        ];
        $default_options = [
            // No default options yet.
        ];
        parent::__construct($App, $args, $default_options);
    }

    /**
     * Outputs the options form on admin.
     *
     * @since 160731.38548 Initial release.
     *
     * @param SCoreClasses\SCore\WidgetForm $Form    Instance.
     * @param array                         $options Options.
     *
     * @return string Form content markup.
     */
    protected function formContent(SCoreClasses\SCore\WidgetForm $Form, array $options): string
    {
        return $markup = ''; // Nothing here for now.
    }

    /**
     * Widget content markup.
     *
     * @since 160731.38548 Initial release.
     *
     * @param array $options Options.
     *
     * @return string Widget content markup.
     */
    protected function widgetContent(array $options): string
    {
        $markup = '<form role="search" method="get" class="search-form" action="'.esc_url(get_post_type_archive_link('kb_article')).'">';
        $markup .=   '<label>';
        $markup .=       '<span class="screen-reader-text">'.__('Search KB Articles:', 'woocommerce-kb-articles').'</span>';
        $markup .=       '<input type="search" name="s" class="search-field" value="'.esc_attr(get_search_query()).'" placeholder="'._('Search KB Articles &hellip;').'" />';
        $markup .=   '</label>';
        $markup .=   '<input type="submit" class="search-submit" value="'.__('Search', 'woocommerce-kb-articles').'" />';
        $markup .= '</form>';

        return $markup;
    }
}
