<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class TaxNumber extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'The value "{{ value }}" is not valid.';

    // We only have 4 countries for now
    public $countries = [
        'DE' => 'DEXXXXXXXXX', //Germany
        'IT' => 'ITXXXXXXXXXXX', //Italy
        'GR' => 'GRXXXXXXXXX', //Greece
        'FR' => 'FRYYXXXXXXXXX', //France
    ];
}
