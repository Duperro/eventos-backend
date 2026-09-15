<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    public function show(string $slug): JsonResponse
    {
        $event = Event::query()->active()->where('slug', $slug)->firstOrFail();

        return response()->json([
            'id' => $event->id,
            'name' => $event->name,
            'slug' => $event->slug,
            'description' => $event->description,
            'price' => $event->price,
            'active' => $event->active,
        ]);
    }
}
