<?php
/**
 * Article title utils.
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
 * Article title utils.
 *
 * @since 16xxxx Initial release.
 */
class Title extends SCoreClasses\SCore\Base\Core
{
    /**
     * Single post type title.
     *
     * @since 16xxxx Initial release.
     *
     * @return array Single title.
     */
    public function forSingle(): string
    {
        if (!is_singular('kb_article')) {
            return ''; // Not applicable.
        }
        $parts       = $this->singleParts();
        $total_parts = count($parts);
        $title       = ''; // Initialize.

        for ($_i = 0; $_i < $total_parts; ++$_i) {
            $_is_first_part   = $_i === 0;
            $_is_last_part    = $_i + 1 === $total_parts;
            $_have_more_parts = $total_parts >= $_i + 1;
            $_is_title_part   = $parts[$_i]['is_title'] ?? false;

            if (!$_is_first_part && $_is_title_part) {
                $title .= '<br />'; // Break line.
            } elseif (!$_is_first_part) {
                $title .= ' <span class="-sep">&raquo;</span> ';
            }
            if ($_is_last_part || !$parts[$_i]['url']) {
                $title .= $parts[$_i]['title'];
            } else {
                $title .= '<a href="'.esc_url($parts[$_i]['url']).'">'.$parts[$_i]['title'].'</a>';
            }
        } // unset($_i, $_is_first_part, $_is_last_part, $_have_more_parts, $_is_title_part); // Housekeeping.

        return s::applyFilters('single_title', $title, $parts);
    }

    /**
     * Post type archive title.
     *
     * @since 16xxxx Initial release.
     *
     * @return string Archive title.
     */
    public function forArchive(): string
    {
        if (!is_post_type_archive('kb_article')) {
            return ''; // Not applicable.
        }
        $parts                 = $this->archiveParts();
        $total_parts           = count($parts);
        $title                 = ''; // Initialize.
        $a_product_part_exists = false;

        foreach ($parts as $_part) {
            if (!empty($_part['is_product'])) {
                $a_product_part_exists = true;
                break; // Done here.
            }
        } // unset($_part); // Housekeeping.

        for ($_i = 0; $_i < $total_parts; ++$_i) {
            $_is_first_part         = $_i === 0;
            $_is_last_part          = $_i + 1 === $total_parts;
            $_have_more_parts       = $total_parts >= $_i + 1;
            $_prev_part_was_product = !$_is_first_part && ($parts[$_i - 1]['is_product'] ?? false);

            if ($a_product_part_exists && $_prev_part_was_product && $_have_more_parts) {
                $title .= '<br />';
            } elseif (!$a_product_part_exists && $_i === 2 && $_have_more_parts) {
                $title .= '<br />';
            } elseif (!$_is_first_part) {
                $title .= ' <span class="-sep">&raquo;</span> ';
            }
            if ($_is_last_part || !$parts[$_i]['url']) {
                $title .= $parts[$_i]['title'];
            } else {
                $title .= '<a href="'.esc_url($parts[$_i]['url']).'">'.$parts[$_i]['title'].'</a>';
            }
        } // unset($_i, $_is_first_part, $_is_last_part, $_have_more_parts, $_prev_part_was_product); // Housekeeping.

        return s::applyFilters('archive_title', $title, $parts);
    }

    /**
     * Article title parts.
     *
     * @since 16xxxx Initial release.
     *
     * @param string $title Avoids `get_the_title()`.
     *
     * @return array Article title parts.
     */
    public function singleParts(string $title = null): array
    {
        if ($this->Wp->is_admin) {
            return []; // Not applicable.
        } elseif (!is_singular('kb_article')) {
            return []; // Not applicable.
        } elseif (($parts = &$this->cacheKey(__FUNCTION__)) !== null) {
            return $parts; // Already cached this.
        }
        $parts = []; // Initialize array of all parts.

        $WC_Product    = s::wcProductBySlug((string) get_query_var('kb_product'));
        $product_title = $WC_Product && $WC_Product->exists() ? $WC_Product->get_title() : '';

        if ($WC_Product && $product_title) {
            $parts[] = ['title' => __('KB', 'woocommerce-kb-articles'), 'url' => a::indexUrl()];
            $parts[] = ['title' => $product_title, 'url' => get_post_type_archive_link('kb_article')];
        } else {
            $parts[] = ['title' => __('Knowledge Base', 'woocommerce-kb-articles'), 'url' => a::indexUrl()];
        }
        $parts[] = ['title' => $title ?? get_the_title(), 'url' => get_permalink(), 'is_title' => true];

        return s::applyFilters('single_title_parts', $parts);
    }

