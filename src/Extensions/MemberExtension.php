<?php

namespace SilverCart\Currencies\Extensions;

use SilverCart\Currencies\Model\Currency;
use SilverStripe\ORM\DataExtension;

/**
 * Extension for Member.
 * 
 * @package SilverCart
 * @subpackage Currencies\Extensions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 17.12.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property \SilverStripe\Security\Member $owner Owner
 */
class MemberExtension extends DataExtension
{
    /**
     * Has one relations.
     *
     * @var array
     */
    private static $has_one = [
        'DefaultCurrency' => Currency::class,
    ];
    
    /**
     * Updates the field labels.
     * 
     * @param array &$labels Labels to update.
     * 
     * @return void
     */
    public function updateFieldLabels(&$labels) : void
    {
        $labels = array_merge($labels, [
            'DefaultCurrency' => _t(self::class . '.DefaultCurrency', 'Default Currency'),
        ]);
    }
}