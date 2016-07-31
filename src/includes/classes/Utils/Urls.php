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
 * @since 160731.38548 Initial release.
 */
class Urls extends SCoreClasses\SCore\Base\Core
{
    /**
     * Permalink options.
     *
     * @since 160731.38548 Initial release.
     *
     * @var array Permalink options.
     */
    protected $permalink_options;

    /**
     * Class constructor.
     *
     * @since 160731.38548 Initial release.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        $this->permalink_options = s::getOption('permalinks');
    }

    /**
     * On `template_redirect` hook.
     *
     * @since 160731.38548 Initial release.
     */
    public function onTemplateRedirect()
    {
        if (is_singular('product') && is_wc_endpoint_url('kb')) {
            wp_redirect($this->index(get_the_ID())).exit();
        }
    }

    /**
     * On `post_type_link` filter.
     *
     * @since 160731.38548 Initial release.
     *
     * @param string|scalar $link    Current link.
     * @param \WP_Post      $WP_Post Post.
     *
     * @return string Filtered post type link.
     */
    public function onPostTypeLink($link, \WP_Post $WP_Post): string
    {
        $link = (string) $link;

        if ($WP_Post->post_type !== 'kb_article') {
            return $link; // Not applicable.
        }
        $product_id = s::getPostMeta($WP_Post->ID, '_product_id');
        $WC_Product = $product_id ? wc_get_product($product_id) : null;

        if ($WC_Product && $WC_Product->exists()) {
            $link = str_replace('%kb_product%', urlencode($WC_Product->post->post_name), $link);
        } else { // `/kb-article/%kb_product%/article-slug` becomes `/kb-articles/article-slug`.
            $link = str_replace('/'.$this->permalink_options['article_base'].'/', '/'.$this->permalink_options['articles_base'].'/', $link);
            $link = str_replace('/%kb_product%', '', $link);
        }
        return $link; // Filtered link.
    }

    /**
     * On `post_type_archive_link` filter.
     *
     * @since 160731.38548 Initial release.
     *
     * @param string|scalar $link      Current link.
     * @param string|scalar $post_type Post type.
     *
     * @return string Filtered post type archive link.
     */
    public function onPostTypeArchiveLink($link, $post_type): string
    {
        $link      = (string) $link;
        $post_type = (string) $post_type;

        if ($post_type !== 'kb_article') {
            return $link; // Not applicable.
        } elseif (($kb_product = (string) get_query_var('kb_product'))) {
            $link = str_replace('%kb_product%', urlencode($kb_product), $link);
        } elseif (is_singular('product') && ($kb_product = get_post()->post_name)) {
            $link = str_replace('%kb_product%', urlencode($kb_product), $link);
        } else { // `/kb/%kb_product%` becomes `/kb`.
            $link = str_replace('/%kb_product%', '', $link);
        }
        return $link; // Filtered link.
    }

    /**
     * On `term_link` filter.
     *
     * @since 160731.38548 Initial release.
     *
     * @param string|scalar $link     Current link.
     * @param \WP_Term      $WP_Term  Term.
     * @param string|scalar $taxonomy Taxonomy.
     *
     * @return string Filtered term link.
     */
    public function onTermLink($link, \WP_Term $WP_Term, $taxonomy): string
    {
        $link     = (string) $link;
        $taxonomy = (string) $taxonomy;

        if ($taxonomy !== 'kb_category' && $taxonomy !== 'kb_tag') {
            return $link; // Not applicable.
        } elseif (($kb_product = (string) get_query_var('kb_product'))) {
            $link = str_replace('%kb_product%', urlencode($kb_product), $link);
        } elseif (is_singular('product') && ($kb_product = get_post()->post_name)) {
            $link = str_replace('%kb_product%', urlencode($kb_product), $link);
        } elseif ($taxonomy === 'kb_category') { // `/kb-category/%kb_product%/category-slug` becomes `/kb-categories/category-slug`.
            $link = str_replace('/'.$this->permalink_options['category_base'].'/', '/'.$this->permalink_options['categories_base'].'/', $link);
            $link = str_replace('/%kb_product%', '', $link);
        } elseif ($taxonomy === 'kb_tag') { // `/kb-tag/%kb_product%/tag-slug` becomes `/kb-tags/tag-slug`.
            $link = str_replace('/'.$this->permalink_options['tag_base'].'/', '/'.$this->permalink_options['tags_base'].'/', $link);
            $link = str_replace('/%kb_product%', '', $link);
        }
        return $link; // Filtered link.
    }

    /**
     * On `author_link` filter.
     *
     * @since 160731.38548 Initial release.
     *
     * @param string|scalar $link     Current link.
     * @param int|scalar    $user_id  Author ID.
     * @param string|scalar $nicename Author nicename.
     *
     * @return string Filtered author link.
     */
    public function onAuthorLink($link, $user_id, $nicename): string
    {
        $link     = (string) $link;
        $user_id  = (int) $user_id;
        $nicename = (string) $nicename;

        if (is_singular('kb_article') || is_post_type_archive('kb_article')) {
            $product_id = s::wcProductIdBySlug((string) get_query_var('kb_product'));
            $link       = $this->author($user_id, $product_id ?: null);
        }
        return $link; // Filtered link.
    }

    /**
     * KB articles index URL.
     *
     * @since 160731.38548 Initial release.
     *
     * @param int|null $product_id Product ID.
     *
     * @return string KB articles index URL.
     */
    public function index(int $product_id = null): string
    {
        if (isset($product_id) && ($WC_Product = wc_get_product($product_id)) && $WC_Product->exists()) {
            return user_trailingslashit(home_url('/'.$this->permalink_options['base'].'/'.urlencode($WC_Product->post->post_name)));
        }
        return user_trailingslashit(home_url('/'.$this->permalink_options['base']));
    }

    /**
     * KB articles by author URL.
     *
     * @since 160731.38548 Initial release.
     *
     * @param int|null $user_id    Author ID.
     * @param int|null $product_id Product ID.
     *
     * @return string KB articles by author URL.
     */
    public function author(int $user_id, int $product_id = null): string
    {
        if (!$user_id || !($WP_User = get_user_by('ID', $user_id)) || !$WP_User->exists()) {
            return ''; // Not possible.
        }
        if (isset($product_id) && ($WC_Product = wc_get_product($product_id)) && $WC_Product->exists()) {
            $url = home_url('/'.$this->permalink_options['author_base'].'/'.urlencode($WC_Product->post->post_name));
        } else {
            $url = home_url('/'.$this->permalink_options['authors_base']);
        }
        return user_trailingslashit(c::mbRTrim($url, '/').'/'.urlencode($WP_User->user_nicename));
    }

    /**
     * Create KB article URL.
     *
     * @since 160731.38548 Initial release.
     *
     * @param int|null $product_id Product ID.
     *
     * @return string Create KB article URL.
     */
    public function create(int $product_id = null): string
    {
        if (isset($product_id)) {
            return admin_url('/post-new.php?post_type=kb_article&_product_id='.$product_id);
        } else {
            return admin_url('/post-new.php?post_type=kb_article');
        }
    }
}
