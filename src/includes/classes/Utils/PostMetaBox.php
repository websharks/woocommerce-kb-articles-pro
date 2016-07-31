<?php
/**
 * Post meta box utils.
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
 * Post meta box utils.
 *
 * @since 160731.38548 Initial release.
 */
class PostMetaBox extends SCoreClasses\SCore\Base\Core
{
    /**
     * On admin init.
     *
     * @since 160731.38548 Initial release.
     */
    public function onAdminInit()
    {
        s::addPostMetaBox([
            'include_post_types' => 'kb_article',
            'slug'               => 'article-product-id',
            'title'              => __('Product-Specific KB', 'woocommerce-kb-articles'),
            'template_file'      => 'admin/menu-pages/post-meta-box/article-product-id.php',
            'context'            => 'side', 'priority' => 'high',
        ]);
    }
}
