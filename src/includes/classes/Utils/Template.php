<?php
/**
 * Template utils.
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
 * Template utils.
 *
 * @since 16xxxx Initial release.
 */
class Template extends SCoreClasses\SCore\Base\Core
{
    /**
     * Single template filter.
     *
     * @since 16xxxx Initial release.
     *
     * @param string $template Template path.
     *
     * @return string Filtered template path.
     */
    public function onSingleTemplate($template): string
    {
        if ($this->Wp->template !== 'storefront') {
            return $template; // Not applicable.
        } elseif (!is_singular('kb_article')) {
            return $template; // Not applicable.
        } elseif (mb_strpos(basename($template), 'single-kb_article') === 0) {
            return $template; // Has a custom template already.
        }
        $template        = c::locateTemplate('site/post-type/single.php');
        return $template = $template['dir'].'/'.$template['file'];
    }

    /**
     * Archive template filter.
     *
     * @since 16xxxx Initial release.
     *
     * @param string $template Template path.
     *
     * @return string Filtered template path.
     */
    public function onArchiveTemplate($template): string
    {
        if ($this->Wp->template !== 'storefront') {
            return $template; // Not applicable.
        } elseif (!is_post_type_archive('kb_article')) {
            return $template; // Not applicable.
        } elseif (mb_strpos(basename($template), 'archive-kb_article') === 0) {
            return $template; // Has a custom template already.
        }
        $template        = c::locateTemplate('site/post-type/archive.php');
        return $template = $template['dir'].'/'.$template['file'];
    }

    /**
     * Search template filter.
     *
     * @since 16xxxx Initial release.
     *
     * @param string $template Template path.
     *
     * @return string Filtered template path.
     */
    public function onSearchTemplate($template): string
    {
        if ($this->Wp->template !== 'storefront') {
            return $template; // Not applicable.
        } elseif (!is_post_type_archive('kb_article')) {
            return $template; // Not applicable.
        } elseif (mb_strpos(basename($template), 'search-kb_article') === 0) {
            return $template; // Has a custom template already.
        }
        $template        = c::locateTemplate('site/post-type/archive.php');
        return $template = $template['dir'].'/'.$template['file'];
    }
}
