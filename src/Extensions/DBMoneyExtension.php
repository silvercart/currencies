<?php

namespace SilverCart\Currencies\Extensions;

use SilverCart\Dev\Tools;
use SilverCart\Admin\Model\Config as SilverCartConfig;
use SilverCart\Currencies\Model\Currency;
use SilverStripe\Core\Extension;

/**
 * Extension for SilvercartMoney.
 * 
 * @package SilverCart
 * @subpackage Currencies\Extensions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 14.12.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property \SilverCart\ORM\FieldType\DBMoney $owner Owner
 */
class DBMoneyExtension extends Extension
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
    public static function skipUpdate() : bool
    {
        return self::getSkipUpdate();
    }
    
    /**
     * Returns whether to skip updating values or not.
     * 
     * @return bool
     */
    public static function getSkipUpdate() : bool
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
    public static function setSkipUpdate(bool $skipUpdate) : void
    {
        self::$skipUpdate = $skipUpdate;
    }
    
    /**
     * Updates the money amount.
     * 
     * @param float|null &$amount Amount to update
     * 
     * @return void
     */
    public function updateAmount(?float &$amount) : void
    {
        if (self::skipUpdate()) {
            return;
        }
        $displayCurrency = SilverCartConfig::DefaultCurrency();
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
     */
    public function updateCurrency(string &$currency) : void
    {
        if (self::skipUpdate()) {
            return;
        }
        $currency = SilverCartConfig::DefaultCurrency();
    }
}