<?php
/**
 * Template.
 *
 * @author @jaswsinc
 * @copyright WP Sharks™
 */
declare (strict_types = 1);
namespace WebSharks\WpSharks\WooCommerceKBArticles\Pro;

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

if (!defined('WPINC')) {
    exit('Do NOT access this file directly.');
}
extract($this->vars); // Template variables.
?>

<div class="<?= esc_attr($this->App->Config->©brand['©slug'].'-shortcode'); ?>">

    <?php if ($atts['show_search_box'] === 'top') : ?>
        <div class="-search-box --top">
            <form role="search" method="get" class="-form" action="<?= esc_url(get_post_type_archive_link('kb_article')); ?>" target="<?= esc_attr($atts['search_link_target']); ?>">
                <input type="search" name="s" class="-s" value="<?= esc_attr(get_search_query()); ?>" placeholder="<?= _('🔎   Search KB Articles &hellip;'); ?>" />
            </form>
        </div>
    <?php endif; ?>

    <?php if ($categories || $tags) : ?>
        <div class="-taxonomies">
            <?php if ($categories) : ?>
                <div class="-taxonomy -categories">
                    <h4><?= __('Categories', 'woocommerce-kb-articles'); ?></h4>
                    <ul class="-list">
                        <?php foreach ($categories as $_WP_Term) : ?>
                            <li class="-list-item -category" title="<?= esc_attr($_WP_Term->name); ?>">
                                <i class="fa fa-folder"></i> <a href="<?= esc_url(get_term_link($_WP_Term->term_id, $_WP_Term->taxonomy)); ?>" target="<?= esc_attr($atts['tax_link_target']); ?>"><?= $_WP_Term->name; ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($tags) : ?>
                <div class="-taxonomy -tags">
                    <h4><?= __('Tags', 'woocommerce-kb-articles'); ?></h4>
                    <ul class="-list">
                        <?php foreach ($tags as $_WP_Term) : ?>
                            <li class="-list-item -tag" title="<?= esc_attr($_WP_Term->name); ?>">
                                <a href="<?= esc_url(get_term_link($_WP_Term->term_id, $_WP_Term->taxonomy)); ?>" target="<?= esc_attr($atts['tax_link_target']); ?>">
                                    <i class="fa fa-tag"></i> <?= $_WP_Term->name; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="-articles">
        <?php if ($WP_Query->have_posts()) : ?>
            <ul class="-list">
                <?php while ($WP_Query->have_posts()) : $WP_Query->the_post(); ?>
                    <li class="-list-item -article">
                        <h4 class="-title<?= $atts['one_line_titles'] ? ' -one-line' : ''; ?>" title="<?= esc_attr(get_the_title()); ?>">
                            <i class="fa fa-file-text"></i> <a href="<?= esc_url(get_permalink()); ?>" target="<?= esc_attr($atts['link_target']); ?>"><?= get_the_title(); ?></a>
                        </h4>
                        <?php if ($atts['show_excerpts']) : ?>
                            <div class="-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>

        <?php if ($WP_Query->found_posts > $atts['max']) : ?>
            <div class="-more">
                <a href="<?= esc_url(get_post_type_archive_link('kb_article')); ?>" target="<?= esc_attr($atts['link_target']); ?>">
                    <?= __('More Knowledge Base Articles', 'woocommerce-kb-articles'); ?> <i class="fa fa-caret-right"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($atts['show_search_box'] === 'bottom') : ?>
        <div class="-search-box --bottom">
            <form role="search" method="get" class="-form" action="<?= esc_url(get_post_type_archive_link('kb_article')); ?>" target="<?= esc_attr($atts['search_link_target']); ?>">
                <input type="search" name="s" class="-s" value="<?= esc_attr(get_search_query()); ?>" placeholder="<?= _('🔎   Search KB Articles &hellip;'); ?>" />
            </form>
        </div>
    <?php endif; ?>

</div>

<?php wp_reset_postdata(); ?>
