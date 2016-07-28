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

extract($this->vars); // Template variables.
$Form = $this->s::postMetaBoxForm('product');
?>
<?= $Form->openTable(); ?>

    <?= $Form->selectRow([
        'label' => __('WooCommerce Product', 'woocommerce-kb-articles'),
        'tip'   => __('Choose a product if you\'d like this article to be connected to a specific product.', 'woocommerce-kb-articles'),

        'name'    => '_product_id',
        'value'   => s::getPostMeta($post_id, '_product_id'),
        'options' => s::postSelectOptions([
            'allow_empty'        => true,
            'allow_arbitrary'    => false,
            'include_post_types' => ['product'],
            'current_post_ids'   => [s::getPostMeta($post_id, '_product_id')],
        ]),
    ]); ?>

<?= $Form->closeTable(); ?>
