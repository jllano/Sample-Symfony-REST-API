<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\DataFixtures\AppFixtures;
use App\Entity\Coupon;
use App\Entity\Product;
use App\Tests\Trait\FixturesTrait;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Symfony\Component\HttpFoundation\Response;

class OrderTest extends WebTestCase
{
    Const TAX_NUMBER_GERMANY = "DE237805674";
    Const TAX_NUMBER_ITALY = "IT50823556571";

    Const TAX_NUMBER_GREECE = "GR237625671";

    Const TAX_NUMBER_FRANCE = "FRGS823556571";

    use FixturesTrait;
    private ReferenceRepository $fixtures;

    public function setUp(): void
    {
        $this->fixtures = $this->loadFixtures([
            AppFixtures::class
        ])->getReferenceRepository();
    }

    public function testCalculatePrice(): void
    {
        static::ensureKernelShutdown();

        $client = static::createClient();

        /*** @var Product $iPhoneProduct */
        $iPhoneProduct = $this->fixtures->getReference('iPhone');

        $payload = [
            'product' => $iPhoneProduct->getId(),
            'taxNumber' => ''
        ];

        $client->request(
            'POST',
            '/calculate-price',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = $client->getResponse();

        // 422 - taxNumber is required
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());

        $payload = [
            'product' => $iPhoneProduct->getId(),
            'taxNumber' => '0INVALID01',
        ];

        $client->request(
            'POST',
            '/calculate-price',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = $client->getResponse();
        // 422 - taxNumber is not valid
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());

        $payload = [
            'product' => 0,
            'taxNumber' => self::TAX_NUMBER_GERMANY
        ];

        $client->request(
            'POST',
            '/calculate-price',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = $client->getResponse();

        // 404 - Product not found
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $payload = [
            'product' => $iPhoneProduct->getId(),
            'taxNumber' => self::TAX_NUMBER_GERMANY
        ];

        $client->request(
            'POST',
            '/calculate-price',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = $client->getResponse();

        // Ok - Test Passed
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, $response->getStatusCode());

        /*** @var Product $caseProduct */
        $caseProduct = $this->fixtures->getReference('case');

        $payload = [
            'product' => $caseProduct->getId(),
            'taxNumber' => self::TAX_NUMBER_FRANCE
        ];

        $client->request(
            'POST',
            '/calculate-price',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = $client->getResponse();

        // Ok - Test Passed
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testPurchase(): void
    {
        static::ensureKernelShutdown();

        $client = static::createClient();

        /*** @var Product $caseProduct */
        $caseProduct = $this->fixtures->getReference('case');

        $payload = [
            'product' => $caseProduct->getId(),
            'taxNumber' => self::TAX_NUMBER_ITALY,
            'couponCode' => 'INVALID-CP',
            'paymentProcessor' => 'stripe'
        ];

        $client->request(
            'POST',
            '/purchase',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = $client->getResponse();
        // 404 - Invalid Coupon/Not Found
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        /*** @var Coupon $fixedCoupon */
        $fixedCoupon = $this->fixtures->getReference('fixedCoupon');

        $payload = [
            'product' => $caseProduct->getId(),
            'taxNumber' => self::TAX_NUMBER_ITALY,
            'couponCode' => $fixedCoupon->getCode(),
            'paymentProcessor' => 'invalid-payment-processor'
        ];

        $client->request(
            'POST',
            '/purchase',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = $client->getResponse();
        // 400 - Invalid Payment Processor
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $payload = [
            'product' => $caseProduct->getId(),
            'taxNumber' => self::TAX_NUMBER_GREECE,
            'couponCode' => $fixedCoupon->getCode(),
            'paymentProcessor' => 'braintree'
        ];

        $client->request(
            'POST',
            '/purchase',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = $client->getResponse();
        // 400 - Invalid Payment Processor / Not supported yet
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $payload = [
            'product' => $caseProduct->getId(),
            'taxNumber' => self::TAX_NUMBER_GREECE,
            'couponCode' => $fixedCoupon->getCode(),
            'paymentProcessor' => 'stripe'
        ];

        $client->request(
            'POST',
            '/purchase',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = $client->getResponse();

        // Ok - Test Passed
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, $response->getStatusCode());
    }
}
