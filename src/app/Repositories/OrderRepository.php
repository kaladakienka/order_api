<?php declare(strict_types = 1);

namespace App\Repositories;

use App\Interfaces\OrderRepositoryInterface;
use Log;
use App\Models\Order;

class OrderRepository implements OrderRepositoryInterface
{
    private $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function createOrder(array $order) : array
    {
        $order = $this->order->create($order);
        return (array) $order->refresh()->getAttributes();
    }

    /**
     * @param string $orderId
     * @param array $params
     * @return bool
     * @throws \Exception
     */
    public function editOrder(string $orderId, array $params) : bool
    {
        $order = $this->order->findOrFail($orderId);

        if ($order->getAttribute('status') == Order::TAKEN) {
            throw new \Exception(Order::ORDER_ALREADY_BEEN_TAKEN, 409);
        }
        return $order->update($params);
    }

    public function listOrders(int $page, int $limit) : array
    {
        $offset = ($page - 1) * $limit;
        return $this->order->skip($offset)->take($limit)->get()->toArray();
    }
}