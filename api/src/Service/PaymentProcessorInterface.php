<?php

namespace App\Service;

use App\Enum\PaymentMethodType;

interface PaymentProcessorInterface
{
    public function supports(PaymentMethodType $paymentMethod): bool;

    public function charge(PaymentMethodType $paymentMethod, float $amount);
}
