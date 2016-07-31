<?php
/**
 * Post type utils.
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
 * Post type utils.
 *
 * @since 16xxxx Initial release.
 */
class PostTypeCols extends SCoreClasses\SCore\Base\Core
{
    /**
     * Custom columns.
     *
     * @since 16xxxx Initial release.
     *
     * @param array $columns Columns.
     *
     * @return array Filtered columns.
     */
    public function onManagePostsColumns(array $columns): array
    {
        return array_slice($columns, 0, 2, true) + ['kb_product' => __('Product', 'woocommerce-kb-articles')] + array_slice($columns, 2, null, true);
    }

    /**
     * Sortable columns.
     *
     * @since 16xxxx Initial release.
     *
     * @param array $columns Columns.
     *
     * @return array Filtered columns.
     */
    public function onManageEditSortableColumns(array $columns): array
    {
        return array_merge($columns, ['kb_product' => 'kb_product']);
    }

    /**
     * Sortable columns.
     *
     * @since 16xxxx Initial release.
     *
     * @param string|scalar $column  Column name.
     * @param int|scalar    $post_id KB article ID.
     */
    public function onManagePostsCustomColumn($column, $post_id)
    {
        $column  = (string) $column;
        $post_id = (int) $post_id;

        if ($column !== 'kb_product' || !$post_id) {
            return; // Not applicable.
        }
        $product_id = s::getPostMeta($post_id, '_product_id');
        $WC_Product = $product_id ? wc_get_product($product_id) : null;

        if ($WC_Product && $WC_Product->exists()) {
            echo '<a href="'.esc_url(add_query_arg('kb_product', $product_id)).'">'.$WC_Product->get_title().'</a>';
        } else {
            echo '—'; // Not associated w/ a product in this case.
        }
    }
}
