<?php declare(strict_types=1);

namespace App\Interfaces;

interface OrderRepositoryInterface
{

    public function createOrder(array $order) : array;

    public function editOrder(string $orderId, array $params) : bool;

    public function listOrders(int $page, int $limit) : array;

}