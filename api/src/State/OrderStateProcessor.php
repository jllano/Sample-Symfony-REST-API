<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Controller\Resource\Order;
use App\Entity\Product;
use App\Entity\Coupon;
use App\Enum\CouponType;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Service\PaymentManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;

class OrderStateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly PaymentManager $paymentManager,
        private readonly ProductRepository $productRepository,
        private readonly CouponRepository $couponRepository
    ) {
    }

    /**
     * @param Order $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return mixed
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        /** @var Product $product */
        $product = $this->productRepository->find($data->product);

        if (!$product instanceof Product) {
            throw new NotFoundHttpException('Product could be not found.');
        }

        $discount = 0;

        if (isset($data->couponCode)) {
            /** @var Coupon $coupon */
            $coupon = $this->couponRepository->findOneBy(['code' => $data->couponCode]);

            if (!$coupon instanceof Coupon) {
                throw new NotFoundHttpException('Coupon could be not found.');
            }

            $discount = $coupon->getAmount();

            if ($coupon->getType() == CouponType::PERCENTAGE) {
                $discount = ($discount / 100) * $product->getPrice();
            }
        }

        $tax = $this->getTaxPercentage($data->taxNumber);
        $subTotal = $product->getPrice() - $discount;
        $total =  $subTotal + ($subTotal / 100) * $tax;

        if ($operation->getName() == 'calculate') {
            $response = ['total' => $total];
        }

        if ($operation->getName() == 'purchase') {
            $response = $this->paymentManager->charge($data->paymentProcessor,$total);
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * Takes a tax number and returns the corresponding percentage based on the country code.
     * @param string $taxNumber
     * @return int
     */
    public function getTaxPercentage(string $taxNumber)
    {
        $countryCode = substr($taxNumber, 0, 2);

        return match ($countryCode) {
            'DE' => 19,
            'IT' => 22,
            'FR' => 20,
            'GR' => 24,
            default => 0,
        };
    }


}
