<?php

namespace App\Services;

use App\Models\Order;

class OrderService
{
    public function getAllOrders()
    {
        return Order::all();
    }

    public function findOrderById($id)
    {
        return Order::findOrFail($id);
    }

    public function createOrder(array $data)
    {
        return Order::create($data);
    }

    public function updateOrder($id, array $data)
    {
        $order = $this->findOrderById($id);
        $order->update($data);

        return $order;
    }

    public function deleteOrder($id)
    {
        $order = $this->findOrderById($id);
        $order->delete();
    }
}
