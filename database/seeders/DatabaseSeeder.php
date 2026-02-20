<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Offer;
use App\Models\BrandingSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminPermissionSeeder::class);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'phone' => '+1234567890',
        ]);

        if (Schema::hasTable('model_has_roles')) {
            $admin->assignRole('admin');
        }

        // Create sample offers
        $offers = [
            [
                'title' => '1-Hour Consultation',
                'description' => 'Professional one-on-one consultation session to discuss your needs and provide expert guidance. Perfect for getting started or addressing specific questions.',
                'price' => 99.00,
                'duration_minutes' => 60,
                'category' => 'Consultation',
                'is_active' => true,
                'max_bookings_per_day' => 5,
            ],
            [
                'title' => '30-Minute Quick Check-In',
                'description' => 'A brief session to review progress, answer quick questions, or provide updates. Ideal for ongoing clients who need regular touchpoints.',
                'price' => 49.00,
                'duration_minutes' => 30,
                'category' => 'Check-in',
                'is_active' => true,
                'max_bookings_per_day' => 10,
            ],
            [
                'title' => '2-Hour Deep Dive Session',
                'description' => 'Comprehensive session for in-depth analysis, strategy development, or intensive training. Includes follow-up notes and action items.',
                'price' => 199.00,
                'duration_minutes' => 120,
                'category' => 'Consultation',
                'is_active' => true,
                'max_bookings_per_day' => 3,
            ],
            [
                'title' => 'Group Workshop (Max 5 People)',
                'description' => 'Interactive group session covering essential topics and best practices. Great for teams or small groups looking to learn together.',
                'price' => 299.00,
                'duration_minutes' => 90,
                'category' => 'Workshop',
                'is_active' => true,
                'max_bookings_per_day' => 2,
            ],
            [
                'title' => 'Follow-Up Session',
                'description' => 'Continuing session for existing clients to review implementation, address challenges, and refine strategies.',
                'price' => 79.00,
                'duration_minutes' => 45,
                'category' => 'Follow-up',
                'is_active' => true,
                'max_bookings_per_day' => 6,
            ],
        ];

        foreach ($offers as $offerData) {
            Offer::create($offerData);
        }

        // Branding defaults
        BrandingSetting::upsertValue('hero_name', 'Dr Lawrence Amoah');
        BrandingSetting::upsertValue('hero_title', 'Coaching & Teaching Creators & Entrepreneurs to Make MONEY Online');
        BrandingSetting::upsertValue('hero_image', null);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin Email: admin@example.com');
        $this->command->info('Admin Password: password123');
    }
}
