<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::query()->updateOrCreate(
            ['slug' => 'evento-prueba'],
            [
                'name' => 'Evento de prueba',
                'description' => 'Evento inicial para probar el sistema',
                'price' => 5000,
                'active' => true,
                'whatsapp_url' => null,
            ],
        );
    }
}
