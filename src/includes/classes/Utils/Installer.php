<?php
/**
 * Install utils.
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
 * Install utils.
 *
 * @since 16xxxx Initial release.
 */
class Installer extends SCoreClasses\SCore\Base\Core
{
    /**
     * Other install routines.
     *
     * @since 16xxxx Initial release.
     */
    public function onOtherInstallRoutines()
    {
        $this->addCaps();
    }

    /**
     * Add capabilities.
     *
     * @since 16xxxx Initial release.
     */
    protected function addCaps()
    {
        $caps = a::postTypeCaps();

        foreach (['administrator', 'editor', 'shop_manager'] as $_role) {
            if (!($_WP_Role = get_role($_role))) {
                continue; // Not possible.
            }
            foreach ($caps as $_cap) {
                $_WP_Role->add_cap($_cap);
            } // unset($_cap);
        } // unset($_role, $_WP_Role);

        if ($this->Wp->is_woocommerce_product_vendors_active) {
            $vendor_caps = a::postTypeVendorCaps();

            foreach (['wc_product_vendors_admin_vendor', 'wc_product_vendors_manager_vendor'] as $_role) {
                if (!($_WP_Role = get_role($_role))) {
                    continue; // Not possible.
                }
                foreach ($vendor_caps as $_cap) {
                    $_WP_Role->add_cap($_cap);
                } // unset($_cap);
            } // unset($_role, $_WP_Role);
        }
    }
}
