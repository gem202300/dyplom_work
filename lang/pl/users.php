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
        'assign_holder_role' => 'Ustaw rolę właściciela',
        'remove_holder_role' => 'Odbierz rolę właściciela',
        'delete'            => 'Usuń użytkownika',
    ],
    'messages' => [
        'success'      => 'Sukces',
        'user_deleted' => 'Użytkownik został pomyślnie usunięty.',
        'update_success' => 'Pomyślnie zaktualizowano.',
    ],
    'errors' => [
        'unauthorized' => 'Brak uprawnień.',
        'cannot_assign_owner_to_unverified' => 'Nie można przyznać roli właściciela',
        'verify_email_first' => 'Konto użytkownika nie jest zweryfikowane. Najpierw zweryfikuj email.',
        'cannot_delete_owner' => 'Nie można usunąć użytkownika',
        'remove_owner_role_or_transfer_noclegi' => 'Użytkownik posiada noclegi. Najpierw usuń rolę właściciela lub przenieś noclegi na innego użytkownika.',
    ],
];
