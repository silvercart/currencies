<?php

namespace SilverCart\Currencies\Extensions;

use ArrayList;
use DataExtension;
use Member;
use SilvercartConfig as Config;
use SilvercartCurrency as Currency;
use SIlvercartProduct as Product;

/**
 * Extension for SilverCart Product.
 * 
 * @package SilverCart
 * @subpackage Currencies\Extensions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.01.2019
 * @copyright 2019 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductExtension extends DataExtension
{
    /**
     * List of display currencies pre extended object (owner ID)
     *
     * @var string[]
     */
    protected $displayCurrency = [];
    
    /**
     * SEts the display currency.
     * 
     * @param string $currency Currency
     * 
     * @return Product
     */
    public function setDisplayCurrency(string $currency) : Product
    {
        $this->displayCurrency[$this->owner->ID] = $currency;
        return $this->owner;
    }
    
    /**
     * Returns the display currency.
     * 
     * @return string
     */
    public function getDisplayCurrency() : string
    {
        if (!array_key_exists($this->owner->ID, $this->displayCurrency)) {
            $displayCurrency = Config::DefaultCurrency();
            $this->setDisplayCurrency($displayCurrency);
        }
        return $this->displayCurrency[$this->owner->ID];
    }
    
    /**
     * Updates the graduated price filter to add currency support.
     * 
     * @param array &$filter Filter to update
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.01.2019
     */
    public function updateGraduatedPriceFilter(array &$filter) : void
    {
        $filter['priceCurrency'] = $this->getDisplayCurrency();
    }
    
    /**
     * Updates the graduated price filter to add currency support.
     * 
     * @param array &$filter Filter to update
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.01.2019
     */
    public function updateGraduatedPricesFilter(array &$filter) : void
    {
        $this->updateGraduatedPriceFilter($filter);
    }
    
    /**
     * Adds support for the customer rebate group to graduated prices.
     * 
     * @param ArrayList $graduatedPricesForMembersGroups Graduated prices
     * @param Member    $member                          Member context
     * @param int       $quantity                        Quantity
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.01.2019
     */
    public function updateGraduatedPriceForCustomersGroups(ArrayList &$graduatedPricesForMembersGroups, Member $member = null, int $quantity = 1) : void
    {
        $defaultCurrency = Currency::getDefault();
        $displayCurrency = Config::DefaultCurrency();
        if (!$graduatedPricesForMembersGroups->exists()
         && $defaultCurrency->Currency != $displayCurrency
        ) {
            $this->owner->setDisplayCurrency($defaultCurrency->Currency);
            $graduatedPrice = $this->owner->getGraduatedPriceForCustomersGroups();
            if ($graduatedPrice !== false) {
                $graduatedPricesForMembersGroups = ArrayList::create([
                    $graduatedPrice,
                ]);
            }
        }
    }
    
    /**
     * Adds support for the customer rebate group to graduated prices.
     * 
     * @param ArrayList $graduatedPricesForMembersGroups Graduated prices
     * @param Member    $member                          Member context
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.01.2019
     */
    public function updateGraduatedPricesForCustomersGroups(ArrayList $graduatedPricesForMembersGroups, Member $member = null) : void
    {
        $defaultCurrency = Currency::getDefault();
        $displayCurrency = Config::DefaultCurrency();
        if (!$graduatedPricesForMembersGroups->exists()
         && $defaultCurrency->Currency != $displayCurrency
        ) {
            $this->owner->setDisplayCurrency($defaultCurrency->Currency);
            $graduatedPricesForMembersGroups = $this->owner->getGraduatedPricesForCustomersGroups();
        }
    }
}