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
class PostType extends SCoreClasses\SCore\Base\Core
{
    /**
     * All caps.
     *
     * @since 160731.38548
     *
     * @var array All caps.
     */
    public $caps;

    /**
     * All vendor caps.
     *
     * @since 160731.38548
     *
     * @var array All vendor caps.
     */
    public $vendor_caps;

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
        $this->vendor_caps = $this->caps;
        $this->caps        = s::applyFilters('caps', $this->caps);
        $this->vendor_caps = s::applyFilters('vendor_caps', $this->vendor_caps);

        $this->permalink_options = s::getOption('permalinks');
    }

    /**
     * On WP init hook.
     *
     * @since 160731.38548 KB article utils.
     */
    public function onInit()
    {
        # Post type.

        register_post_type(
            'kb_article',
            s::applyFilters('post_type_args', [
                'public' => true,

                'supports' => [
                    'title',

                    'editor',
                    'excerpt',
                    'revisions',
                    'publicize',
                    'wpcom-markdown',

                    'author',
                    'thumbnail',
                    'custom-fields',

                    'comments',
                    'trackbacks',
                ],
                'rewrite' => false, // See below.

                'menu_icon'     => 'dashicons-book',
                'menu_position' => null, // See below.

                'description' => __('Knowledge Base Articles', 'woocommerce-kb-articles'),

                'labels' => [ // See: <http://jas.xyz/244m2Sd>
                    'name'          => __('KB Articles', 'woocommerce-kb-articles'),
                    'singular_name' => __('KB Article', 'woocommerce-kb-articles'),

                    'name_admin_bar' => __('KB Article', 'woocommerce-kb-articles'),
                    'menu_name'      => __('KB Articles', 'woocommerce-kb-articles'),

                    'all_items'    => __('All Articles', 'woocommerce-kb-articles'),
                    'add_new'      => __('Add Article', 'woocommerce-kb-articles'),
                    'add_new_item' => __('Add New Article', 'woocommerce-kb-articles'),
                    'new_item'     => __('New Article', 'woocommerce-kb-articles'),
                    'edit_item'    => __('Edit Article', 'woocommerce-kb-articles'),
                    'view_item'    => __('View Article', 'woocommerce-kb-articles'),

                    'search_items'       => __('Search Knowledge Base', 'woocommerce-kb-articles'),
                    'not_found'          => __('No Articles Found', 'woocommerce-kb-articles'),
                    'not_found_in_trash' => __('No Articles Found in Trash', 'woocommerce-kb-articles'),

                    'insert_into_item'      => __('Insert Into Article', 'woocommerce-kb-articles'),
                    'uploaded_to_this_item' => __('Upload to this Article', 'woocommerce-kb-articles'),

                    'featured_image'        => __('Set Featured Image', 'woocommerce-kb-articles'),
                    'remove_featured_image' => __('Remove Featured Image', 'woocommerce-kb-articles'),
                    'use_featured_image'    => __('Use as Featured Image', 'woocommerce-kb-articles'),

                    'items_list'            => __('Articles List', 'woocommerce-kb-articles'),
                    'items_list_navigation' => __('Articles List Navigation', 'woocommerce-kb-articles'),

                    'archives'          => __('Knowledge Base Archives', 'woocommerce-kb-articles'),
                    'filter_items_list' => __('Filter Articles List', 'woocommerce-kb-articles'),
                    'parent_item_colon' => __('Parent Article:', 'woocommerce-kb-articles'),
                ],

                'map_meta_cap'    => true,
                'capability_type' => [
                    'kb_article',
                    'kb_articles',
                ],
            ])
        );
        add_filter('custom_menu_order', '__return_true');
        add_filter('menu_order', function (array $menu_items) {
            $posts_item = 'edit.php'; // i.e., Blog posts.
            $posts_item_key = array_search($posts_item, $menu_items, true);

            $products_item = 'edit.php?post_type=product';
            $products_item_key = array_search($products_item, $menu_items, true);

            $after_item = $posts_item_key === false ? $products_item : $posts_item;
            $after_item_key = $posts_item_key === false ? $products_item_key : $posts_item_key;

            $kb_articles_item = 'edit.php?post_type=kb_article';
            $kb_articles_key = array_search($kb_articles_item, $menu_items, true);

            if ($after_item_key !== false && $kb_articles_key !== false) {
                $new_menu_items = []; // Initialize new menu items.

                foreach ($menu_items as $_key => $_item) {
                    if ($_item !== $kb_articles_item) {
                        $new_menu_items[] = $_item;
                    }
                    if ($_item === $after_item) {
                        $new_menu_items[] = $kb_articles_item;
                    }
                } // unset($_key, $_item); // Housekeeping.

                $menu_items = $new_menu_items;
            }
            return $menu_items;
        }, 1000);

        # Post type category.

        register_taxonomy(
            'kb_category',
            'kb_article',
            s::applyFilters('category_taxonomy_args', [
                'public'            => true,
                'show_in_nav_menus' => false,
                'show_admin_column' => true,
                'hierarchical'      => true,

                'rewrite' => false, // See below.

                'description' => __('Knowledge Base Categories', 'woocommerce-kb-articles'),

                'labels' => [ // See: <http://jas.xyz/244m1Oc>
                    'name'          => __('KB Categories', 'woocommerce-kb-articles'),
                    'singular_name' => __('KB Category', 'woocommerce-kb-articles'),

                    'name_admin_bar' => __('KB Category', 'woocommerce-kb-articles'),
                    'menu_name'      => __('Categories', 'woocommerce-kb-articles'),

                    'all_items'           => __('All Categories', 'woocommerce-kb-articles'),
                    'add_new_item'        => __('Add New Category', 'woocommerce-kb-articles'),
                    'new_item_name'       => __('New Category Name', 'woocommerce-kb-articles'),
                    'add_or_remove_items' => __('Add or Remove Categories', 'woocommerce-kb-articles'),
                    'view_item'           => __('View Category', 'woocommerce-kb-articles'),
                    'edit_item'           => __('Edit Category', 'woocommerce-kb-articles'),
                    'update_item'         => __('Update Category', 'woocommerce-kb-articles'),

                    'search_items' => __('Search Categories', 'woocommerce-kb-articles'),
                    'not_found'    => __('No Categories Found', 'woocommerce-kb-articles'),
                    'no_terms'     => __('No Categories', 'woocommerce-kb-articles'),

                    'choose_from_most_used'      => __('Choose From the Most Used Categories', 'woocommerce-kb-articles'),
                    'separate_items_with_commas' => __('Separate Categories w/ Commas', 'woocommerce-kb-articles'),

                    'items_list'            => __('Categories List', 'woocommerce-kb-articles'),
                    'items_list_navigation' => __('Categories List Navigation', 'woocommerce-kb-articles'),

                    'archives'          => __('All Categories', 'woocommerce-kb-articles'),
                    'popular_items'     => __('Popular Categories', 'woocommerce-kb-articles'),
                    'parent_item'       => __('Parent Category', 'woocommerce-kb-articles'),
                    'parent_item_colon' => __('Parent Category:', 'woocommerce-kb-articles'),
                ],

                'capabilities' => [
                    'assign_terms' => 'edit_kb_articles',
                    'edit_terms'   => 'edit_kb_articles',
                    'manage_terms' => 'edit_others_kb_articles',
                    'delete_terms' => 'delete_others_kb_articles',
                ],
            ])
        );
        # Post type tag.

        register_taxonomy(
            'kb_tag',
            'kb_article',
            s::applyFilters('tag_taxonomy_args', [
                'public'            => true,
                'show_in_nav_menus' => false,
                'show_admin_column' => true,
                'hierarchical'      => false,

                'rewrite' => false, // See below.

                'description' => __('Knowledge Base Tags', 'woocommerce-kb-articles'),

                'labels' => [ // See: <http://jas.xyz/244m1Oc>
                    'name'          => __('KB Tags', 'woocommerce-kb-articles'),
                    'singular_name' => __('KB Tag', 'woocommerce-kb-articles'),

                    'name_admin_bar' => __('KB Tag', 'woocommerce-kb-articles'),
                    'menu_name'      => __('Tags', 'woocommerce-kb-articles'),

                    'all_items'           => __('All Tags', 'woocommerce-kb-articles'),
                    'add_new_item'        => __('Add New Tag', 'woocommerce-kb-articles'),
                    'new_item_name'       => __('New Tag Name', 'woocommerce-kb-articles'),
                    'add_or_remove_items' => __('Add or Remove Tags', 'woocommerce-kb-articles'),
                    'view_item'           => __('View Tag', 'woocommerce-kb-articles'),
                    'edit_item'           => __('Edit Tag', 'woocommerce-kb-articles'),
                    'update_item'         => __('Update Tag', 'woocommerce-kb-articles'),

                    'search_items' => __('Search Tags', 'woocommerce-kb-articles'),
                    'not_found'    => __('No Tags Found', 'woocommerce-kb-articles'),
                    'no_terms'     => __('No Tags', 'woocommerce-kb-articles'),

                    'choose_from_most_used'      => __('Choose From the Most Used Tags', 'woocommerce-kb-articles'),
                    'separate_items_with_commas' => __('Separate Tags w/ Commas', 'woocommerce-kb-articles'),

                    'items_list'            => __('Tags List', 'woocommerce-kb-articles'),
                    'items_list_navigation' => __('Tags List Navigation', 'woocommerce-kb-articles'),

                    'archives'          => __('All Tags', 'woocommerce-kb-articles'),
                    'popular_items'     => __('Popular Tags', 'woocommerce-kb-articles'),
                    'parent_item'       => __('Parent Tag', 'woocommerce-kb-articles'),
                    'parent_item_colon' => __('Parent Tag:', 'woocommerce-kb-articles'),
                ],

                'capabilities' => [
                    'assign_terms' => 'edit_kb_articles',
                    'edit_terms'   => 'edit_kb_articles',
                    'manage_terms' => 'edit_others_kb_articles',
                    'delete_terms' => 'delete_others_kb_articles',
                ],
            ])
        );
        # Post type rewrites.

        $archive_rewrite_config = [
            'with_front'  => false,
            'paged'       => true,
            'feed'        => true,
            'forcomments' => false,
            'endpoints'   => true,
            'walk_dirs'   => false,
            'ep_mask'     => EP_NONE,
        ];
        $permalink_rewrite_config = [
            'with_front'  => false,
            'paged'       => true,
            'feed'        => true,
            'forcomments' => false,
            'endpoints'   => true,
            'walk_dirs'   => false,
            'ep_mask'     => EP_PERMALINK,
        ];
        $archive_rewrite_config   = s::applyFilters('archive_rewrite_config', $archive_rewrite_config);
        $permalink_rewrite_config = s::applyFilters('permalink_rewrite_config', $permalink_rewrite_config);

        add_rewrite_tag('%kb_article%', '([^/]+)', 'post_type=kb_article&kb_article=');
        add_rewrite_tag('%kb_category%', '(.+?)', 'post_type=kb_article&taxonomy=kb_category&kb_category=');
        add_rewrite_tag('%kb_tag%', '([^/]+)', 'post_type=kb_article&taxonomy=kb_tag&kb_tag=');
        add_rewrite_tag('%kb_author%', '([^/]+)', 'post_type=kb_article&kb_author=');
        add_rewrite_tag('%kb_product%', '([^/]+)', 'post_type=kb_article&kb_product=');

        add_permastruct('kb_articles', $this->permalink_options['articles_base'].'/%kb_article%', $permalink_rewrite_config);
        add_permastruct('kb_categories', $this->permalink_options['categories_base'].'/%kb_category%', $archive_rewrite_config);
        add_permastruct('kb_tags', $this->permalink_options['tags_base'].'/%kb_tag%', $archive_rewrite_config);
        add_permastruct('kb_authors', $this->permalink_options['authors_base'].'/%kb_author%', $archive_rewrite_config);

        add_permastruct('kb_article', $this->permalink_options['article_base'].'/%kb_product%/%kb_article%', $permalink_rewrite_config);
        add_permastruct('kb_category', $this->permalink_options['category_base'].'/%kb_product%/%kb_category%', $archive_rewrite_config);
        add_permastruct('kb_tag', $this->permalink_options['tag_base'].'/%kb_product%/%kb_tag%', $archive_rewrite_config);
        add_permastruct('kb_author', $this->permalink_options['author_base'].'/%kb_product%/%kb_author%', $archive_rewrite_config);

        add_rewrite_rule($this->permalink_options['base'].'(?:/(?!page|feed|rdf|rss|rss2|atom)([^/]+))?/?$', 'index.php?post_type=kb_article&kb_product=$matches[1]', 'top');
        add_rewrite_rule($this->permalink_options['base'].'(?:/(?!page|feed|rdf|rss|rss2|atom)([^/]+))?/page/?([0-9]{1,})/?$', 'index.php?post_type=kb_article&kb_product=$matches[1]&paged=$matches[2]', 'top');
        add_rewrite_rule($this->permalink_options['base'].'(?:/(?!page|feed|rdf|rss|rss2|atom)([^/]+))?/(feed|rdf|rss|rss2|atom)/?$', 'index.php?post_type=kb_article&kb_product=$matches[1]&feed=$matches[2]', 'top');
        add_rewrite_rule($this->permalink_options['base'].'(?:/(?!page|feed|rdf|rss|rss2|atom)([^/]+))?/feed/(feed|rdf|rss|rss2|atom)/?$', 'index.php?post_type=kb_article&kb_product=$matches[1]&feed=$matches[2]', 'top');

        $WP_Post_Type              = get_post_type_object('kb_article');
        $WP_Post_Type->has_archive = $this->permalink_options['base'].'/%kb_product%';
        $WP_Post_Type->rewrite     = [
            'with_front' => $permalink_rewrite_config['with_front'],
            'pages'      => $permalink_rewrite_config['paged'],
            'feeds'      => $permalink_rewrite_config['feed'],
            'ep_mask'    => $permalink_rewrite_config['ep_mask'],
            'slug'       => $this->permalink_options['article_base'].'/%kb_product%',
        ];
        $category_taxonomy_object          = get_taxonomy('kb_category');
        $category_taxonomy_object->rewrite = [
            'hierarchical' => true, // e.g., `(.+?)` above.
            'with_front'   => $archive_rewrite_config['with_front'],
            'ep_mask'      => $archive_rewrite_config['ep_mask'],
            'slug'         => $this->permalink_options['category_base'].'/%kb_product%/%kb_category%',
        ];
        $tag_taxonomy_object          = get_taxonomy('kb_tag');
        $tag_taxonomy_object->rewrite = [
            'hierarchical' => false, // e.g., `([^/]+)` above.
            'with_front'   => $archive_rewrite_config['with_front'],
            'ep_mask'      => $archive_rewrite_config['ep_mask'],
            'slug'         => $this->permalink_options['tag_base'].'/%kb_product%/%kb_tag%',
        ];
    }

    /**
     * On `pre_get_posts` hook.
     *
     * @since 160731.38548 Initial release.
     *
     * @param \WP_Query $WP_Query The query.
     */
    public function onPreGetPosts(\WP_Query $WP_Query)
    {
        if (($kb_article = $WP_Query->get('kb_article'))) {
            $WP_Query->set('name', $kb_article);
        }
        if (($kb_author = $WP_Query->get('kb_author'))) {
            $WP_Query->set('author_name', $kb_author);
        }
        if (($kb_product = $WP_Query->get('kb_product'))) {
            $meta_query = [
                'key'   => s::postMetaKey('_product_id'),
                'value' => s::wcProductIdBySlug($kb_product),
            ];
            if (($existing_meta_queries = $WP_Query->get('meta_query'))) {
                $WP_Query->set('meta_query', ['relation' => 'AND', $meta_query, $existing_meta_queries]);
            } else {
                $WP_Query->set('meta_query', [$meta_query]);
            } // This preserves an existing meta key that is established already.
        }
        if ($this->Wp->is_admin && $WP_Query->get('orderby') === 'kb_product' && s::isMenuPageForPostType('kb_article')) {
            $WP_Query->set('meta_key', s::postMetaKey('_product_id'));
            $WP_Query->set('orderby', 'meta_value_num');
        }
    }

    /**
     * On `get_terms` filter.
     *
     * @since 160731.38548 Initial release.
     *
     * @param array      $clauses    An array of clauses.
     * @param array|null $taxonomies An array of taxonomies.
     * @param array      $terms      An array of args to `get_terms()`.
     *
     * @return array Filtered term query clauses.
     */
    public function onGetTermsClauses(array $clauses, $taxonomies, array $args): array
    {
        if ($this->Wp->is_admin) {
            return $clauses; // Not applicable.
        } elseif ($taxonomies !== ['kb_category'] && $taxonomies !== ['kb_tag']) {
            return $clauses; // Not applicable.
        } elseif (!is_singular(['kb_article', 'product']) && !is_post_type_archive('kb_article')) {
            return $clauses; // Not applicable.
        }
        if (is_singular('product')) {
            if (!($product_id = get_the_ID())) {
                return $clauses; // Not applicable.
            }
        } elseif (!($product_id = s::wcProductIdBySlug((string) get_query_var('kb_product')))) {
            return $clauses; // Not applicable.
        }
        $WpDb = s::wpDb(); // Database class.

        $__sql = /* Product-specific article IDs. */ '
            SELECT `__posts`.`ID`
                FROM `'.esc_sql($WpDb->posts).'` AS `__posts`,
                `'.esc_sql($WpDb->postmeta).'` AS `__meta`

            WHERE `__posts`.`post_type` = \'kb_article\'
                AND `__posts`.`ID` = `__meta`.`post_id`
                AND `__meta`.`meta_key` = %s
                AND `__meta`.`meta_value` = %s
        ';
        $__sql = $WpDb->prepare($__sql, s::postMetaKey('_product_id'), $product_id);

        $_sql = /* Product-specific term IDs. */ '
            SELECT `_term_relationships`.`term_taxonomy_id`
                FROM `'.esc_sql($WpDb->term_relationships).'` AS `_term_relationships`
            WHERE `_term_relationships`.`object_id` IN('.$__sql.')
        ';
        $clauses['where'] = !empty($clauses['where']) ? $clauses['where'].' AND' : '';
        $clauses['where'] .= ' `tt`.`term_taxonomy_id` IN('.$_sql.')';

        return $clauses;
    }

    /**
     * On `query_vars` filter.
     *
     * @since 160731.38548 Initial release.
     *
     * @param array $query_vars Public query vars.
     *
     * @return array Filtered public query vars.
     */
    public function onQueryVars(array $query_vars): array
    {
        return array_merge($query_vars, ['kb_article', 'kb_category', 'kb_tag', 'kb_author', 'kb_product']);
    }
}
