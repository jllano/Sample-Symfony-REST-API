<?php

namespace App\Service;

class UnsupportedPaymentMethodException extends \Exception
{
    protected $message = 'Unsupported payment method';
}
