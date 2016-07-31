<?php
/**
 * Shortcode utils.
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
 * Shortcode utils.
 *
 * @since 160731.38548 Initial release.
 */
class Shortcode extends SCoreClasses\SCore\Base\Core
{
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
    }

    /**
     * Shortcode.
     *
     * @since 160707.2545 Initial release.
     *
     * @param array|string $atts      Shortcode attributes.
     * @param string|null  $content   Shortcode content.
     * @param string       $shortcode Shortcode name.
     */
    public function onShortcode($atts = [], $content = '', $shortcode = ''): string
    {
        /*
         * Content/shortcode.
         */
        $atts      = is_array($atts) ? $atts : [];
        $content   = (string) $content;
        $shortcode = (string) $shortcode;

        /*
         * Parse attributes.
         */
        $default_atts = [
            'meta_query_logic' => 'AND',
            'for_product_ids'  => '',

            'article_ids'     => '',
            'ignore_stickies' => '0',

            'tax_query_logic' => 'OR',
            'in_category_ids' => '',
            'with_tag_ids'    => '',

            'order_by' => 'comment_count:DESC,modified:DESC',
            'max'      => '25', // -1 = all.

            'show_search_box'    => 'true',
            'search_link_target' => '_self',

            'show_categories'     => 'true',
            'child_categories_of' => '0', // `0` = top-level only.
            // Set to `-1` (or empty string) to disable this behavior.
            // // Does not apply when showing specific categories.
            'max_categories' => '25', // `0` = all.

            'show_tags' => 'true',
            'max_tags'  => '25', // `0` = all.

            'link_target'     => '_self',
            'tax_link_target' => '_self',
        ];
        if (is_singular('product')) {
            $default_atts['for_product_ids'] = get_the_ID();
        }
        if (!empty($atts['article_ids'])) {
            $default_atts['ignore_stickies'] = 'true';
            $default_atts['order_by']        = 'post__in';
        }
        $atts = shortcode_atts($default_atts, $atts, $shortcode);
        $atts = array_map('strval', $atts); // Force strings.

        $atts['for_product_ids'] = preg_split('/[\s,]+/u', $atts['for_product_ids'], -1, PREG_SPLIT_NO_EMPTY);

        $atts['article_ids']     = preg_split('/[\s,]+/u', $atts['article_ids'], -1, PREG_SPLIT_NO_EMPTY);
        $atts['ignore_stickies'] = filter_var($atts['ignore_stickies'], FILTER_VALIDATE_BOOLEAN);

        $atts['in_category_ids'] = preg_split('/[\s,]+/u', $atts['in_category_ids'], -1, PREG_SPLIT_NO_EMPTY);
        $atts['with_tag_ids']    = preg_split('/[\s,]+/u', $atts['with_tag_ids'], -1, PREG_SPLIT_NO_EMPTY);

        $atts['order_by'] = preg_split('/[\s,]+/u', $atts['order_by'], -1, PREG_SPLIT_NO_EMPTY);
        $atts['max']      = max(-1, (int) $atts['max']);

        if (!in_array(mb_strtolower($atts['show_search_box']), ['top', 'bottom'], true)) {
            $atts['show_search_box'] = filter_var($atts['show_search_box'], FILTER_VALIDATE_BOOLEAN);
            $atts['show_search_box'] = $atts['show_search_box'] ? 'top' : '';
        }
        if (in_array(mb_strtolower($atts['show_categories']), ['', '1', 'on', 'yes', 'true', '0', 'off', 'no', 'false'], true)) {
            $atts['show_categories']     = filter_var($atts['show_categories'], FILTER_VALIDATE_BOOLEAN);
            $atts['child_categories_of'] = max(-1, isset($atts['child_categories_of'][0]) ? (int) $atts['child_categories_of'] : -1);
        } else {
            $atts['show_categories']     = preg_split('/[\s,]+/u', $atts['show_categories'], -1, PREG_SPLIT_NO_EMPTY);
            $atts['child_categories_of'] = -1; // Not applicable; showing specific categories.
        }
        $atts['max_categories'] = max(0, (int) $atts['max_categories']);

        if (in_array(mb_strtolower($atts['show_tags']), ['', '1', 'on', 'yes', 'true', '0', 'off', 'no', 'false'], true)) {
            $atts['show_tags'] = filter_var($atts['show_tags'], FILTER_VALIDATE_BOOLEAN);
        } else {
            $atts['show_tags'] = preg_split('/[\s,]+/u', $atts['show_tags'], -1, PREG_SPLIT_NO_EMPTY);
        }
        $atts['max_tags'] = max(0, (int) $atts['max_tags']);

        // Filter for other plugins/extensions.
        $atts = s::applyFilters('shortcode_atts', $atts);

        /*
         * Build & do query.
         */
        $query_args['post_type'] = 'kb_article';

        if ($atts['for_product_ids']) {
            if (!empty($query_args['meta_query'])) {
                $query_args['meta_query']['relation'] = $atts['meta_query_logic'];
            }
            $query_args['meta_query'][] = [
                'key'     => s::postMetaKey('_product_id'),
                'value'   => $atts['for_product_ids'],
                'compare' => 'IN',
            ];
        }
        if ($atts['article_ids']) {
            $query_args['post__in'] = $atts['article_ids'];
        }
        $query_args['ignore_sticky_posts'] = $atts['ignore_stickies'];

        if ($atts['in_category_ids']) {
            if (!empty($query_args['tax_query'])) {
                $query_args['tax_query']['relation'] = $atts['tax_query_logic'];
            }
            $query_args['tax_query'][] = [
                'taxonomy' => 'kb_category',
                'field'    => 'term_id',
                'terms'    => $atts['in_category_ids'],
                'operator' => 'IN',
            ];
        }
        if ($atts['with_tag_ids']) {
            if (!empty($query_args['tax_query'])) {
                $query_args['tax_query']['relation'] = $atts['tax_query_logic'];
            }
            $query_args['tax_query'][] = [
                'taxonomy' => 'kb_tag',
                'field'    => 'term_id',
                'terms'    => $atts['with_tag_ids'],
                'operator' => 'IN',
            ];
        }
        if (in_array('post__in', $atts['order_by'], true)) {
            $query_args['orderby'] = 'post__in';
        } else {
            foreach ($atts['order_by'] as $_order_by) {
                $_parts                            = explode(':', $_order_by, 2);
                $query_args['orderby'][$_parts[0]] = $_parts[1] ?? 'ASC';
            } // unset($_order_by, $_parts);
        }
        $query_args['posts_per_page'] = $atts['max'];

        // Filter for other plugins/extensions.
        $query_args = s::applyFilters('shortcode_query_args', $query_args);

        $WP_Query = new \WP_Query($query_args);

        /*
         * Build & do categories query.
         */
        if ($atts['show_categories'] === true || is_array($atts['show_categories'])) {
            $category_query_args['taxonomy'] = 'kb_category';

            if ($atts['show_categories'] && is_array($atts['show_categories'])) {
                $category_query_args['orderby'] = 'include';
                $category_query_args['include'] = $atts['show_categories'];
            } else { // Alphabetically & perhaps children only.
                if ($atts['child_categories_of'] >= 0) {
                    $category_query_args['parent'] = $atts['child_categories_of'];
                }
                $category_query_args['orderby'] = 'name';
                $category_query_args['order']   = 'ASC';
            }
            $category_query_args['number'] = $atts['max_categories'];

            // Filter for other plugins/extensions.
            $category_query_args = s::applyFilters('shortcode_category_query_args', $category_query_args);

            $categories = get_terms($category_query_args);
            $categories = is_array($categories) ? $categories : [];
        } else {
            $categories = []; // Otherwise, no categories in this case (do not show).
        }

        /*
         * Build & do tags query.
         */
        if ($atts['show_tags'] === true || is_array($atts['show_tags'])) {
            $tag_query_args['taxonomy'] = 'kb_tag';

            if ($atts['show_tags'] && is_array($atts['show_tags'])) {
                $tag_query_args['orderby'] = 'include';
                $tag_query_args['include'] = $atts['show_tags'];
            } else { // Alphabetically.
                $tag_query_args['orderby'] = 'name';
                $tag_query_args['order']   = 'ASC';
            }
            $tag_query_args['number'] = $atts['max_tags'];

            // Filter for other plugins/extensions.
            $tag_query_args = s::applyFilters('shortcode_tag_query_args', $tag_query_args);

            $tags = get_terms($tag_query_args);
            $tags = is_array($tags) ? $tags : [];
        } else {
            $tags = []; // Otherwise, no tags in this case (do not show).
        }

        /*
         * Return shortcode content via template.
         */
        return c::getTemplate('site/shortcodes/kb.php')->parse(compact('atts', 'WP_Query', 'categories', 'tags'));
    }
}
