<?php

namespace App\Service;

use App\Enum\PaymentMethodType;

class PaymentManager
{
    /**
     * @param PaymentProcessorInterface[] $paymentProcessors
     */
    public function __construct(
        private $paymentProcessors
    ) {
    }

    /**
     * @throws UnsupportedPaymentMethodException
     */
    public function charge(PaymentMethodType $paymentMethod, float $amount)
    {
        foreach ($this->paymentProcessors as $paymentProcessor) {
            if ($paymentProcessor->supports($paymentMethod)) {
                return $paymentProcessor->charge($paymentMethod, $amount);
            }
        }

        throw new UnsupportedPaymentMethodException(sprintf('No payment processor support payment method of type "%s"', $paymentMethod->value));
    }
}
