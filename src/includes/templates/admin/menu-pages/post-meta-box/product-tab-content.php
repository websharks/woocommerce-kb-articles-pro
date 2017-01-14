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
$Form = $this->s::postMetaBoxForm('product-tab-content');
?>
<?= $Form->openTable('', '', ['class' => '-block-display']); ?>

    <?php // Current tab content.
    if (s::postMetaExists(null, '_tab_content')) {
        $_tab_content = (string) s::getPostMeta(null, '_tab_content');
    } else {
        $_tab_content = s::getOption('product_tab_default_content');
    } // Only use default value if no meta values exist yet.
    ?>
    <?= $Form->textareaRow([
        'label' => __('Tab Content (e.g., <code>[kb /]</code>)', 'woocommerce-kb-articles'),
        'tip'   => __('Content displayed in the Knowledge Base tab.<hr />If empty, the Knowledge Base tab will simply not be shown.', 'woocommerce-kb-articles'),
        'name'  => '_tab_content', 'value' => $_tab_content,
    ]); ?>

<?= $Form->closeTable(); ?>
