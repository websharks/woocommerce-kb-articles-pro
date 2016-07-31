<?php
/**
 * Storefront utils.
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
 * Storefront utils.
 *
 * @since 160731.38548 Initial release.
 */
class Storefront extends SCoreClasses\SCore\Base\Core
{
    /**
     * On `wp` hook.
     *
     * @since 160731.38548 Initial release.
     *
     * @param \WP $WP Instance.
     */
    public function onWp(\WP $WP)
    {
        if ($this->Wp->is_admin) {
            return; // Not applicable.
        }
        if (is_singular('kb_article')) {
            remove_action('storefront_single_post_after', 'storefront_post_nav');
        }
        if (is_search() && is_post_type_archive('kb_article')) {
            remove_action('storefront_loop_post', 'storefront_post_meta', 20);
            remove_action('storefront_loop_post', 'storefront_post_content', 30);
            add_action('storefront_loop_post', 'the_excerpt', 30);
        }
    }

    /**
     * Scripts/styles.
     *
     * @since 160731.38548 Initial release.
     */
    public function onWpEnqueueScripts()
    {
        if (!a::stylesScriptsMayApply()) {
            return; // Not applicable.
        }
        wp_enqueue_style($this->App->Config->©brand['©slug'].'-storefront', c::appUrl('/client-s/css/site/storefront.min.css'), [], $this->App::VERSION);
    }
}
