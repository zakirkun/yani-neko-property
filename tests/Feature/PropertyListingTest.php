<?php

namespace Tests\Feature;

use App\Enums\PropertyPurpose;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_only_displays_published_eligible_listings(): void
    {
        $visible = Property::factory()->published()->featured()->create(['title' => 'Visible Home']);
        Property::factory()->create(['title' => 'Draft Home']);
        Property::factory()->future()->featured()->create(['title' => 'Future Home']);

        $this->get(route('home'))->assertOk()
            ->assertSee($visible->title)
            ->assertDontSee('Draft Home')
            ->assertDontSee('Future Home');
    }

    public function test_draft_and_future_listings_cannot_be_viewed(): void
    {
        $draft = Property::factory()->create();
        $future = Property::factory()->future()->create();

        $this->get(route('properties.show', $draft->slug))->assertNotFound();
        $this->get(route('properties.show', $future->slug))->assertNotFound();
    }

    public function test_listings_can_be_filtered_by_purpose_city_price_bedrooms_and_type(): void
    {
        $house = PropertyType::factory()->create(['name' => 'House', 'slug' => 'house']);
        $apartment = PropertyType::factory()->create(['name' => 'Apartment', 'slug' => 'apartment']);
        $match = Property::factory()->published()->create([
            'title' => 'Matching Residence', 'purpose' => PropertyPurpose::Sale, 'city' => 'Bristol',
            'price' => 500000, 'bedrooms' => 4, 'property_type_id' => $house->id,
        ]);
        Property::factory()->published()->create([
            'title' => 'Wrong Residence', 'purpose' => PropertyPurpose::Rent, 'city' => 'Bath',
            'price' => 2000, 'bedrooms' => 2, 'property_type_id' => $apartment->id,
        ]);

        $this->get(route('properties.index', [
            'purpose' => 'sale', 'city' => 'Bristol', 'min_price' => 450000, 'max_price' => 550000,
            'bedrooms' => 3, 'property_type' => $house->id,
        ]))->assertOk()->assertSee($match->title)->assertDontSee('Wrong Residence');
    }

    public function test_generated_property_slugs_are_unique(): void
    {
        $first = Property::factory()->create(['title' => 'A Lovely Home']);
        $second = Property::factory()->create(['title' => 'A Lovely Home']);

        $this->assertSame('a-lovely-home', $first->slug);
        $this->assertSame('a-lovely-home-2', $second->slug);
    }
}
