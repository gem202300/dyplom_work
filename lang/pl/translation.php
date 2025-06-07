<?php

use App\Enums\Auth\RoleType;

return [
    'navigation' => [
        'dashboard'        => 'Dashboard',
        'users'            => 'Użytkownicy',
        'events'           => 'Wydarzenia',
        'reservations'     => 'Rejestracje',
        'my_reservations'  => 'Moje rejestracje',
    ],
    'attributes' => [
        'actions'    => 'Akcje',
        'created_at' => 'Utworzono',
        'updated_at' => 'Zaktualizowano',
        'deleted_at' => 'Usunięto',
    ],
    'placeholder' => [
        'enter'  => 'Wprowadź',
        'select' => 'Wybierz',
    ],
    'roles' => [
        RoleType::ADMIN->value  => 'Administrator',
        RoleType::WORKER->value => 'Pracownik',
        RoleType::USER->value   => 'Użytkownik',
    ],
    'messages' => [
        'successes' => [
            'stored_title'  => 'Utworzono',
            'updated_title' => 'Zaktualizowano',
        ],
    ],
];
