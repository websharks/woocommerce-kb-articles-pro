<?php
/**
 * WC product utils.
 *
 * @author @jaswsinc
 * @copyright WP Sharks™
 */
declare (strict_types = 1);
namespace WebSharks\WpSharks\WooCommerceKBArticles\Pro\Classes\Utils;

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
 * WC product utils.
 *
 * @since 160731.38548 Initial release.
 */
class WcProduct extends SCoreClasses\SCore\Base\Core
{
    /**
     * Permalink options.
     *
     * @since 160731.38548 Initial release.
     *
     * @var array Permalink options.
     */
    protected $permalink_options;

    /**
     * Class constructor.
     *
     * @since 160731.38548 Initial release.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        $this->permalink_options = s::getOption('permalinks');
    }

    /**
     * On `init` hook.
     *
     * @since 160731.38548 Initial release.
     */
    public function onInit()
    {
        // This runs after `WC_Query:add_endpoints()` on purpose.
        // WooCommerce adds endpoints using `EP_PAGE`, but these use `EP_PERMALINK`.

        // Must use dashes to avoid conflicting w/ query vars in KB articles themselves.
        // Title filters are not necessary because each of these are redirects.

        $WC_Query                           = WC()->query;
        $WC_Query->query_vars['kb']         = $this->permalink_options['product_base_endpoint'];
        $WC_Query->query_vars['kb-article'] = $this->permalink_options['product_article_endpoint'];

        add_rewrite_endpoint($WC_Query->query_vars['kb'], EP_PERMALINK);
        add_rewrite_endpoint($WC_Query->query_vars['kb-article'], EP_PERMALINK);
    }

    /**
     * On admin init.
     *
     * @since 160731.38548 Initial release.
     */
    public function onAdminInit()
    {
        s::addPostMetaBox([
            'include_post_types' => 'product',
            'slug'               => 'product-tab-content',
            'title'              => __('Knowledge Base', 'woocommerce-kb-articles'),
            'template_file'      => 'admin/menu-pages/post-meta-box/product-tab-content.php',
        ]);
    }

    /**
     * On product tabs (front-end).
     *
     * @since 160731.38548 Initial release.
     *
     * @param array $tabs Product tabs.
     *
     * @return array Filtered product tabs.
     */
    public function onWcProductTabs(array $tabs): array
    {
        if (s::postMetaExists(null, '_tab_content')) {
            $tab_content = (string) s::getPostMeta(null, '_tab_content');
        } else {
            $tab_content = s::getOption('product_tab_default_content');
        } // Only use default value if no meta values exist yet.

        $tab_content = s::applyFilters('product_tab_content', $tab_content);

        if (!$tab_content || !is_string($tab_content)) {
            return $tabs; // If empty, do not show.
        }
        $tabs['kb'] = [
            'priority' => (int) s::getOption('product_tab_priority'),
            'title'    => __('Knowledge Base', 'woocommerce-kb-articles'),

            'callback' => function () use (&$tab_content) {
                echo '<h2>'; // Title with link to open in new tab.
                echo    '<a href="'.esc_url(wc_get_endpoint_url('kb')).'" target="_blank" class="-stand-alone-link">'.__('Open in New Tab', 'woocommerce-kb-articles').'</a>';
                echo    __('Product Knowledge Base', 'woocommerce-kb-articles');
                echo '</h2>';

                echo $tab_content;
            },
        ];
        return $tabs; // Tabs can be filtered again by other plugins/extensions.
    }
}
