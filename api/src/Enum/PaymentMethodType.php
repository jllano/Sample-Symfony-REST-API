<?php

namespace App\Enum;

enum PaymentMethodType: string
{
    case PAYPAL = 'paypal';
    case STRIPE = 'stripe';

    case BRAINTREE = 'braintree';
}
