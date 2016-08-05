<?php
/**
 * Application.
 *
 * @author @jaswsinc
 * @copyright WP Sharks™
 */
declare (strict_types = 1);
namespace WebSharks\WpSharks\WooCommerceKBArticles\Pro\Classes;

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
 * Application.
 *
 * @since 160731.38548 Initial release.
 */
class App extends SCoreClasses\App
{
    /**
     * Version.
     *
     * @since 160731.38548
     *
     * @var string Version.
     */
    const VERSION = '160803.25650'; //v//

    /**
     * Constructor.
     *
     * @since 160731.38548 Initial release.
     *
     * @param array $instance Instance args.
     */
    public function __construct(array $instance = [])
    {
        $instance_base = [
            '©di' => [
                '©default_rule' => [
                    'new_instances' => [],
                ],
            ],

            '§specs' => [
                '§in_wp'           => false,
                '§is_network_wide' => false,

                '§type' => 'plugin',
                '§file' => dirname(__FILE__, 4).'/plugin.php',
            ],
            '©brand' => [
                '©acronym' => 'WC KBAs',
                '©name'    => 'WooCommerce KB Articles',

                '©slug' => 'woocommerce-kb-articles',
                '©var'  => 'woocommerce_kb_articles',

                '©short_slug' => 'wc-kb-as',
                '©short_var'  => 'wc_kb_as',

                '©text_domain' => 'woocommerce-kb-articles',
            ],

            '§pro_option_keys' => [],
            '§default_options' => [
                'permalinks' => [
                    'base' => 'kb',

                    'articles_base'   => 'kb-articles',
                    'categories_base' => 'kb-categories',
                    'tags_base'       => 'kb-tags',
                    'authors_base'    => 'kb-authors',

                    'article_base'  => 'kb-article',
                    'category_base' => 'kb-category',
                    'tag_base'      => 'kb-tag',
                    'author_base'   => 'kb-author',

                    'product_endpoint' => 'kb',
                ],
                'product_tab_priority'        => 22,
                'product_tab_default_content' => '[kb /]',
                'product_tab_content_filters' => [
                    'jetpack-markdown',
                    'jetpack-latex',
                    'wptexturize',
                    'wpautop',
                    'shortcode_unautop',
                    'wp_make_content_images_responsive',
                    'capital_P_dangit',
                    'do_shortcode',
                    'convert_smilies',
                ],
            ],

            '§dependencies' => [
                '§plugins' => [
                    'woocommerce' => [
                        'in_wp'       => true,
                        'name'        => 'WooCommerce',
                        'url'         => 'https://wordpress.org/plugins/woocommerce/',
                        'archive_url' => 'https://wordpress.org/plugins/woocommerce/developers/',
                        'test'        => function (string $slug) {
                            $min_version = '2.6.2'; // Update when necessary.
                            if (version_compare(WC_VERSION, $min_version, '<')) {
                                return [
                                    'min_version' => $min_version,
                                    'reason'      => 'needs-upgrade',
                                ];
                            }
                        },
                    ],
                ],
                '§others' => [
                    'fancy_permalinks' => [
                        'name'        => __('Fancy Permalinks', 'woocommerce-kb-articles'),
                        'description' => __('a Permalink Structure other than <em>plain</em>', 'woocommerce-kb-articles'),

                        'test' => function (string $key) {
                            if (!get_option('permalink_structure')) {
                                return [
                                    'how_to_resolve' => sprintf(__('<a href="%1$s">change your Permalink settings</a> to anything but <em>plain</em>', 'woocommerce-kb-articles'), esc_url(admin_url('/options-permalink.php'))),
                                    'cap_to_resolve' => 'manage_options',
                                ];
                            }
                        },
                    ],
                ],
            ],
        ];
        parent::__construct($instance_base, $instance);
    }

    /**
     * Early hook setup handler.
     *
     * @since 160731.38548 Initial release.
     */
    protected function onSetupEarlyHooks()
    {
        parent::onSetupEarlyHooks();

        s::addAction('other_install_routines', [$this->Utils->Installer, 'onOtherInstallRoutines']);
        s::addAction('other_uninstall_routines', [$this->Utils->Uninstaller, 'onOtherUninstallRoutines']);
    }

