<?php

namespace App\Controller\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\OrderStateProcessor;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as OrderAssert;
use App\Enum\PaymentMethodType;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/calculate-price',
            openapiContext: [
                'description' => 'Calculate Price',
                'summary' => 'for calculation of price'
            ],
            shortName: 'Calculate',
            normalizationContext: ["groups" => ["calculate:read"]],
            denormalizationContext: ["groups" => ["calculate:write"]],
            name: 'calculate'
        ),
        new Post(
            uriTemplate: '/purchase',
            openapiContext: [
                'description' => 'Make Purchase',
                'summary' => 'for Making a Purchase'
            ],
            shortName: 'Purchase',
            normalizationContext: ["groups" => ["purchase:read"]],
            denormalizationContext: ["groups" => ["purchase:write"]],
            name: 'purchase'
        )
    ],
    processor: OrderStateProcessor::class
)]


class Order
{
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Groups(
        [
            'purchase:write',
            'purchase:read',
            'calculate:write',
            'calculate:read'
        ]
    )]
    public int $product;

    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[OrderAssert\TaxNumber()]
    #[Groups(
        [
            'purchase:write',
            'purchase:read',
            'calculate:write',
            'calculate:read'
        ]
    )]

    public string $taxNumber;

    #[Groups(
        [
            'purchase:write',
            'purchase:read',
            'calculate:write',
            'calculate:read'
        ]
    )]
    public string $couponCode;

    #[Assert\NotBlank(groups: ['purchase:write', 'purchase:read'])]
    #[Assert\NotNull(groups: ['purchase:write', 'purchase:read'])]
    #[Groups(
        [
            'purchase:write',
            'purchase:read'
        ]
    )]
    /**
     * @PaymentMethodType
     */
    public PaymentMethodType $paymentProcessor;
}
