<?php

namespace App\Service;

use App\Enum\PaymentMethodType;

class StripePaymentProcessor implements PaymentProcessorInterface
{
    /**
     * @param PaymentMethodType $paymentMethod
     * @return bool
     */
    public function supports(PaymentMethodType $paymentMethod): bool
    {
        return $paymentMethod === PaymentMethodType::STRIPE;
    }

    /**
     * @param PaymentMethodType $paymentMethod
     * @param float $amount
     * @return string
     */
    public function charge(PaymentMethodType $paymentMethod, float $amount)
    {
        // TODO: Implement Stripe API for charging the given amount

        return 'OK';
    }
}
