<?php declare(strict_types=1);

namespace App\Tests\Repositories;

use App\Interfaces\OrderRepositoryInterface;

final class OrderTestRepository implements OrderRepositoryInterface
{
    private $orders;

    public function __construct()
    {
        $this->orders = [
            [
                'id' => 1,
                'distance' => 5104,
                "status" => "unassigned",
                "created_at" => "2018-10-01 11:23:23",
                "updated_at" => "2018-10-01 22:33:16"
            ],
            [
                'id' => 2,
                'distance' => 1823,
                "status" => "unassigned",
                "created_at" => "2018-10-01 11:50:24",
                "updated_at" => "2018-10-01 11:50:24"
            ],
        ];
    }

    public function createOrder(array $order): array
    {
        $nextId = count($this->orders) + 1;
        $order = [
            'id' => $nextId,
            'distance' => $order['distance'],
            'status' => 'unassigned',
            'created_at' => date('Y-d-m h:i:s', time()),
            'updated_at' => date('Y-d-m h:i:s', time()),
        ];
        $this->orders[] = $order;
        return $order;
    }

    public function editOrder(string $orderId, array $params): bool
    {
        for ($i = 0; $i < count($this->orders); $i++) {
            if ($orderId == $this->orders[$i]['id']) {
                foreach ($params as $key => $value) {
                    $this->orders[$i][$key] = $value;
                }
            }
        }

        return true;
    }

    public function listOrders(int $page, int $limit): array
    {
        return $this->orders;
    }
}