<?php

namespace SilverCart\Currencies\Extensions;

use SilverCart\Dev\Tools;
use SilverCart\Currencies\Model\Currency;
use SilverCart\Model\Customer\Customer;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Member;

/**
 * Extension for SiteConfig.
 * 
 * @package SilverCart
 * @subpackage Currencies\Extensions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 14.12.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property \SilverStripe\SiteConfig\SiteConfig $owner Owner
 */
class ConfigExtension extends DataExtension
{
    const SESSION_KEY_CURRENCY = 'SilverCart.Currency';
    
    /**
     * Updates the default currency.
     * 
     * @param string &$defaultCurrency Currency to update
     * 
     * @return void
     */
    public function updateDefaultCurrency(string &$defaultCurrency) : void
    {
        $currency = self::getCurrentCurrency();
        if ($currency instanceof Currency) {
            $defaultCurrency = $currency->Currency;
        }
    }
    
    /**
     * Returns thr current currency.
     * 
     * @return Currency|null
     */
    public static function getCurrentCurrency() : ?Currency
    {
        if (Tools::isBackendEnvironment()) {
            $currency = Currency::getDefault();
        } else {
            $currency        = null;
            $sessionCurrency = Tools::Session()->get(self::SESSION_KEY_CURRENCY);
            if (is_null($sessionCurrency)) {
                $customer = Customer::currentUser();
                if ($customer instanceof Member
                 && $customer->exists()
                ) {
                    $currency = $customer->DefaultCurrency();
                }
            } else {
                $currency = Currency::get()->byID($sessionCurrency);
            }
        }
        if (!($currency instanceof Currency)
         || !$currency->exists()
        ) {
            $currency = Currency::getDefault();
        }
        return $currency;
    }
    
    /**
     * Returns thr current currency.
     * Alias for self::getCurrentCurrency().
     * 
     * @return Currency|null
     */
    public static function CurrentCurrency() : ?Currency
    {
        return self::getCurrentCurrency();
    }
}