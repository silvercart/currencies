<?php

namespace SilverCart\Currencies\Extensions;

use DataExtension;

/**
 * Extension for Member.
 * 
 * @package SilverCart
 * @subpackage Currencies\Extensions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 17.12.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class MemberExtension extends DataExtension
{
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
            'DefaultCurrency' => _t(self::class . '.DefaultCurrency', 'Default Currency'),
        ]);
    }
}