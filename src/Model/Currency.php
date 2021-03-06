<?php

//namespace SilverCart\Currencies\Model;

use DB as DB;
use DataObject as DataObject;
use DropdownField as DropdownField;
use FieldList as FieldList;
use SilvercartConfig as Config;
use SilvercartTools as Tools;

/**
 * A currency with a calculation factor to the default currency.
 * 
 * @package SilverCart
 * @subpackage Currencies\Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.11.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartCurrency extends DataObject
{
    /**
     * DB attributes.
     *
     * @var array
     */
    private static $db = [
        'IsDefault' => 'Boolean',
        'Currency'  => 'Varchar(3)',
        'Factor'    => 'Float',
    ];
    /**
     * DB attributes.
     *
     * @var array
     */
    private static $table_name = 'SilvercartCurrency';
    
    /**
     * Returns the plural name.
     * 
     * @return string
     */
    public function plural_name()
    {
        return Tools::plural_name_for($this);
    }
    
    /**
     * Returns the singular name.
     * 
     * @return string
     */
    public function singular_name()
    {
        return Tools::singular_name_for($this);
    }
    
    /**
     * Returns the CMS fields.
     * 
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $excludeCurrencies = SilvercartCurrency::get()->map('ID', 'Currency')->toArray();
            if ($this->exists()
             && array_key_exists($this->ID, $excludeCurrencies))
            {
                unset($excludeCurrencies[$this->ID]);
            }
            $availableCurrencies = self::getCurrencyList($excludeCurrencies);
            $currencyField       = DropdownField::create('Currency', $this->fieldLabel('Currency'), $availableCurrencies, $this->Currency);
            $fields->removeByName('Currency');
            $fields->insertAfter('IsDefault', $currencyField);
            $fields->dataFieldByName('Factor')->setDescription($this->fieldLabel('FactorDesc'));
        });
        return parent::getCMSFields();
    }
    
    /**
     * Returns the field labels.
     * 
     * @param bool $includerelations Include relations?
     * 
     * @return array
     */
    public function fieldLabels($includerelations = true)
    {
        return array_merge(
                parent::fieldLabels($includerelations),
                Tools::field_labels_for(self::class)
        );
    }
    
    /**
     * Returns the summary fields.
     * 
     * @return array
     */
    public function summaryFields()
    {
        return [
            'Currency'  => $this->fieldLabel('Currency'),
            'IsDefault' => $this->fieldLabel('IsDefault'),
            'Factor'    => $this->fieldLabel('Factor'),
        ];
    }
    
    /**
     * Handles the default currency property on before write.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.11.2018
     */
    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if ($this->isChanged('IsDefault')
         && $this->IsDefault
        ) {
            $tableName = self::config()->get('table_name');
            DB::query("UPDATE {$tableName} SET IsDefault = false WHERE ID != {$this->ID}");
        }
    }
    
    /**
     * Adds the default currency if no currency exists yet.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.11.2018
     */
    public function requireDefaultRecords()
    {
        $existing = self::get();
        if ($existing->exists()) {
            return;
        }
        $currency = self::create();
        $currency->Currency  = Config::DefaultCurrency();
        $currency->IsDefault = true;
        $currency->write();
    }
    
    /**
     * Returns the Currency as title to use in CMS.
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->Currency;
    }
    
    /**
     * Returns a curency list.
     * 
     * @param array $excludeCurrencies List of currencies to exclude
     * 
     * @return array
     */
    public static function getCurrencyList($excludeCurrencies = [])
    {
        $xml          = file_get_contents( dirname(__FILE__) . '/../../xml/currencies.xml');
        $xmlAsArray   = json_decode(json_encode((array) simplexml_load_string($xml)), 1);
        $currencies   = $xmlAsArray['CcyTbl']['CcyNtry'];
        $currencyList = [];

        foreach ($currencies as $currencyData) {
            if (!array_key_exists('Ccy', $currencyData)) {
                continue;
            }
            $countryName  = $currencyData['CtryNm'];
            $currencyName = $currencyData['CcyNm'];
            $currency     = $currencyData['Ccy'];
            $currencyID   = $currencyData['CcyNbr'];
            $minorUnits   = $currencyData['CcyMnrUnts'];
            $currencyList[$currency] = "{$currency} ({$currencyName})";
        }
        
        if (count($excludeCurrencies) > 0) {
            foreach ($excludeCurrencies as $currencyToExclude) {
                if (array_key_exists($currencyToExclude, $currencyList)) {
                    unset($currencyList[$currencyToExclude]);
                }
            }
        }
        
        return $currencyList;
    }
    
    /**
     * Returns the default currency.
     * 
     * @return SilvercartCurrency
     */
    public static function getDefault()
    {
        return self::get()
            ->filter('IsDefault', true)
            ->first();
    }
    
    /**
     * Converts the given amount from $fromCurrency to $toCurrency.
     * 
     * @param float  $amount       Original amount
     * @param string $fromCurrency Original currency
     * @param string $toCurrency   Target currency
     * 
     * @return float
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.12.2018
     */
    public static function convertFromCurrency($amount, $fromCurrency, $toCurrency)
    {
        $convertedAmount = $amount;
        $defaultCurrency = self::getDefault();
        if ($fromCurrency === $defaultCurrency->Currency) {
            $targetCurrency = self::get()
                    ->filter('Currency', $toCurrency)
                    ->first();
            if ($targetCurrency instanceof SilvercartCurrency
             && $targetCurrency->exists()
            ) {
                $convertedAmount = $amount * $targetCurrency->Factor;
            }
        } elseif ($toCurrency === $defaultCurrency->Currency) {
            $originalCurrency = self::get()
                    ->filter('Currency', $fromCurrency)
                    ->first();
            if ($originalCurrency instanceof SilvercartCurrency
             && $originalCurrency->exists()
            ) {
                $convertedAmount = $amount / $originalCurrency->Factor;
            }
        } else {
            $defaultCurrencyAmount = self::convertFromCurrency($amount, $fromCurrency, $defaultCurrency->Currency);
            $convertedAmount       = self::convertFromCurrency($defaultCurrencyAmount, $defaultCurrency->Currency, $toCurrency);
        }
        return $convertedAmount;
    }
    
    /**
     * Returns the calculation factor to use to calculate the value from 
     * $fromCurrency to $toCurrency.
     * 
     * @param string $fromCurrency Currency to calculate from
     * @param string $toCurrency   Currency to calculate to
     * 
     * @return float
     */
    public static function getFactorFor($fromCurrency, $toCurrency)
    {
        $factor = 0;
        if ($fromCurrency != $toCurrency) {
            $defaultCurrency    = self::getDefault();
            $fromCurrencyObject = self::get()->filter('Currency', $fromCurrency)->first();
            $toCurrencyObject   = self::get()->filter('Currency', $toCurrency)->first();
            if ($defaultCurrency->Currency == $toCurrency) {
                $factor = 1 / $fromCurrencyObject->Factor;
            } elseif ($defaultCurrency->Currency == $fromCurrency) {
                $factor = $toCurrencyObject->Factor;
            } else {
                $factor =  $toCurrencyObject->Factor / $fromCurrencyObject->Factor;
            }
        }
        return $factor;
    }

    /**
     * Returns the current exchange rate from $fromCurrency to $toCurrency using
     * the free API currencyconverterapi.com.
     * 
     * @param string $fromCurrency From currency
     * @param string $toCurrency   To currency
     * 
     * @return float
     */
    public static function getCurrentExchangeRate($fromCurrency, $toCurrency) : float
    {
        return self::convertAmountWithCurrentExchangeRate($fromCurrency, $toCurrency);
    }

    /**
     * Returns the converted amount using the current exchange rate from 
     * $fromCurrency to $toCurrency using the free API currencyconverterapi.com.
     * 
     * @param string $fromCurrency From currency
     * @param string $toCurrency   To currency
     * @param float  $amount       Amount to convert
     * 
     * @return float
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.12.2018
     */
    public static function convertAmountWithCurrentExchangeRate($fromCurrency, $toCurrency, $amount = 1) : float
    {
        $factor   = 0;
        $property = "{$fromCurrency}_{$toCurrency}";
        $json     = file_get_contents("https://free.currencyconverterapi.com/api/v6/convert?q={$property}&compact=ultra");
        $object   = json_decode($json);
        if (property_exists($object, $property)) {
            $factor = $object->{$property};
        }
        return $factor * $amount;
    }
}