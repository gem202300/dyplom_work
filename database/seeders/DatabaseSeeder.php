<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(NotificationSeeder::class);
        $this->call([
            CategorySeeder::class,
            AttractionSeeder::class,
            MapIconSeeder::class,
            ObjectTypeSeeder::class,
            NoclegSeeder::class,
            RatingSeeder::class,
            RatingReportSeeder::class,
            BannedWordsSeeder::class,
        ]);

    }
}
