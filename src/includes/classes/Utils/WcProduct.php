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
        WC()->query->query_vars['kb'] = $this->permalink_options['product_endpoint'];
        add_rewrite_endpoint($this->permalink_options['product_endpoint'], EP_PERMALINK);

        add_filter('woocommerce_endpoint_kb_title', function () {
            return __('Knowledge Base', 'woocommerce-kb-articles');
        });
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
        $tabs['kb'] = [
            'priority' => (int) s::getOption('product_tab_priority'),
            'title'    => __('Knowledge Base', 'woocommerce-kb-articles'),

            'callback' => function () {
                echo '<h2>'; // Title with link to open in new tab.
                echo    '<a href="'.esc_url(wc_get_endpoint_url('kb')).'" target="_blank" class="-stand-alone-link">'.__('Open in New Tab', 'woocommerce-kb-articles').'</a>';
                echo    __('Product Knowledge Base', 'woocommerce-kb-articles');
                echo '</h2>';

                $default_tab_content = s::getOption('product_tab_default_content');
                $tab_content = (string) s::getPostMeta(null, '_tab_content', $default_tab_content);
                echo $tab_content = s::applyFilters('product_tab_content', $tab_content);
            },
        ];
        return $tabs; // Tabs can be filtered again by other plugins/extensions.
    }
}
