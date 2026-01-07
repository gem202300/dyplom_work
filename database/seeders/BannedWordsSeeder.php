<?php

namespace Database\Seeders;

use App\Models\BannedWord;
use Illuminate\Database\Seeder;

class BannedWordsSeeder extends Seeder
{
    public function run(): void
    {
        $words = [
            // Основні вульгаризми (точні)
            ['word' => 'kurwa', 'partial' => false],
            ['word' => 'chuj', 'partial' => false],
            ['word' => 'pizda', 'partial' => false],
            ['word' => 'jebać', 'partial' => false],
            ['word' => 'pierdolić', 'partial' => false],

            ['word' => 'kurw', 'partial' => true],      // ловить kurwa, skurwysyn, kurwić тощо
            ['word' => 'chuj', 'partial' => true],
            ['word' => 'huj', 'partial' => true],
            ['word' => 'pizd', 'partial' => true],
            ['word' => 'jeb', 'partial' => true],
            ['word' => 'pierdol', 'partial' => true],
            ['word' => 'pierd', 'partial' => true],

            ['word' => 'skurwysyn', 'partial' => true],
            ['word' => 'skurwiel', 'partial' => true],
            ['word' => 'sukinsyn', 'partial' => true],
            ['word' => 'cipa', 'partial' => true],
            ['word' => 'cipę', 'partial' => true],
            ['word' => 'dupa', 'partial' => true],     
            ['word' => 'gówno', 'partial' => true],
            ['word' => 'gowno', 'partial' => true],
            ['word' => 'spierdalaj', 'partial' => true],
            ['word' => 'wypierdalaj', 'partial' => true],
            ['word' => 'debil', 'partial' => true],
            ['word' => 'idiota', 'partial' => true],
            ['word' => 'debilek', 'partial' => true],
            ['word' => 'pedał', 'partial' => true],    
            ['word' => 'ciota', 'partial' => true],

            ['word' => 'buc', 'partial' => true],
            ['word' => 'frajer', 'partial' => true],
            ['word' => 'szmata', 'partial' => true],
            ['word' => 'dziwka', 'partial' => true],
            ['word' => 'choler', 'partial' => true],   // cholera, cholernie
            ['word' => 'jasna cholera', 'partial' => false],
        ];

       
        foreach ($words as $word) {
            BannedWord::firstOrCreate(
                ['word' => $word['word']],
                ['partial' => $word['partial']]
            );
        }
    }
}