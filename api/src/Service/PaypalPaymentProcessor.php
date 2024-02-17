<?php

namespace App\Service;

use App\Enum\PaymentMethodType;

class PaypalPaymentProcessor implements PaymentProcessorInterface
{
    /**
     * @param PaymentMethodType $paymentMethod
     * @return bool
     */
    public function supports(PaymentMethodType $paymentMethod): bool
    {
        return $paymentMethod === PaymentMethodType::PAYPAL;
    }

    /**
     * @param PaymentMethodType $paymentMethod
     * @param float $amount
     * @return string
     */
    public function charge(PaymentMethodType $paymentMethod, float $amount)
    {
        // TODO: Implement Paypal API for charging the given amount

        return 'OK';
    }
}
