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

$App = c::app(); // Plugin instance.
?>
<?php get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

        <?php while (have_posts()) : the_post();
            $post_id       = (int) get_the_ID();
            $modified_date = get_the_modified_date();
            $date          = get_the_date();
            ?>

            <article id="post-<?= $post_id; ?>" <?php post_class(); ?>>

                <header class="entry-header">
                    <h1 class="entry-title">
                        <span class="entry-modified" title="<?= sprintf(__('Last Modified: %1$s', 'woocommerce-kb-articles'), esc_attr($modified_date)); ?> / <?= sprintf(__('Published: %1$s', 'woocommerce-kb-articles'), esc_attr($date)); ?>">
                            <?= __('Last Modified:', 'woocommerce-kb-articles'); ?> <time class="entry-date"><?= esc_html($modified_date); ?></time>
                        </span>
                        <?= a::singleTitle(); ?>
                    </h1>
                </header>

                <aside class="entry-meta">
                    <div class="author">
                        <?= get_avatar(get_the_author_meta('ID'), 128); ?>
                        <div class="label"><?= __('Written by', 'woocommerce-kb-articles'); ?></div>
                        <?= get_the_author_posts_link(); ?>
                    </div>

                    <?php if (($categories = get_the_term_list($post_id, 'kb_category', '', __(', ', 'woocommerce-kb-articles')))) : ?>
                        <div class="cat-links">
                            <div class="label"><?= __('Posted in', 'woocommerce-kb-articles'); ?></div>
                            <?= $categories; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (($tags = get_the_term_list($post_id, 'kb_tag', '', __(', ', 'woocommerce-kb-articles')))) : ?>
                        <div class="tags-links">
                            <div class="label"><?= __('Tagged', 'woocommerce-kb-articles'); ?></div>
                            <?= $tags; ?>
                        </div>
                    <?php endif; ?>
                </aside>

                <?php do_action('storefront_single_post_before'); ?>

                <div class="entry-content">
                    <?php the_content(); ?>
                </div>

                <?php do_action('storefront_single_post_after'); ?>

            </article>

        <?php endwhile; ?>

        </main>
    </div>

<?php do_action('storefront_sidebar'); ?>
<?php get_footer(); ?>
