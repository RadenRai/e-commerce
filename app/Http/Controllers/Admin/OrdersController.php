<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\OrderService;  

class OrdersController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }


    public function index()
    {
        $orders = $this->orderService->getAllOrders();
        return view('admin.orders.index', compact('orders'));
    }


    public function create()
    {
        return view('admin.orders.create');
    }

    
    public function store(Request $request)
    {
        $validatedData = $this->validateOrder($request);

        $this->orderService->createOrder($validatedData);

        return redirect()->route('admin.orders.index')->with('success', 'Order created successfully.');
    }

    public function show(string $id)
    {
        $order = $this->orderService->findOrderById($id);

        return view('admin.orders.show', compact('order'));
    }

    public function edit(string $id)
    {
        $order = $this->orderService->findOrderById($id);

        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, string $id)
    {
        $validatedData = $this->validateOrder($request);

        $this->orderService->updateOrder($id, $validatedData);

        return redirect()->route('admin.orders.index')->with('success', 'Order updated successfully.');
    }

    public function destroy(string $id)
    {
        $this->orderService->deleteOrder($id);

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
    }
    private function validateOrder(Request $request)
    {
        return $request->validate([
            'customer_name' => 'required|string|max:255',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|in:pending,completed,cancelled',
        ]);
    }
}
