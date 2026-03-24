<?php

namespace App\Tests\Controller;

use App\Entity\Coupon;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PurchaseControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $em;
    private Product $product;
    private Product $expensiveProduct;
    private Coupon $coupon;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);

        $this->product = (new Product())->setName('Test Product')->setPrice('100.00');
        $this->expensiveProduct = (new Product())->setName('Expensive Product')->setPrice('900.00');
        $this->coupon = (new Coupon())->setCode('TEST10P')->setType('P')->setValue(10);

        $this->em->persist($this->product);
        $this->em->persist($this->expensiveProduct);
        $this->em->persist($this->coupon);
        $this->em->flush();
    }

    protected function tearDown(): void
    {
        $this->em->remove($this->em->find(Product::class, $this->product->getId()));
        $this->em->remove($this->em->find(Product::class, $this->expensiveProduct->getId()));
        $this->em->remove($this->em->find(Coupon::class, $this->coupon->getId()));
        $this->em->flush();

        parent::tearDown();
    }

    public function testSuccessfulPurchaseWithPaypal(): void
    {
        // 100 * 1.19 = 119 * 100 = 11900 < 100000
        $this->request([
            'product' => $this->product->getId(),
            'taxNumber' => 'DE123456789',
            'paymentProcessor' => 'paypal',
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(['message' => 'Payment successful'], $this->json());
    }

    public function testSuccessfulPurchaseWithStripe(): void
    {
        // 100 * 1.22 = 122 > 100
        $this->request([
            'product' => $this->product->getId(),
            'taxNumber' => 'IT12345678900',
            'paymentProcessor' => 'stripe',
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(['message' => 'Payment successful'], $this->json());
    }

    public function testSuccessfulPurchaseWithCoupon(): void
    {
        // 100 - 10% = 90 * 1.2 = 108
        $this->request([
            'product' => $this->product->getId(),
            'taxNumber' => 'FRAA123456789',
            'couponCode' => 'TEST10P',
            'paymentProcessor' => 'stripe',
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    public function testPaypalFailurePriceTooHigh(): void
    {
        // 900 * 1.24 = 1116 * 100 = 111600 > 100000
        $this->request([
            'product' => $this->expensiveProduct->getId(),
            'taxNumber' => 'GR123456789',
            'paymentProcessor' => 'paypal',
        ]);

        $this->assertSame(400, $this->client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('payment', $this->json()['errors']);
    }

    public function testStripeFailurePriceTooLow(): void
    {
        $cheap = (new Product())->setName('Cheap')->setPrice('10.00');
        $this->em->persist($cheap);
        $this->em->flush();

        // 10 * 1.19 = 11.9 < 100
        $this->request([
            'product' => $cheap->getId(),
            'taxNumber' => 'DE123456789',
            'paymentProcessor' => 'stripe',
        ]);

        $this->em->remove($cheap);
        $this->em->flush();

        $this->assertSame(400, $this->client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('payment', $this->json()['errors']);
    }

    public function testValidationMissingFields(): void
    {
        $this->request([]);

        $this->assertSame(422, $this->client->getResponse()->getStatusCode());
        $errors = $this->json()['errors'];
        $this->assertArrayHasKey('product', $errors);
        $this->assertArrayHasKey('taxNumber', $errors);
        $this->assertArrayHasKey('paymentProcessor', $errors);
    }

    public function testValidationInvalidTaxNumber(): void
    {
        $this->request([
            'product' => $this->product->getId(),
            'taxNumber' => 'INVALID',
            'paymentProcessor' => 'paypal',
        ]);

        $this->assertSame(422, $this->client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('taxNumber', $this->json()['errors']);
    }

    public function testValidationUnknownProduct(): void
    {
        $this->request([
            'product' => 999999,
            'taxNumber' => 'DE123456789',
            'paymentProcessor' => 'paypal',
        ]);

        $this->assertSame(422, $this->client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('product', $this->json()['errors']);
    }

    public function testValidationUnknownCoupon(): void
    {
        $this->request([
            'product' => $this->product->getId(),
            'taxNumber' => 'DE123456789',
            'couponCode' => 'NOTEXIST',
            'paymentProcessor' => 'paypal',
        ]);

        $this->assertSame(422, $this->client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('couponCode', $this->json()['errors']);
    }

    public function testValidationUnknownPaymentProcessor(): void
    {
        $this->request([
            'product' => $this->product->getId(),
            'taxNumber' => 'DE123456789',
            'paymentProcessor' => 'bitcoin',
        ]);

        $this->assertSame(422, $this->client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('paymentProcessor', $this->json()['errors']);
    }

    private function request(array $body): void
    {
        $this->client->request(
            'POST',
            '/purchase',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($body),
        );
    }

    private function json(): array
    {
        return json_decode($this->client->getResponse()->getContent(), true);
    }
}
