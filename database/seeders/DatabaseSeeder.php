<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(NotificationSeeder::class);
        $this->call([
            CategorySeeder::class,
            AttractionSeeder::class,
            ObjectTypeSeeder::class,
            NoclegSeeder::class,
            RatingSeeder::class,
            BannedWordsSeeder::class,
        ]);

    }
}
