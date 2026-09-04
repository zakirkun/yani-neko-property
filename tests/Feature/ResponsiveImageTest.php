<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResponsiveImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_listing_and_agent_images_use_native_lazy_loading_hints(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('properties/listing.jpg', 'image');
        Storage::disk('public')->put('agents/alex.jpg', 'image');

        $agent = Agent::factory()->create(['avatar' => 'agents/alex.jpg']);
        $property = Property::factory()->published()->featured()->for($agent)->create();
        $property->images()->create([
            'path' => 'properties/listing.jpg',
            'alt_text' => 'Sunny living room',
            'is_primary' => true,
        ]);

        $this->get(route('home'))->assertOk()
            ->assertSee('loading="lazy"', false)
            ->assertSee('decoding="async"', false)
            ->assertSee('sizes="(max-width: 640px) 100vw, (max-width: 960px) 50vw, 33vw"', false);

        $this->get(route('agents.index'))->assertOk()
            ->assertSee('loading="lazy"', false)
            ->assertSee('decoding="async"', false)
            ->assertSee('sizes="(max-width: 640px) 100vw, 190px"', false);
    }

    public function test_property_gallery_prioritizes_only_the_lead_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('properties/front.jpg', 'image');
        Storage::disk('public')->put('properties/kitchen.jpg', 'image');

        $property = Property::factory()->published()->create();
        $property->images()->createMany([
            ['path' => 'properties/front.jpg', 'sort_order' => 1, 'is_primary' => true],
            ['path' => 'properties/kitchen.jpg', 'sort_order' => 2],
        ]);

        $response = $this->get(route('properties.show', $property->slug))->assertOk();

        $response->assertSee('loading="eager"', false)
            ->assertSee('fetchpriority="high"', false)
            ->assertSee('loading="lazy"', false)
            ->assertSee('sizes="(max-width: 640px) 100vw, (max-width: 960px) 66vw, 50vw"', false);
    }
}
