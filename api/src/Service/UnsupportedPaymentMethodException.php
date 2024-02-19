<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UnsupportedPaymentMethodException extends BadRequestException
{
    protected $message = 'Unsupported payment method';
}
