<?php

use App\Enums\Auth\RoleType;

return [
    'attributes' => [
        'id'                => 'ID',
        'name'              => 'Nazwisko i imię',
        'email'             => 'Email',
        'email_verified_at' => 'Email zweryfikowano',
        'created_at'        => 'Data rejestracji',
        'roles'             => 'Role',
        'phone'             => 'Telefon',
        'address'           => 'Adres',
    ],
    'actions' => [
        'assign_admin_role' => 'Ustaw rolę administratora',
        'remove_admin_role' => 'Odbierz rolę administratora',
        'assign_worker_role'=> 'Ustaw rolę pracownika',
        'remove_worker_role'=> 'Odbierz rolę pracownika',
        'delete'            => 'Usuń użytkownika',
    ],
    'messages' => [
        'success'      => 'Sukces',
        'user_deleted' => 'Użytkownik został pomyślnie usunięty.',
        'update_success' => 'Pomyślnie zaktualizowano.',
    ],
    'errors' => [
        'unauthorized' => 'Brak uprawnień.',
    ],
];
