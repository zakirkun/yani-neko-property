<?php

namespace Database\Seeders;

use App\Enums\PropertyPurpose;
use App\Enums\PropertyStatus;
use App\Models\Agent;
use App\Models\Amenity;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $types = collect([
            'House' => 'Detached and character family homes.',
            'Apartment' => 'City apartments and contemporary residences.',
            'Townhouse' => 'Low-maintenance homes close to local amenities.',
            'Land' => 'Residential land ready for a new vision.',
        ])->mapWithKeys(function (string $description, string $name): array {
            $type = PropertyType::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'description' => $description]);

            return [$name => $type];
        });

        $amenities = collect(['Pool', 'Garage', 'Garden', 'Air conditioning', 'Balcony', 'Home office'])
            ->map(fn (string $name): Amenity => Amenity::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name]));

        $agents = collect([
            ['name' => 'Alex Morgan', 'email' => 'agent@example.com', 'phone' => '+1 555 0142'],
            ['name' => 'Sophia Bennett', 'email' => 'sophia@example.com', 'phone' => '+1 555 0188'],
        ])->map(function (array $details): Agent {
            $user = User::updateOrCreate(
                ['email' => $details['email']],
                ['name' => $details['name'], 'password' => 'password', 'email_verified_at' => now()]
            );
            $user->syncRoles('agent');

            return Agent::updateOrCreate(['user_id' => $user->id], [
                'name' => $details['name'],
                'email' => $details['email'],
                'phone' => $details['phone'],
                'bio' => 'A trusted local property advisor focused on clear guidance and attentive service.',
                'is_active' => true,
            ]);
        })->values();

        $listings = [
            ['Willow Garden House', 'House', 'sale', 785000, 'Brighton', 4, 3],
            ['Harbour View Apartment', 'Apartment', 'rent', 3200, 'Brighton', 2, 2],
            ['The Maple Townhouse', 'Townhouse', 'sale', 595000, 'Bristol', 3, 2],
            ['Clifton Park Residence', 'House', 'rent', 4100, 'Bristol', 4, 3],
            ['Riverside Loft', 'Apartment', 'sale', 445000, 'Bath', 2, 2],
            ['Lansdown Family Home', 'House', 'sale', 925000, 'Bath', 5, 3],
            ['Meadow Building Plot', 'Land', 'sale', 275000, 'Oxford', 0, 0],
            ['Summertown Apartment', 'Apartment', 'rent', 2450, 'Oxford', 2, 1],
            ['Cedar Lane Townhouse', 'Townhouse', 'rent', 2850, 'Cambridge', 3, 2],
            ['Orchard House', 'House', 'sale', 865000, 'Cambridge', 4, 3],
            ['Old Mill Courtyard', 'Townhouse', 'sale', 635000, 'York', 3, 2],
            ['Minster View Flat', 'Apartment', 'rent', 1950, 'York', 1, 1],
        ];

        foreach ($listings as $index => [$title, $type, $purpose, $price, $city, $bedrooms, $bathrooms]) {
            $property = Property::updateOrCreate(['slug' => Str::slug($title)], [
                'agent_id' => $agents[$index % $agents->count()]->id,
                'property_type_id' => $types[$type]->id,
                'title' => $title,
                'description' => 'A thoughtfully presented property combining comfortable living with excellent access to local amenities.',
                'purpose' => PropertyPurpose::from($purpose),
                'status' => PropertyStatus::Published,
                'price' => $price,
                'address' => (12 + $index).' Market Street',
                'city' => $city,
                'state' => 'England',
                'postcode' => 'AB'.($index + 1).' 2CD',
                'bedrooms' => $bedrooms,
                'bathrooms' => $bathrooms,
                'area' => $bedrooms ? 700 + ($index * 95) : 4200,
                'is_featured' => $index < 4,
                'published_at' => now()->subDays($index + 1),
            ]);
            $property->amenities()->sync($amenities->pluck('id')->slice($index % 3, 3)->values());
        }

        Property::updateOrCreate(['slug' => 'coming-soon-cottage'], [
            'agent_id' => $agents->first()->id,
            'property_type_id' => $types['House']->id,
            'title' => 'Coming Soon Cottage',
            'description' => 'A preview listing awaiting publication.',
            'purpose' => PropertyPurpose::Sale,
            'status' => PropertyStatus::Draft,
            'price' => 525000,
            'address' => '1 Haven Lane',
            'city' => 'Brighton',
            'state' => 'England',
            'postcode' => 'BN1 1AA',
            'bedrooms' => 3,
            'bathrooms' => 2,
            'area' => 1250,
            'is_featured' => false,
            'published_at' => null,
        ]);
    }
}
