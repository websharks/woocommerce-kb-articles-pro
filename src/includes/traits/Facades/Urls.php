<?php
/**
 * Article URLs.
 *
 * @author @jaswsinc
 * @copyright WP Sharks™
 */
declare (strict_types = 1);
namespace WebSharks\WpSharks\WooCommerceKBArticles\Pro\Traits\Facades;

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
 * Article URLs.
 *
 * @since 160731.38548
 */
trait Urls
{
    /**
     * @since 160731.38548 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\Utils\PostType::index()
     */
    public static function indexUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->Urls->index(...$args);
    }

    /**
     * @since 160731.38548 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\Utils\PostType::author()
     */
    public static function authorUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->Urls->author(...$args);
    }

    /**
     * @since 160731.38548 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\Utils\PostType::create()
     */
    public static function createUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->Urls->create(...$args);
    }
}
