<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Notifications\TestNotification;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $user = User::find(103);

        if (!$user) {
            dd("No users found to seed notifications");
        }
        $user->notify(new TestNotification('Witamy', 'Witamy! To jest twoje pierwsze testowe powiadomienie.'));
        $user->notify(new TestNotification('Przypomnienie', 'Drugie testowe powiadomienie.'));
        $user->notify(new TestNotification('Sprawdzenie', 'Trzecie powiadomienie do sprawdzenia listy.'));

    }
}
