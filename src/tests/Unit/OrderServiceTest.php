<?php declare(strict_types=1);

namespace App\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use App\Services\OrderService;
use App\Tests\Repositories\OrderTestRepository;

final class OrderServiceTest extends \TestCase
{
    private $orderService;
    private $mockHandler;

    public function setUp()
    {
        $this->mockHandler = new MockHandler();

        $client = new Client([
            'handler' => $this->mockHandler,
        ]);
        $orderRepo = new OrderTestRepository();

        $this->orderService = new OrderService($client, $orderRepo);
    }

    public function testShouldCreateOrder()
    {
        $parameters = [
            'origin' => ['6.433738', '3.432619'],
            'destination' => ['6.451487', '3.428404']
        ];
        $this->mockHandler->append(new Response(200, [],
            file_get_contents(__DIR__ . '/../Fixtures/distance_response.json')));

        $order = $this->orderService->createOrder($parameters['origin'], $parameters['destination']);

        $this->assertArrayHasKey('id', $order);
        $this->assertArrayHasKey('distance', $order);
        $this->assertArrayHasKey('status', $order);

        $this->assertEquals(5560, $order['distance']);
        $this->assertEquals('unassigned', $order['status']);
    }

    public function testShouldEditOrder()
    {
        $orderId = '1';
        $parameters = [ 'status' => 'taken' ];
        $updated = $this->orderService->editOrder($orderId, $parameters['status']);

        $this->assertTrue($updated);
    }

    public function testShouldListOrders()
    {
        $orders = $this->orderService->listOrders(1, 10);
        $this->assertCount(2, $orders);
    }
}