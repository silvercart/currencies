<?php

namespace SilverCart\Currencies\Extensions;

use SilverCart\Admin\Model\Config as SilverCartConfig;
use SilverCart\Currencies\Model\Currency;
use SilverCart\Model\Product\Product;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Member;

/**
 * Extension for SilverCart Product.
 * 
 * @package SilverCart
 * @subpackage Currencies\Extensions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.01.2019
 * @copyright 2019 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property \SilverCart\Model\Product\Product $owner Owner
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
            $displayCurrency = SilverCartConfig::DefaultCurrency();
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
     */
    public function updateGraduatedPriceForCustomersGroups(ArrayList &$graduatedPricesForMembersGroups, Member $member = null, int $quantity = 1) : void
    {
        $defaultCurrency = Currency::getDefault();
        $displayCurrency = SilverCartConfig::DefaultCurrency();
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
     * @param ArrayList &$graduatedPricesForMembersGroups Graduated prices
     * @param Member    $member                           Member context
     * 
     * @return void
     */
    public function updateGraduatedPricesForCustomersGroups(ArrayList &$graduatedPricesForMembersGroups, Member $member = null) : void
    {
        $defaultCurrency = Currency::getDefault();
        $displayCurrency = SilverCartConfig::DefaultCurrency();
        if (!$graduatedPricesForMembersGroups->exists()
         && $defaultCurrency->Currency != $displayCurrency
        ) {
            $this->owner->setDisplayCurrency($defaultCurrency->Currency);
            $graduatedPricesForMembersGroups = $this->owner->getGraduatedPricesForCustomersGroups();
        }
    }
}