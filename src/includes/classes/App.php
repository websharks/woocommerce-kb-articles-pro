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
 * @since 16xxxx Initial release.
 */
class App extends SCoreClasses\App
{
    /**
     * Version.
     *
     * @since 16xxxx
     *
     * @var string Version.
     */
    const VERSION = '160728.16169'; //v//

    /**
     * Constructor.
     *
     * @since 16xxxx Initial release.
     *
     * @param array $instance Instance args.
     */
    public function __construct(array $instance = [])
    {
        $instance_base = [
            '©di' => [
                '©default_rule' => [
                    'new_instances' => [
                    ],
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

                '©short_slug' => 'wc-kbas',
                '©short_var'  => 'wc_kbas',

                '©text_domain' => 'woocommerce-kb-articles',
            ],

            '§pro_option_keys' => [],
            '§default_options' => [
                'permalinks' => [
                    'articles_base' => 'kb-articles',
                    'cats_base'     => 'kb-cats',
                    'tags_base'     => 'kb-tags',
                    'general_slug'  => 'general',
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
     * @since 16xxxx Initial release.
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
     * @since 16xxxx Initial release.
     */
    protected function onSetupOtherHooks()
    {
        parent::onSetupOtherHooks();

        if ($this->Wp->is_admin) {
            add_action('admin_init', [$this->Utils->PostMetaBox, 'onAdminInit']);
        }
        // After other WooCommerce post types.
        add_action('init', [$this->Utils->PostType, 'onInit'], 6);

        add_filter('query_vars', [$this->Utils->PostType, 'onQueryVars']);
        add_action('pre_get_posts', [$this->Utils->PostType, 'onPreGetPosts']);

        add_filter('post_type_link', [$this->Utils->PostType, 'onPostTypeLink'], 10, 2);
        add_filter('term_link', [$this->Utils->PostType, 'onTermLink'], 10, 3);
    }
}
