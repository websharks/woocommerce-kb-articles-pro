<?php
/**
 * WC breadcrumb utils.
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
 * WC breadcrumb utils.
 *
 * @since 160731.38548 Initial release.
 */
class WcBreadcrumbs extends SCoreClasses\SCore\Base\Core
{
    /**
     * On breadcrumbs.
     *
     * @since 160731.38548 Initial release.
     *
     * @param array $crumbs Current crumbs.
     *
     * @return array Filtered array of crumbs.
     */
    public function onWcGetBreadcrumb(array $crumbs): array
    {
        $¤title = 0; // Title key.
        $¤url   = 1; // URL key.

        $¤next    = count($crumbs);
        $¤current = max(0, $¤next - 1);
        $¤prev    = max(0, $¤current - 1);

        if (is_singular('product') && is_wc_endpoint_url('kb')) {
            // Link the product up.
            $crumbs[$¤current][$¤url] = get_permalink();

            $crumbs[$¤next] = [ // Next crumb as endpoint indicator.
                $¤title => strip_tags(WC()->query->get_endpoint_title('kb')),
                $¤url   => '', // Current endpoint indicator (not linked).
            ];
        } elseif (is_singular('kb_article')) {
            $crumbs = array_slice($crumbs, 0, 1);
            $parts  = a::singleTitleParts();

            for ($_i = 0, $_total_parts = count($parts); $_i < $_total_parts; ++$_i) {
                if ($_i + 1 === $_total_parts || !$parts[$_i]['url']) {
                    $crumbs[] = [$¤title => strip_tags($parts[$_i]['title']), $¤url => ''];
                } else {
                    $crumbs[] = [$¤title => strip_tags($parts[$_i]['title']), $¤url => $parts[$_i]['url']];
                }
            } // unset($_i, $_total_parts);
        } elseif (is_post_type_archive('kb_article')) {
            $crumbs = array_slice($crumbs, 0, 1);
            $parts  = a::archiveTitleParts();

            for ($_i = 0, $_total_parts = count($parts); $_i < $_total_parts; ++$_i) {
                if ($_i + 1 === $_total_parts || !$parts[$_i]['url']) {
                    $crumbs[] = [$¤title => strip_tags($parts[$_i]['title']), $¤url => ''];
                } else {
                    $crumbs[] = [$¤title => strip_tags($parts[$_i]['title']), $¤url => $parts[$_i]['url']];
                }
            } // unset($_i, $_total_parts); // Housekeeping.
        }
        return $crumbs;
    }
}
