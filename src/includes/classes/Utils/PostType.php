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
class PostType extends SCoreClasses\SCore\Base\Core
{
    /**
     * All post type caps.
     *
     * @since 16xxxx Initial release.
     *
     * @var array All caps.
     */
    public $caps;

    /**
     * Class constructor.
     *
     * @since 16xxxx Initial release.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        $this->caps = [
            'create_kb_articles',

            'edit_kb_articles',
            'edit_others_kb_articles',
            'edit_published_kb_articles',
            'edit_private_kb_articles',

            'publish_kb_articles',

            'delete_kb_articles',
            'delete_private_kb_articles',
            'delete_published_kb_articles',
            'delete_others_kb_articles',

            'read_private_kb_articles',
        ];
    }

    /**
     * On WP init hook.
     *
     * @since 16xxxx KB article utils.
     */
    public function onInit()
    {
        # Post type.

        register_post_type(
            'kb_article',
            [
                'public' => true,

                'supports' => [
                    'title',

                    'editor',
                    'excerpt',
                    'revisions',
                    'wpcom-markdown',

                    'author',
                    'thumbnail',
                    'custom-fields',

                    'comments',
                    'trackbacks',
                ],
                'rewrite' => false, // See below.

                'menu_position' => 6,
                'menu_icon'     => 'dashicons-book',
                'description'   => __('Knowledge base articles.', 'woocommerce-kb-articles'),

                'labels' => [ // See: <http://jas.xyz/244m2Sd>
                    'name'                  => __('KB Articles', 'woocommerce-kb-articles'),
                    'singular_name'         => __('KB Article', 'woocommerce-kb-articles'),
                    'add_new'               => __('Add KB Article', 'woocommerce-kb-articles'),
                    'add_new_item'          => __('Add New KB Article', 'woocommerce-kb-articles'),
                    'edit_item'             => __('Edit KB Article', 'woocommerce-kb-articles'),
                    'new_item'              => __('New KB Article', 'woocommerce-kb-articles'),
                    'view_item'             => __('View KB Article', 'woocommerce-kb-articles'),
                    'search_items'          => __('Search KB Articles', 'woocommerce-kb-articles'),
                    'not_found'             => __('No KB Articles found', 'woocommerce-kb-articles'),
                    'not_found_in_trash'    => __('No KB Articles found in Trash', 'woocommerce-kb-articles'),
                    'parent_item_colon'     => __('Parent KB Article:', 'woocommerce-kb-articles'),
                    'archives'              => __('KB Article Archives', 'woocommerce-kb-articles'),
                    'insert_into_item'      => __('Insert into KB Article', 'woocommerce-kb-articles'),
                    'uploaded_to_this_item' => __('Upload to this KB Article', 'woocommerce-kb-articles'),
                    'featured_image'        => __('Set Featured Image', 'woocommerce-kb-articles'),
                    'remove_featured_image' => __('Remove Featured Image', 'woocommerce-kb-articles'),
                    'use_featured_image'    => __('Use as Featured Image', 'woocommerce-kb-articles'),
                    'filter_items_list'     => __('Filter KB Articles List', 'woocommerce-kb-articles'),
                    'items_list_navigation' => __('KB Articles List Navigation', 'woocommerce-kb-articles'),
                    'items_list'            => __('KB Articles List', 'woocommerce-kb-articles'),
                    'name_admin_bar'        => __('KB Article', 'woocommerce-kb-articles'),
                    'menu_name'             => __('KB Articles', 'woocommerce-kb-articles'),
                    'all_items'             => __('All KB Articles', 'woocommerce-kb-articles'),
                ],

                'map_meta_cap'    => true,
                'capability_type' => [
                    'kb_article',
                    'kb_articles',
                ],
            ]
        );
        # Post type rewrites.

        add_rewrite_tag('%kb_product%', '([^/]+)', 'kb_product=');
        add_rewrite_tag('%kb_article%', '([^/]+)', 'post_type=kb_article&name=');

        add_permastruct('kb_article', 'kb/articles/%kb_product%/%kb_article%', [
            'with_front' => false,
            'ep_mask'    => EP_PERMALINK,
        ]);
        add_filter('post_type_link', function ($link, \WP_Post $WP_Post) {
            if ($WP_Post->post_type !== 'kb_article') {
                return $link; // Not applicable.
            }
            $product_id = s::getPostMeta($WP_Post->ID, '_product_id');
            $WC_Product = $product_id ? wc_get_product($product_id) : null;

            if ($WC_Product && $WC_Product->exists() && $WC_Product->post->post_name) {
                return $link = str_replace('%kb_product%', $WC_Product->post->post_name, $link);
            } else {
                return $link = str_replace('%kb_product%', 'product', $link);
            }
        }, 10, 2);

        # Post type category.

        register_taxonomy(
            'kb_cat',
            'kb_article',
            [
                'public'            => true,
                'show_in_nav_menus' => false,
                'hierarchical'      => true,

                'rewrite' => false, // See below.

                'description' => __('KB categories.', 'woocommerce-kb-articles'),

                'labels' => [ // See: <http://jas.xyz/244m1Oc>
                    'name'                       => __('KB Categories', 'woocommerce-kb-articles'),
                    'singular_name'              => __('KB Category', 'woocommerce-kb-articles'),
                    'search_items'               => __('Search KB Categories', 'woocommerce-kb-articles'),
                    'popular_items'              => __('Popular KB Categories', 'woocommerce-kb-articles'),
                    'all_items'                  => __('All KB Categories', 'woocommerce-kb-articles'),
                    'parent_item'                => __('Parent KB Category', 'woocommerce-kb-articles'),
                    'parent_item_colon'          => __('Parent KB Category:', 'woocommerce-kb-articles'),
                    'edit_item'                  => __('Edit KB Category', 'woocommerce-kb-articles'),
                    'view_item'                  => __('View KB Category', 'woocommerce-kb-articles'),
                    'update_item'                => __('Update KB Category', 'woocommerce-kb-articles'),
                    'add_new_item'               => __('Add New KB Category', 'woocommerce-kb-articles'),
                    'new_item_name'              => __('New KB Category Name', 'woocommerce-kb-articles'),
                    'separate_items_with_commas' => __('Separate KB Categories w/ Commas', 'woocommerce-kb-articles'),
                    'add_or_remove_items'        => __('Add or Remove KB Categories', 'woocommerce-kb-articles'),
                    'choose_from_most_used'      => __('Choose From the Most Used KB Categories', 'woocommerce-kb-articles'),
                    'not_found'                  => __('No KB Categories Found', 'woocommerce-kb-articles'),
                    'no_terms'                   => __('No KB Categories', 'woocommerce-kb-articles'),
                    'items_list_navigation'      => __('KB Categories List Navigation', 'woocommerce-kb-articles'),
                    'items_list'                 => __('KB Categories List', 'woocommerce-kb-articles'),
                    'name_admin_bar'             => __('KB Category', 'woocommerce-kb-articles'),
                    'menu_name'                  => __('KB Categories', 'woocommerce-kb-articles'),
                    'archives'                   => __('All KB Categories', 'woocommerce-kb-articles'),
                ],

                'capabilities' => [
                    'assign_terms' => 'edit_kb_articles',
                    'edit_terms'   => 'edit_kb_articles',
                    'manage_terms' => 'edit_others_kb_articles',
                    'delete_terms' => 'delete_others_kb_articles',
                ],
            ]
        );

        # Post type tag.

        register_taxonomy(
            'kb_tag',
            'kb_article',
            [
                'public'            => true,
                'show_in_nav_menus' => false,
                'hierarchical'      => false,

                'rewrite' => false, // See below.

                'description' => __('KB tags.', 'woocommerce-kb-articles'),

                'labels' => [ // See: <http://jas.xyz/244m1Oc>
                    'name'                       => __('KB Tags', 'woocommerce-kb-articles'),
                    'singular_name'              => __('KB Tag', 'woocommerce-kb-articles'),
                    'search_items'               => __('Search KB Tags', 'woocommerce-kb-articles'),
                    'popular_items'              => __('Popular KB Tags', 'woocommerce-kb-articles'),
                    'all_items'                  => __('All KB Tags', 'woocommerce-kb-articles'),
                    'parent_item'                => __('Parent KB Tag', 'woocommerce-kb-articles'),
                    'parent_item_colon'          => __('Parent KB Tag:', 'woocommerce-kb-articles'),
                    'edit_item'                  => __('Edit KB Tag', 'woocommerce-kb-articles'),
                    'view_item'                  => __('View KB Tag', 'woocommerce-kb-articles'),
                    'update_item'                => __('Update KB Tag', 'woocommerce-kb-articles'),
                    'add_new_item'               => __('Add New KB Tag', 'woocommerce-kb-articles'),
                    'new_item_name'              => __('New KB Tag Name', 'woocommerce-kb-articles'),
                    'separate_items_with_commas' => __('Separate KB Tags w/ Commas', 'woocommerce-kb-articles'),
                    'add_or_remove_items'        => __('Add or Remove KB Tags', 'woocommerce-kb-articles'),
                    'choose_from_most_used'      => __('Choose From the Most Used KB Tags', 'woocommerce-kb-articles'),
                    'not_found'                  => __('No KB Tags Found', 'woocommerce-kb-articles'),
                    'no_terms'                   => __('No KB Tags', 'woocommerce-kb-articles'),
                    'items_list_navigation'      => __('KB Tags List Navigation', 'woocommerce-kb-articles'),
                    'items_list'                 => __('KB Tags List', 'woocommerce-kb-articles'),
                    'name_admin_bar'             => __('KB Tag', 'woocommerce-kb-articles'),
                    'menu_name'                  => __('KB Tags', 'woocommerce-kb-articles'),
                    'archives'                   => __('All KB Tags', 'woocommerce-kb-articles'),
                ],

                'capabilities' => [
                    'assign_terms' => 'edit_kb_articles',
                    'edit_terms'   => 'edit_kb_articles',
                    'manage_terms' => 'edit_others_kb_articles',
                    'delete_terms' => 'delete_others_kb_articles',
                ],
            ]
        );
        # Post type category/tag rewrites.

        add_rewrite_tag('%kb_cat_product%', '([^/]+)', 'kb_product=');
        add_rewrite_tag('%kb_tag_product%', '([^/]+)', 'kb_product=');

        add_rewrite_tag('%kb_cat%', '(.+?)', 'taxonomy=kb_cat&term=');
        add_rewrite_tag('%kb_tag%', '([^/]+)', 'taxonomy=kb_tag&term=');

        add_permastruct('kb_cat', 'kb/cats/%kb_cat_product%/%kb_cat%', [
            'with_front' => false,
            'ep_mask'    => EP_NONE,
        ]);
        add_permastruct('kb_tag', 'kb/tags/%kb_tag_product%/%kb_tag%', [
            'with_front' => false,
            'ep_mask'    => EP_NONE,
        ]);
        add_filter('term_link', function ($link, \WP_Term $WP_Term, $taxonomy) {
            if ($taxonomy !== 'kb_cat' && $taxonomy !== 'kb_tag') {
                return $link; // Not applicable.
            }
            if (($product_slug = (string) get_query_var('kb_product'))) {
                return $link = str_replace(['%kb_cat_product%', '%kb_tag_product%'], $product_slug, $link);
            } else {
                return $link = str_replace(['%kb_cat_product%', '%kb_tag_product%'], 'product', $link);
            }
        }, 10, 3);
    }

    /**
     * Create KB article URL.
     *
     * @since 16xxxx Initial release.
     *
     * @return string Create KB article URL.
     */
    public function createUrl(): string
    {
        return admin_url('/post-new.php?post_type=kb_article');
    }
}
