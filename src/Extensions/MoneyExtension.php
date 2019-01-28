<?php

namespace SilverCart\Currencies\Extensions;

use DataExtension;
use SilvercartConfig as Config;
use SilvercartCurrency as Currency;
use SilvercartTools as Tools;

/**
 * Extension for SilvercartMoney.
 * 
 * @package SilverCart
 * @subpackage Currencies\Extensions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 14.12.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class MoneyExtension extends DataExtension
{
    /**
     * Determines whether to update values or not.
     *
     * @var bool
     */
    protected static $skipUpdate = false;

    /**
     * Returns whether to skip updating values or not.
     * 
     * @return bool
     */
    public static function skipUpdate()
    {
        return self::getSkipUpdate();
    }
    
    /**
     * Returns whether to skip updating values or not.
     * 
     * @return bool
     */
    public static function getSkipUpdate()
    {
        if (Tools::isBackendEnvironment()) {
            self::setSkipUpdate(true);
        }
        return self::$skipUpdate;
    }
    
    /**
     * Sets whether to skip updating values or not.
     * 
     * @param bool $skipUpdate Skip updating values?
     * 
     * @return void
     */
    public static function setSkipUpdate($skipUpdate)
    {
        self::$skipUpdate = $skipUpdate;
    }
    
    /**
     * Updates the money amount.
     * 
     * @param float &$amount Amount to update
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.12.2018
     */
    public function updateAmount(&$amount)
    {
        if (self::skipUpdate()) {
            return;
        }
        $displayCurrency = Config::DefaultCurrency();
        self::setSkipUpdate(true);
        $currency = $this->owner->getCurrency();
        self::setSkipUpdate(false);
        if ($currency !== $displayCurrency) {
            $amount = Currency::convertFromCurrency($amount, $currency, $displayCurrency);
            $this->owner->setCurrency($displayCurrency);
        }
    }
    
    /**
     * Updates the money currency.
     * 
     * @param string &$currency Currency to update
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.12.2018
     */
    public function updateCurrency(&$currency)
    {
        if (self::skipUpdate()) {
            return;
        }
        $currency = Config::DefaultCurrency();
    }
}