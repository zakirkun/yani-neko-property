<?php

namespace Tests\Feature;

use App\Enums\PropertyStatus;
use App\Models\Agent;
use App\Models\Amenity;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoDataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_repeatable_demo_data_and_accounts(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(13, Property::count());
        $this->assertSame(12, Property::where('status', PropertyStatus::Published->value)->count());
        $this->assertSame(1, Property::where('status', PropertyStatus::Draft->value)->count());
        $this->assertSame(2, Agent::count());
        $this->assertSame(6, Amenity::count());
        $this->assertGreaterThan(0, Property::has('amenities')->count());
        $this->assertSame(0, PropertyImage::count(), 'Demo listings use the supported no-image placeholders.');

        $admin = User::where('email', 'admin@example.com')->sole();
        $agent = User::where('email', 'agent@example.com')->sole();
        $this->assertTrue($admin->hasRole('super-admin'));
        $this->assertTrue($agent->hasRole('agent'));
        $this->assertTrue(Hash::check('password', $admin->password));
        $this->assertTrue(Hash::check('password', $agent->password));
    }
}