    /**
     * Other hook setup handler.
     *
     * @since 160731.38548 Initial release.
     */
    protected function onSetupOtherHooks()
    {
        parent::onSetupOtherHooks();

        /* add_action('posts_pre_query', function ($_, \WP_Query $WP_Query) {
            header('content-type: text/plain; charset=utf-8');
            exit(print_r($WP_Query, true));
        }, 10, 2); */

        add_action('init', [$this->Utils->PostType, 'onInit'], 6);
        add_filter('query_vars', [$this->Utils->PostType, 'onQueryVars']);
        add_action('pre_get_posts', [$this->Utils->PostType, 'onPreGetPosts']);
        add_filter('terms_clauses', [$this->Utils->PostType, 'onGetTermsClauses'], 10, 3);

        add_filter('post_type_archive_link', [$this->Utils->Urls, 'onPostTypeArchiveLink'], 10, 2);
        add_filter('post_type_link', [$this->Utils->Urls, 'onPostTypeLink'], 10, 2);
        add_filter('term_link', [$this->Utils->Urls, 'onTermLink'], 10, 3);
        add_filter('author_link', [$this->Utils->Urls, 'onAuthorLink'], 10, 3);
        add_action('template_redirect', [$this->Utils->Urls, 'onTemplateRedirect']);

        add_action('init', [$this->Utils->WcProduct, 'onInit']);
        add_filter('woocommerce_product_tabs', [$this->Utils->WcProduct, 'onWcProductTabs']);
        add_filter('woocommerce_get_breadcrumb', [$this->Utils->WcBreadcrumbs, 'onWcGetBreadcrumb']);

        if ($this->Wp->is_woocommerce_product_vendors_active) {
            add_action('pre_get_posts', [$this->Utils->Vendors, 'onPreGetPosts']);
            add_filter('wcpv_default_admin_vendor_role_caps', [$this->Utils->Vendors, 'onDefaultCaps']);
            add_filter('wcpv_default_manager_vendor_role_caps', [$this->Utils->Vendors, 'onDefaultCaps']);
        }
        if ($this->Wp->is_admin) {
            add_action('admin_menu', [$this->Utils->MenuPage, 'onAdminMenu']);
            add_action('admin_init', [$this->Utils->WcProduct, 'onAdminInit']);
            add_action('admin_init', [$this->Utils->PostMetaBox, 'onAdminInit']);

            add_filter('manage_kb_article_posts_columns', [$this->Utils->PostTypeCols, 'onManagePostsColumns']);
            add_filter('manage_edit-kb_article_sortable_columns', [$this->Utils->PostTypeCols, 'onManageEditSortableColumns']);
            add_filter('manage_kb_article_posts_custom_column', [$this->Utils->PostTypeCols, 'onManagePostsCustomColumn'], 10, 2);
        }
        add_action('wp_enqueue_scripts', [$this->Utils->StylesScripts, 'onWpEnqueueScripts']);

        if ($this->Wp->template === 'storefront') {
            add_action('wp', [$this->Utils->Storefront, 'onWp']);
            add_action('wp_enqueue_scripts', [$this->Utils->Storefront, 'onWpEnqueueScripts']);
        }
        add_filter('single_template', [$this->Utils->Template, 'onSingleTemplate']);
        add_filter('archive_template', [$this->Utils->Template, 'onArchiveTemplate']);
        add_filter('search_template', [$this->Utils->Template, 'onSearchTemplate']);

        add_action('widgets_init', function () {
            register_widget(Classes\Widgets\Meta::class);
            register_widget(Classes\Widgets\Search::class);
            register_widget(Classes\Widgets\Product::class);
            register_widget(Classes\Widgets\Authors::class);
            register_widget(Classes\Widgets\Categories::class);
        });
        add_shortcode('kb', [$this->Utils->Shortcode, 'onShortcode']);

        $product_tab_content_filters = s::getOption('product_tab_content_filters');

        if (in_array('jetpack-markdown', $product_tab_content_filters, true) && s::jetpackCanMarkdown()) {
            s::addFilter('product_tab_content', c::class.'::stripLeadingIndents', -10000);
            s::addFilter('product_tab_content', s::class.'::jetpackMarkdown', -10000);
        }
        if (in_array('jetpack-latex', $product_tab_content_filters, true) && s::jetpackCanLatex()) {
            s::addFilter('product_tab_content', 'latex_markup', 9);
        }
        if (in_array('wptexturize', $product_tab_content_filters, true)) {
            s::addFilter('product_tab_content', 'wptexturize', 10);
        }
        if (in_array('wpautop', $product_tab_content_filters, true)) {
            s::addFilter('product_tab_content', 'wpautop', 10);
        }
        if (in_array('shortcode_unautop', $product_tab_content_filters, true)) {
            s::addFilter('product_tab_content', 'shortcode_unautop', 10);
        }
        if (in_array('wp_make_content_images_responsive', $product_tab_content_filters, true)) {
            s::addFilter('product_tab_content', 'wp_make_content_images_responsive', 10);
        }
        if (in_array('capital_P_dangit', $product_tab_content_filters, true)) {
            s::addFilter('product_tab_content', 'capital_P_dangit', 11);
        }
        if (in_array('do_shortcode', $product_tab_content_filters, true)) {
            s::addFilter('product_tab_content', 'do_shortcode', 11);
        }
        if (in_array('convert_smilies', $product_tab_content_filters, true)) {
            s::addFilter('product_tab_content', 'convert_smilies', 20);
        }
    }
}
