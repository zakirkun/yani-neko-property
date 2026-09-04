<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Models\Agent;
use App\Models\Lead;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_property_inquiry_creates_a_lead_assigned_to_the_listing_agent(): void
    {
        $agent = Agent::factory()->create();
        $property = Property::factory()->published()->for($agent)->create();
        $data = ['name' => 'Taylor Reed', 'email' => 'taylor@example.com', 'phone' => '555-0100', 'message' => 'Please arrange a viewing.'];

        $this->post(route('properties.inquiries.store', $property->slug), $data)
            ->assertRedirect(route('properties.show', $property->slug))->assertSessionHas('success');

        $lead = Lead::sole();
        $this->assertSame($property->id, $lead->property_id);
        $this->assertSame($agent->id, $lead->agent_id);
        $this->assertSame(LeadStatus::New, $lead->status);
        $this->assertSame($data['email'], $lead->email);
    }

    public function test_invalid_property_inquiry_is_rejected(): void
    {
        $property = Property::factory()->published()->create();

        $this->from(route('properties.show', $property->slug))
            ->post(route('properties.inquiries.store', $property->slug), ['name' => '', 'email' => 'not-an-email', 'message' => ''])
            ->assertRedirect(route('properties.show', $property->slug))
            ->assertSessionHasErrors(['name', 'email', 'message']);

        $this->assertDatabaseCount('leads', 0);
    }

    public function test_contact_form_creates_an_unassigned_lead(): void
    {
        $data = ['name' => 'Jordan Lee', 'email' => 'jordan@example.com', 'message' => 'I am looking for a family home.'];

        $this->post(route('contact.store'), $data)->assertRedirect(route('contact'))->assertSessionHas('success');

        $this->assertDatabaseHas('leads', [
            'name' => $data['name'], 'email' => $data['email'], 'property_id' => null,
            'agent_id' => null, 'status' => LeadStatus::New->value,
        ]);
    }
}
