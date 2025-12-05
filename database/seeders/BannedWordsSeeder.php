<?php

namespace Database\Seeders;

use App\Models\BannedWord;
use Illuminate\Database\Seeder;

class BannedWordsSeeder extends Seeder
{
    public function run(): void
    {
        $words = [
            ['word' => 'kurwa', 'partial' => false],
            ['word' => 'chuj', 'partial' => false],
            ['word' => 'debil', 'partial' => true],
            ['word' => 'idiota', 'partial' => true],
            ['word' => 'pierd', 'partial' => true],
            ['word' => 'kurw', 'partial' => true],
        ];

        foreach ($words as $word) {
            BannedWord::create($word);
        }
    }
}
