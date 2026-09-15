<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Event;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $event = Event::query()->active()->findOrFail($validated['event_id']);

        $order = DB::transaction(function () use ($event, $validated): Order {
            return Order::query()->create([
                'uuid' => (string) Str::uuid(),
                'event_id' => $event->id,
                'customer_name' => $validated['name'],
                'customer_email' => $validated['email'],
                'customer_phone' => $validated['phone'] ?? null,
                'amount' => $event->price,
                'status' => 'pending',
                'client_transaction_id' => 'EVT-'.Str::upper(Str::random(12)),
            ]);
        });

        return response()->json([
            'message' => 'Orden creada correctamente',
            'order' => [
                'uuid' => $order->uuid,
                'event' => $event->name,
                'amount' => $order->amount,
                'status' => $order->status,
                'client_transaction_id' => $order->client_transaction_id,
            ],
        ], 201);
    }
}