    /**
     * Archive title parts.
     *
     * @since 16xxxx Initial release.
     *
     * @return array Archive title parts.
     */
    public function archiveParts(): array
    {
        if ($this->Wp->is_admin) {
            return []; // Not applicable.
        } elseif (!is_post_type_archive('kb_article')) {
            return []; // Not applicable.
        } elseif (($parts = &$this->cacheKey(__FUNCTION__)) !== null) {
            return $parts; // Already cached this.
        }
        $parts = []; // Initialize array of all parts.

        $WC_Product    = s::wcProductBySlug((string) get_query_var('kb_product'));
        $product_id    = $WC_Product && $WC_Product->exists() ? (int) $WC_Product->get_id() : 0;
        $product_title = $WC_Product && $WC_Product->exists() ? $WC_Product->get_title() : '';

        $is_category = is_tax('kb_category');
        $is_tag      = !$is_category && is_tax('kb_tag');
        $is_tax      = $is_category || $is_tag;

        $WP_Term            = $is_tax ? get_queried_object() : null;
        $term_ancestors     = $is_category ? get_ancestors($WP_Term->term_id, $WP_Term->taxonomy, 'taxonomy') : [];
        $parent_terms       = []; // Initialize array of parent terms.
        $total_parent_terms = 0; // Initialize counter also.

        $is_author = !$is_tax && ($author_name = (string) get_query_var('author_name'));
        $WP_User   = $is_author ? get_user_by('slug', $author_name) : null;

        $is_search    = is_search(); // Also a search?
        $search_terms = $is_search ? (string) get_query_var('s') : '';

        $category_icon = '<i class="fa fa-folder-open-o"></i>';
        $category_span = '<span class="-category-name -term-name">';

        $tag_icon = '<i class="fa fa-tag"></i>';
        $tag_span = '<span class="-tag-name -term-name">';

        $author_icon = '<i class="fa fa-user"></i>';
        $author_span = '<span class="-author-name">';

        if ($is_tax && $WP_Term && $term_ancestors) {
            foreach ($term_ancestors as $_term_id) {
                if (($_WP_Term = get_term($_term_id, $WP_Term->taxonomy))) {
                    $parent_terms[] = $_WP_Term;
                    ++$total_parent_terms;
                } // Array of `\WP_Term` class instances.
            } // unset($_term_id, $_WP_Term); // Housekeeping.
        }
        if (($WC_Product && $product_id && $product_title) || ($is_tax && $WP_Term) || ($is_author && $WP_User) || ($is_search && $search_terms)) {
            $parts[] = ['title' => __('KB', 'woocommerce-kb-articles'), 'url' => a::indexUrl()];
        } else {
            $parts[] = ['title' => __('Knowledge Base', 'woocommerce-kb-articles'), 'url' => a::indexUrl()];
        }
        if ($WC_Product && $product_id && $product_title) {
            $parts[] = ['title' => $product_title, 'url' => get_post_type_archive_link('kb_article'), 'is_product' => true];
        }
        if ($is_category && $WP_Term) {
            if ($parent_terms) {
                for ($_i = 0; $_i < count($parent_terms); ++$_i) {
                    $_WP_Term = $parent_terms[$_i];
                    $parts[]  = ['title' => $category_span.$_WP_Term->name.'</span>', 'url' => get_term_link($_WP_Term->term_id, $_WP_Term->taxonomy)];
                } // unset($_i, $_WP_Term); // Housekeeping.
                $parts[] = ['title' => $category_icon.' '.$category_span.$WP_Term->name.'</span>', 'url' => get_term_link($WP_Term->term_id, $WP_Term->taxonomy)];
            } else {
                $parts[] = ['title' => $category_icon.' '.$category_span.$WP_Term->name.'</span>', 'url' => get_term_link($WP_Term->term_id, $WP_Term->taxonomy)];
            }
        } elseif ($is_tag && $WP_Term) {
            $parts[] = ['title' => $tag_icon.' '.$tag_span.$WP_Term->name.'</span>', 'url' => get_term_link($WP_Term->term_id, $WP_Term->taxonomy)];
            //
        } elseif ($is_author && $WP_User) {
            $parts[] = ['title' => $author_icon.' '.__('Author', 'woocommerce-kb-articles').': '.$author_span.$WP_User->display_name.'</span>', 'url' => get_author_posts_url($WP_User->ID)];
        }
        if ($is_search && $search_terms) {
            $parts[] = ['title' => sprintf(__('🔎 Results for: <tt class="-s">%1$s</tt>', 'woocommerce-kb-articles'), esc_html($search_terms)), 'url' => ''];
        }
        return s::applyFilters('archive_title_parts', $parts);
    }
}
