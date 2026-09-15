<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'event_id' => Event::factory(),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->unique()->safeEmail(),
            'customer_phone' => fake()->optional()->numerify('09########'),
            'amount' => fake()->numberBetween(100, 100000),
            'status' => 'pending',
            'client_transaction_id' => 'EVT-'.Str::upper(Str::random(12)),
            'payment_url' => null,
            'paid_at' => null,
        ];
    }
}
