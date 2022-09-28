<?php

namespace SilverCart\Currencies\Admin\Controllers;

use SilverCart\Admin\Controllers\ModelAdmin as SilverCartModelAdmin;
use SilverCart\Currencies\Model\Currency;

/**
 * Model admin for SilverCart Currency.
 * 
 * @package SilverCart
 * @subpackage Currencies\Admin\Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.11.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CurrencyAdmin extends SilverCartModelAdmin
{
    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    private static $menuCode = 'config';
    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    private static $menuSortIndex = 50;
    /**
     * The URL segment
     *
     * @var string
     */
    private static $url_segment = 'silvercart-currencies';
    /**
     * The menu title
     *
     * @var string
     */
    private static $menu_title = 'Currencies';
    /**
     * Menu icon
     * 
     * @var string
     */
    private static $menu_icon = null;
    /**
     * Menu icon CSS class
     * 
     * @var string
     */
    private static $menu_icon_class = 'font-icon-menu-reports';
    /**
     * Managed models
     *
     * @var array
     */
    private static $managed_models = [
        Currency::class,
    ];
}