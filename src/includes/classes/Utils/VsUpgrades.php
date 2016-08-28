<?php
/**
 * VS upgrades.
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
 * VS upgrades.
 *
 * @since 160826 VS upgrades.
 */
class VsUpgrades extends SCoreClasses\SCore\Base\Core
{
    /**
     * Upgrading from < v160826.
     *
     * @since 160826 VS upgrade handler.
     */
    public function fromLt160826()
    {
        $permalinks         = s::getOption('permalinks');
        $default_permalinks = s::getDefaultOption('permalinks');

        if (empty($permalinks['product_base_endpoint'])) {
            $permalinks['product_base_endpoint'] = $default_permalinks['product_base_endpoint'];
        }
        if (empty($permalinks['product_article_endpoint'])) {
            $permalinks['product_article_endpoint'] = $default_permalinks['product_article_endpoint'];
        }
        s::updateOptions(compact('permalinks'));
    }
}
