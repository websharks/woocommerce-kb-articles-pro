<?php
/**
 * Search utils.
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
 * Search utils.
 *
 * @since 160731.38548 Initial release.
 */
class Search extends SCoreClasses\SCore\Base\Core
{
    /**
     * Highlight search terms.
     *
     * @since 160731.38548 Initial release.
     *
     * @param string $string Input string.
     *
     * @return string String w/ highlighted search terms.
     */
    public function highlightTerms(string $string): string
    {
        static $search_terms, $regex;

        if (!isset($search_terms, $search_terms_regex)) {
            if (is_search()) {
                $search_terms       = (array) get_query_var('search_terms');
                $search_terms_regex = '/('.implode('|', c::escRegex($search_terms)).')/ui';
            } else {
                $search_terms = $search_regex = '';
            }
        } // This caches a repeat task to help optimize this routine.

        if (!$string || !$search_terms || !$search_terms_regex) {
            return $string; // Nothing to do.
        }
        return $string = preg_replace_callback($search_terms_regex, function ($m) {
            return '<i class="-hst">'.esc_html($m[0]).'</i>';
        }, $string);
    }
}
