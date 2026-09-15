<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_order_can_be_created_for_an_active_event(): void
    {
        $event = Event::factory()->create([
            'name' => 'Evento de prueba',
            'price' => 5000,
        ]);

        $response = $this->postJson('/api/orders', [
            'event_id' => $event->id,
            'name' => 'Rafael Bermeo',
            'email' => 'rafael@example.com',
            'phone' => '0999999999',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Orden creada correctamente')
            ->assertJsonPath('order.event', 'Evento de prueba')
            ->assertJsonPath('order.amount', 5000)
            ->assertJsonPath('order.status', 'pending')
            ->assertJsonPath('order.client_transaction_id', fn (string $value): bool => preg_match('/^EVT-[A-Z0-9]{12}$/', $value) === 1);

        $this->assertDatabaseHas('orders', [
            'event_id' => $event->id,
            'customer_name' => 'Rafael Bermeo',
            'customer_email' => 'rafael@example.com',
            'amount' => 5000,
            'status' => 'pending',
        ]);
    }

    public function test_an_order_is_rejected_when_email_is_invalid(): void
    {
        $event = Event::factory()->create();

        $this->postJson('/api/orders', [
            'event_id' => $event->id,
            'name' => 'Rafael Bermeo',
            'email' => 'correo-invalido',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_an_order_is_rejected_when_event_does_not_exist(): void
    {
        $this->postJson('/api/orders', [
            'event_id' => 999999,
            'name' => 'Rafael Bermeo',
            'email' => 'rafael@example.com',
        ])->assertUnprocessable()->assertJsonValidationErrors('event_id');
    }

    public function test_an_order_is_rejected_when_event_is_inactive(): void
    {
        $event = Event::factory()->inactive()->create();

        $this->postJson('/api/orders', [
            'event_id' => $event->id,
            'name' => 'Rafael Bermeo',
            'email' => 'rafael@example.com',
        ])->assertUnprocessable()->assertJsonValidationErrors('event_id');
    }

    public function test_order_amount_is_always_taken_from_the_event(): void
    {
        $event = Event::factory()->create(['price' => 5000]);

        $this->postJson('/api/orders', [
            'event_id' => $event->id,
            'name' => 'Rafael Bermeo',
            'email' => 'rafael@example.com',
            'amount' => 1,
        ])->assertCreated()->assertJsonPath('order.amount', 5000);

        $this->assertDatabaseHas('orders', [
            'event_id' => $event->id,
            'amount' => 5000,
        ]);
    }

    public function test_an_active_event_can_be_retrieved_by_slug(): void
    {
        $event = Event::factory()->create([
            'name' => 'Evento de prueba',
            'slug' => 'evento-prueba',
            'description' => 'Evento inicial para probar el sistema',
            'price' => 5000,
        ]);

        $this->getJson('/api/events/evento-prueba')
            ->assertOk()
            ->assertExactJson([
                'id' => $event->id,
                'name' => 'Evento de prueba',
                'slug' => 'evento-prueba',
                'description' => 'Evento inicial para probar el sistema',
                'price' => 5000,
                'active' => true,
            ]);
    }

    public function test_an_inactive_event_returns_not_found(): void
    {
        Event::factory()->inactive()->create(['slug' => 'evento-inactivo']);

        $this->getJson('/api/events/evento-inactivo')->assertNotFound();
    }
}
