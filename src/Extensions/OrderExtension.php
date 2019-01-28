<?php

namespace SilverCart\Currencies\Extensions;

use DataExtension;
use SilvercartCurrency as Currency;

/**
 * Extension for SilverCart Order.
 * 
 * @package SilverCart
 * @subpackage Currencies\Extensions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 17.12.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class OrderExtension extends DataExtension
{
    /**
     * DB attributes.
     *
     * @var array
     */
    private static $db = [
        'DefaultCurrencyFactor' => 'Float',
    ];
    /**
     * Has one relations.
     *
     * @var array
     */
    private static $has_one = [
        'DefaultCurrency' => 'SilvercartCurrency',
    ];
    
    /**
     * Updates the field labels.
     * 
     * @param array &$labels Labels to update.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.12.2018
     */
    public function updateFieldLabels(&$labels)
    {
        $labels = array_merge($labels, [
            'DefaultCurrency'           => _t(self::class . '.DefaultCurrency', 'Default Currency'),
            'DefaultCurrencyFactor'     => _t(self::class . '.DefaultCurrencyFactor', 'Default Currency Factor'),
            'DefaultCurrencyFactorDesc' => _t(self::class . '.DefaultCurrencyFactorDesc', 'Default currency factor at the time the order was placed.'),
        ]);
    }
    
    /**
     * Adds the default currency and the calculation factor to the order.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.12.2018
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if (empty($this->DefaultCurrencyID)) {
            $defaultCurrency = Currency::getDefault();
            if ($defaultCurrency instanceof Currency
             && $defaultCurrency->exists()
            ) {
                $this->DefaultCurrencyID     = $defaultCurrency->ID;
                $this->DefaultCurrencyFactor = Currency::getFactorFor($this->owner->AmountTotal->getCurrency(), $defaultCurrency->Currency);
            }
        }
    }
}