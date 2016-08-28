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
 * @since 160731.38548 Initial release.
 */
class Installer extends SCoreClasses\SCore\Base\Core
{
    /**
     * Version-specific upgrades.
     *
     * @since 160826 New permalink option keys.
     *
     * @param array $history Install history.
     */
    public function onVsUpgrades(array $history)
    {
        if (version_compare($history['last_version'], '160826', '<')) {
            $this->App->Utils->VsUpgrades->fromLt160826();
        }
    }

    /**
     * Other install routines.
     *
     * @since 160731.38548 Initial release.
     *
     * @param array $history Install history.
     */
    public function onOtherInstallRoutines(array $history)
    {
        $this->addCaps(); // Install caps.
    }

    /**
     * Add capabilities.
     *
     * @since 160731.38548 Initial release.
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
