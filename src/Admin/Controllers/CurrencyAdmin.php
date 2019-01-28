<?php

namespace SilverCart\Currencies\Admin\Controllers;

use SilvercartModelAdmin as ModelAdmin;

/**
 * 
 * @package SilverCart
 * @subpackage Currencies\Admin\Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.11.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CurrencyAdmin extends ModelAdmin
{
    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    public static $menuCode = 'config';
    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    public static $menuSortIndex = 50;
    /**
     * The URL segment
     *
     * @var string
     */
    public static $url_segment = 'silvercart-currencies';
    /**
     * The menu title
     *
     * @var string
     */
    public static $menu_title = 'Currencies';

    /**
     * Managed models
     *
     * @var array
     */
    public static $managed_models = [
        'SilvercartCurrency',
    ];
}