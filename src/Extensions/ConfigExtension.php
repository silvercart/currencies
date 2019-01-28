<?php

namespace SilverCart\Currencies\Extensions;

use DataExtension;
use Member;
use Session;
use SilvercartCurrency as Currency;
use SilvercartCustomer as Customer;
use SilvercartTools as Tools;

/**
 * Extension for SiteConfig.
 * 
 * @package SilverCart
 * @subpackage Currencies\Extensions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 14.12.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
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
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.12.2018
     */
    public function updateDefaultCurrency(&$defaultCurrency)
    {
        $currency = self::getCurrentCurrency();
        if ($currency instanceof $currency) {
            $defaultCurrency = $currency->Currency;
        }
    }
    
    /**
     * Returns thr current currency.
     * 
     * @return Currency
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.12.2018
     */
    public static function getCurrentCurrency()
    {
        if (Tools::isBackendEnvironment()) {
            $currency = Currency::getDefault();
        } else {
            $currency        = null;
            $sessionCurrency = Session::get(self::SESSION_KEY_CURRENCY);
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
     * Alisas for self::getCurrentCurrency().
     * 
     * @return Currency
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.12.2018
     */
    public static function CurrentCurrency()
    {
        return self::getCurrentCurrency();
    }
}