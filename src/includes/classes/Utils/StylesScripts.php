<?php
/**
 * Styles/scripts.
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
 * Styles/scripts.
 *
 * @since 160731.38548 Initial release.
 */
class StylesScripts extends SCoreClasses\SCore\Base\Core
{
    /**
     * Scripts/styles.
     *
     * @since 160731.38548 Initial release.
     */
    public function onWpEnqueueScripts()
    {
        if (!$this->mayApply()) {
            return; // Not applicable.
        }
        wp_enqueue_style($this->App->Config->©brand['©slug'], c::appUrl('/client-s/css/site/kb.min.css'), [], $this->App::VERSION);

        wp_enqueue_script($this->App->Config->©brand['©slug'], c::appUrl('/client-s/js/site/kb.min.js'), ['jquery'], $this->App::VERSION, true);
        wp_localize_script($this->App->Config->©brand['©slug'], 'wubXJBTLnHvFXQfaEehKKyrNtYFxmygsData', [
            'brand' => [
                'slug' => $this->App->Config->©brand['©slug'],
                'var'  => $this->App->Config->©brand['©var'],
            ],
            'i18n' => [],
        ]);
    }

    /**
     * Scripts/styles may apply?
     *
     * @since 160731.38548 Initial release.
     *
     * @return bool True if styles/scripts may apply.
     */
    public function mayApply(): bool
    {
        if (($may = &$this->cacheKey(__FUNCTION__)) !== null) {
            return $may; // Already cached this.
        }
        $may = is_post_type_archive('kb_article') || is_singular(['kb_article', 'product'])
                || (is_singular() && mb_strpos(get_post()->post_content, '[kb') !== false);

        return $may = s::applyFilters('styles_scripts_may_apply', $may);
    }
}
