<?php

namespace SilverCart\Currencies\Tasks;

use SilvercartCurrency as Currency;
use BuildTask;
use SS_HTTPRequest as HTTPRequest;

/**
 * Task to update the currency exchange rates.
 * 
 * @package SilverCart
 * @subpackage Currencies\Tasks
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 20.12.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ExchangeRateTask extends BuildTask
{
    /**
     * Set a custom url segment (to follow dev/tasks/)
     *
     * @config
     * @var string
     */
    private static $segment = 'currency-exchange-rate-task';

    /**
     * @var string $title Shown in the overview on the {@link TaskRunner}
     * HTML or CLI interface. Should be short and concise, no HTML allowed.
     */
    protected $title = 'Update Currency Exchange Rate Task';

    /**
     * @var string $description Describe the implications the task has,
     * and the changes it makes. Accepts HTML formatting.
     */
    protected $description = 'Updates the currency exchange rate factors for all non-default currencies using the free API currencyconverterapi.com.';

    /**
     * Runs the task.
     *
     * @param HTTPRequest $request HTTP Request
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.12.2018
     */
    public function run($request) : void
    {
        $defaultCurrency      = Currency::getDefault();
        $nonDefaultCurrencies = Currency::get()
                ->filter('IsDefault', false);
        foreach ($nonDefaultCurrencies as $nonDefaultCurrency) {
            /* @var $nonDefaultCurrency Currency */
            $currentFactor = Currency::getCurrentExchangeRate($defaultCurrency->Currency, $nonDefaultCurrency->Currency);
            if ($nonDefaultCurrency->Factor != $currentFactor) {
                $nonDefaultCurrency->Factor = $currentFactor;
                $nonDefaultCurrency->write();
            }
        }
    }
}