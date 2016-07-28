<?php
/**
 * Uninstall utils.
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
 * Uninstall utils.
 *
 * @since 16xxxx Initial release.
 */
class Uninstaller extends SCoreClasses\SCore\Base\Core
{
    /**
     * Other uninstall routines.
     *
     * @since 16xxxx Initial release.
     *
     * @param int $site_counter Site counter.
     */
    public function onOtherUninstallRoutines(int $site_counter)
    {
        $this->deletePostTypeCaps($site_counter);
        $this->deletePostTypePosts($site_counter);
    }

    /**
     * Remove all post type caps.
     *
     * @since 16xxxx KB article utils.
     *
     * @param int $site_counter Site counter.
     */
    protected function deletePostTypeCaps(int $site_counter)
    {
        foreach (array_keys(wp_roles()->roles) as $_Role) {
            if (!is_object($_Role = get_role($_Role))) {
                continue; // Not possible.
            }
            foreach (a::postTypeCaps() as $_cap) {
                $_Role->remove_cap($_cap);
            }
        } // unset($_Role, $_cap); // Housekeeping.
    }

    /**
     * Delete post type posts.
     *
     * @since 16xxxx Initial release.
     *
     * @param int $site_counter Site counter.
     */
    protected function deletePostTypePosts(int $site_counter)
    {
        $WpDb = s::wpDb();

        $sql = /* Restriction post IDs. */ '
            SELECT `ID`
                FROM `'.esc_sql($WpDb->posts).'`
            WHERE `post_type` = \'kb_article\'
        ';
        if (!($results = $WpDb->get_results($sql))) {
            return; // Nothing to delete.
        }
        foreach ($results as $_key => $_result) {
            wp_delete_post($_result->ID, true);
        } // unset($_key, $_result); // Housekeeping.
    }
}
