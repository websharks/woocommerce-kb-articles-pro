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

$App       = c::app(); // Plugin instance.
$is_search = is_search(); // A search query?
?>
<?php get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

        <?php if (have_posts()) : ?>

            <header class="page-header">
                <h1 class="page-title">
                    <?= a::archiveTitle(); ?>
                </h1>
                <?php the_archive_description('<div class="taxonomy-description">', '</div>'); ?>
            </header>

            <?php do_action('storefront_loop_before'); ?>

            <?php while (have_posts()) : the_post();
                $post_id       = (int) get_the_ID();
                $modified_date = get_the_modified_date();
                $date          = get_the_date();
                ?>

                <article id="post-<?= $post_id; ?>" <?php post_class(); ?>>

                    <header class="entry-header">
                        <h1 class="entry-title" title="<?= esc_attr(get_the_title()); ?>">
                            <span class="entry-modified" title="<?= sprintf(__('Last Modified: %1$s', 'woocommerce-kb-articles'), esc_attr($modified_date)); ?> / <?= sprintf(__('Published: %1$s', 'woocommerce-kb-articles'), esc_attr($date)); ?>">
                                <time class="entry-date"><?= esc_html($modified_date); ?></time>
                            </span>
                            <a href="<?= esc_url(get_permalink()); ?>" rel="bookmark"><i class="fa fa-file-text"></i> <?= $is_search ? a::highlightSearchTerms(get_the_title()) : get_the_title(); ?></a>
                        </h1>
                    </header>

                    <div class="entry-content">
                        <?php if ($is_search) : ?>
                            <?php
                            ob_start();
                            the_excerpt();
                            echo a::highlightSearchTerms(ob_get_clean());
                            ?>
                        <?php else : ?>
                            <?php the_excerpt(); ?>
                        <?php endif; ?>
                    </div>

                </article>

            <?php endwhile; ?>

            <?php do_action('storefront_loop_after'); // Pagination. ?>

        <?php else : ?>
            <?php get_template_part('content', 'none'); ?>
        <?php endif; ?>

        </main>
    </div>

<?php do_action('storefront_sidebar'); ?>
<?php get_footer(); ?>
