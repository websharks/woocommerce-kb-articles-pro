<?php
/**
 * Template.
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

$Form              = $this->s::menuPageForm('§save-options');
$permalink_options = s::getOption('permalinks');
$root_host         = $this->App->Config->©urls['©hosts']['©roots']['©app'];
?>
<?= $Form->openTag(); ?>
    <?= $Form->openTable(
        __('Product Tab Options', 'woocommerce-kb-articles'),
        sprintf(__('These allow you to customize the \'Knowledge Base\' product tab added by this plugin. The defaults are usually just fine, but you can customize further if you like. You can also browse <em>our</em> <a href="%1$s" target="_blank">knowledge base</a> to learn more about these options.', 'woocommerce-kb-articles'), esc_url(s::brandUrl('/kb')))
    ); ?>

        <?= $Form->inputRow([
            'type'  => 'number',
            'label' => __('Tab Priority (Position)', 'woocommerce-kb-articles'),
            'tip'   => __('This controls the tab display position in WooCommerce.<hr />You should only need to change this if you have another plugin that adds a new tab with the same (i.e., a conflicting) priority.', 'woocommerce-kb-articles'),

            'name'  => 'product_tab_priority',
            'value' => s::getOption('product_tab_priority'),
        ]); ?>

        <?= $Form->textareaRow([
            'label' => __('Default Content', 'woocommerce-kb-articles'),
            'tip'   => __('When you add a new product, this will be the default Knowledge Base tab content.<hr />Setting this to <code>[kb /]</code> is a suggested default, but you can learn more about the shortcode and customize it further if you like.', 'woocommerce-kb-articles'),
            'note'  => __('e.g., <code>[kb show_search_box="yes" max="25" /]</code> and many other supported attributes.', 'woocommerce-kb-articles'),

            'name'  => 'product_tab_default_content',
            'value' => s::getOption('product_tab_default_content'),
        ]); ?>

        <?= $Form->selectRow([
            'label' => __('Content Filters', 'woocommerce-kb-articles'),
            'tip'   => __('This controls which built-in WordPress content filters are applied to content in the \'Knowledge Base\' Product tab. All content filters suggested.<hr />Note: <code>jetpack-markdown</code> is only possible if you have Jetpack installed with Markdown enabled. The same is true for <code>jetpack-latex</code>.', 'woocommerce-kb-articles'),

            'name'     => 'product_tab_content_filters',
            'multiple' => true, // i.e., An array.
            'value'    => s::getOption('product_tab_content_filters'),
            'options'  => [
                'jetpack-markdown'                  => 'jetpack-markdown',
                'jetpack-latex'                     => 'jetpack-latex',
                'wptexturize'                       => 'wptexturize',
                'wpautop'                           => 'wpautop',
                'shortcode_unautop'                 => 'shortcode_unautop',
                'wp_make_content_images_responsive' => 'wp_make_content_images_responsive',
                'capital_P_dangit'                  => 'capital_P_dangit',
                'do_shortcode'                      => 'do_shortcode',
                'convert_smilies'                   => 'convert_smilies',
            ],
        ]); ?>

    <?= $Form->closeTable(); ?>

    <?= $Form->openTable(
        __('Permalink Options', 'woocommerce-kb-articles'),
        __('This controls URLs that lead to KB indexes, articles, categories, tags, etc. The defaults are usually just fine, but you can adjust if necessary.', 'woocommerce-kb-articles').
        ' '.__('If you do customize these, be sure to use <strong>unique</strong> values; i.e., none of these can be the same as any other.', 'woocommerce-kb-articles')
    ); ?>

        <?= $Form->inputRow([
            'label' => __('Root Index Base', 'woocommerce-kb-articles'),
            'tip'   => sprintf(__('%1$s/<code>%2$s</code>', 'woocommerce-kb-articles'), esc_html($root_host), esc_html($permalink_options['base'])),

            'name'  => '[permalinks][base]',
            'value' => $permalink_options['base'],
        ]); ?>

        <?= $Form->inputRow([
            'label' => __('Articles Base', 'woocommerce-kb-articles'),
            'tip'   => sprintf(__('%1$s/<code>%2$s</code>/[slug]', 'woocommerce-kb-articles'), esc_html($root_host), esc_html($permalink_options['articles_base'])),

            'name'  => '[permalinks][articles_base]',
            'value' => $permalink_options['articles_base'],
        ]); ?>

        <?= $Form->inputRow([
            'label' => __('Product-Specific Article', 'woocommerce-kb-articles'),
            'tip'   => sprintf(__('%1$s/<code>%2$s</code>/[product]/[slug]', 'woocommerce-kb-articles'), esc_html($root_host), esc_html($permalink_options['article_base'])),

            'name'  => '[permalinks][article_base]',
            'value' => $permalink_options['article_base'],
        ]); ?>

        <?= $Form->inputRow([
            'label' => __('Categories Base', 'woocommerce-kb-articles'),
            'tip'   => sprintf(__('%1$s/<code>%2$s</code>/[slug]', 'woocommerce-kb-articles'), esc_html($root_host), esc_html($permalink_options['categories_base'])),

            'name'  => '[permalinks][categories_base]',
            'value' => $permalink_options['categories_base'],
        ]); ?>

        <?= $Form->inputRow([
            'label' => __('Product-Specific Category', 'woocommerce-kb-articles'),
            'tip'   => sprintf(__('%1$s/<code>%2$s</code>/[product]/[slug]', 'woocommerce-kb-articles'), esc_html($root_host), esc_html($permalink_options['category_base'])),

            'name'  => '[permalinks][category_base]',
            'value' => $permalink_options['category_base'],
        ]); ?>

        <?= $Form->inputRow([
            'label' => __('Tags Base', 'woocommerce-kb-articles'),
            'tip'   => sprintf(__('%1$s/<code>%2$s</code>/[slug]', 'woocommerce-kb-articles'), esc_html($root_host), esc_html($permalink_options['tags_base'])),

            'name'  => '[permalinks][tags_base]',
            'value' => $permalink_options['tags_base'],
        ]); ?>

        <?= $Form->inputRow([
            'label' => __('Product-Specific Tag', 'woocommerce-kb-articles'),
            'tip'   => sprintf(__('%1$s/<code>%2$s</code>/[product]/[slug]', 'woocommerce-kb-articles'), esc_html($root_host), esc_html($permalink_options['tag_base'])),

            'name'  => '[permalinks][tag_base]',
            'value' => $permalink_options['tag_base'],
        ]); ?>

        <?= $Form->inputRow([
            'label' => __('Authors Base', 'woocommerce-kb-articles'),
            'tip'   => sprintf(__('%1$s/<code>%2$s</code>/[slug]', 'woocommerce-kb-articles'), esc_html($root_host), esc_html($permalink_options['authors_base'])),

            'name'  => '[permalinks][authors_base]',
            'value' => $permalink_options['authors_base'],
        ]); ?>

        <?= $Form->inputRow([
            'label' => __('Product-Specific Author', 'woocommerce-kb-articles'),
            'tip'   => sprintf(__('%1$s/<code>%2$s</code>/[product]/[slug]', 'woocommerce-kb-articles'), esc_html($root_host), esc_html($permalink_options['author_base'])),

            'name'  => '[permalinks][author_base]',
            'value' => $permalink_options['author_base'],
        ]); ?>

    <?= $Form->closeTable(); ?>

    <?= $Form->openTable(
        __('Permalink Endpoint Options', 'woocommerce-kb-articles'),
        __('An Endpoint adds a new behavhior to an existing Permalink structure in WooCommerce.', 'woocommerce-kb-articles')
    ); ?>

        <?= $Form->inputRow([
            'label' => __('Product Endpoint Redirect', 'woocommerce-kb-articles'),
            'tip'   => sprintf(__('This is a product endpoint that, if accessed, will redirect a visitor to the KB index for a specific product.<hr />e.g., %1$s/product/[slug]/<code>kb</code>', 'woocommerce-kb-articles'), esc_html($root_host), esc_html($permalink_options['product_endpoint'])),

            'name'  => '[permalinks][product_endpoint]',
            'value' => $permalink_options['product_endpoint'],
        ]); ?>

    <?= $Form->closeTable(); ?>

    <?= $Form->submitButton(); ?>
<?= $Form->closeTag(); ?>
