<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Enums\Auth\RoleType;
use Illuminate\Support\Carbon;
use WireUi\Traits\WireUiActions;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class UserTable extends PowerGridComponent
{
    use AuthorizesRequests, WithExport, WireUiActions;

    
    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput(),
            Footer::make()->showPerPage()->showRecordCount(),
        ];
    }

    
    public function dataSource(): Builder
    {
        return User::query()->with('roles');
    }

    public function relationSearch(): array
    {
        return [
            'roles' => ['name'],
        ];
    }

   
    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('email')
            ->add('created_at')
            ->add('created_at_formatted', function ($user) {
                return Carbon::parse($user->created_at)->format('Y-m-d H:i');
            })
            ->add('email_verified_at')
            ->add('email_verified_at_formatted', function ($user) {
                return Carbon::parse($user->email_verified_at)->format('Y-m-d H:i');
            })
            ->add('joined_roles', function ($user) {
                return $user->roles->pluck('name')
                    ->map(function ($roleName) {
                        return __('translation.roles.' . $roleName);
                    })->join(', ');
            });
    }

  
    public function columns(): array
    {
        return [
            Column::make(__('users.attributes.id'), 'id'),
            Column::make(__('users.attributes.name'), 'name')
                ->sortable()
                ->searchable(),
            Column::make(__('users.attributes.email'), 'email')
                ->sortable()
                ->searchable(),
            Column::make(__('users.attributes.phone'), 'phone')
                ->sortable()
                ->searchable(),
            Column::make(__('users.attributes.address'), 'address')
                ->sortable()
                ->searchable(),
            Column::make(__('users.attributes.created_at'), 'created_at_formatted', 'created_at')
                ->sortable()
                ->searchable(),
            Column::make(__('users.attributes.email_verified_at'), 'email_verified_at_formatted', 'email_verified_at')
                ->sortable()
                ->searchable(),
            Column::make(__('users.attributes.roles'), 'joined_roles'),
            Column::action(__('translation.attributes.actions')),
        ];
    }

    
    public function filters(): array
    {
        return [
            Filter::inputText('name')
                ->placeholder(__('translation.placeholder.enter')),
            Filter::inputText('email')
                ->placeholder(__('translation.placeholder.enter')),
            Filter::inputText('phone')
                ->placeholder(__('translation.placeholder.enter')),
            Filter::inputText('address')
                ->placeholder(__('translation.placeholder.enter')),
            Filter::datetimepicker('created_at_formatted', 'created_at'),
            Filter::datetimepicker('email_verified_at_formatted', 'email_verified_at'),
            Filter::multiSelect('joined_roles', 'roles.name')
                ->dataSource(
                    Role::all()->map(function ($role) {
                        $role->translated_name = __('translation.roles.' . $role->name);
                        return $role;
                    })
                )
                ->optionValue('id')
                ->optionLabel('translated_name')
                ->builder(function (Builder $query, $ids) {
                    $query->whereHas('roles', function ($sq) use ($ids) {
                        $sq->whereIn('id', $ids);
                    });
                    return $query;
                }),
        ];
    }

    
    #[\Livewire\Attributes\On('assignAdminRoleAction')]
    public function assignAdminRoleAction($id): void
    {
        $this->authorize('update', Auth::user());
        User::findOrFail($id)->assignRole(RoleType::ADMIN->value);
    }

    #[\Livewire\Attributes\On('removeAdminRoleAction')]
    public function removeAdminRoleAction($id): void
    {
        $this->authorize('update', Auth::user());
        User::findOrFail($id)->removeRole(RoleType::ADMIN->value);
    }

    #[\Livewire\Attributes\On('assignWorkerRoleAction')]
    public function assignWorkerRoleAction($id): void
    {
        $this->authorize('update', Auth::user());
        User::findOrFail($id)->assignRole(RoleType::WORKER->value);
    }

    #[\Livewire\Attributes\On('removeWorkerRoleAction')]
    public function removeWorkerRoleAction($id): void
    {
        $this->authorize('update', Auth::user());
        User::findOrFail($id)->removeRole(RoleType::WORKER->value);
    }

    
    public function actions(User $user): array
    {
        return [
            Button::add('assignAdminRoleAction')
                ->slot('<x-wireui-icon name="shield-check" class="w-5 h-5" mini />')
                ->tooltip(__('users.actions.assign_admin_role'))
                ->class('text-gray-500')
                ->dispatch('assignAdminRoleAction', ['id' => $user->id]),
            Button::add('removeAdminRoleAction')
                ->slot('<x-wireui-icon name="shield-check" class="w-5 h-5" mini />')
                ->tooltip(__('users.actions.remove_admin_role'))
                ->class('text-green-500')
                ->dispatch('removeAdminRoleAction', ['id' => $user->id]),
            Button::add('assignWorkerRoleAction')
                ->slot('<x-wireui-icon name="cube" class="w-5 h-5" mini />')
                ->tooltip(__('users.actions.assign_worker_role'))
                ->class('text-gray-500')
                ->dispatch('assignWorkerRoleAction', ['id' => $user->id]),
            Button::add('removeWorkerRoleAction')
                ->slot('<x-wireui-icon name="cube" class="w-5 h-5" mini />')
                ->tooltip(__('users.actions.remove_worker_role'))
                ->class('text-green-500')
                ->dispatch('removeWorkerRoleAction', ['id' => $user->id]),
            Button::add('deleteUserAction')
                ->slot('<x-wireui-icon name="trash" class="w-5 h-5" mini />')
                ->tooltip(__('users.actions.delete'))
                ->class('text-red-500')
                ->dispatch('deleteUserAction', ['id' => $user->id])
                ->can(auth()->user()->isAdmin()),
        ];
    }

    
    #[\Livewire\Attributes\On('deleteUserAction')]
    public function deleteUserAction($id): void
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, __('users.errors.unauthorized'));
        }

        $user = User::findOrFail($id);

        if (method_exists($user, 'reservations')) {
            $user->reservations()->delete();
        }

        $user->delete();

        $this->notification()->success(
            __('users.messages.success'),
            __('users.messages.user_deleted')
        );

        $this->dispatch('pg:refresh');
    }

   
    public function actionRules($row): array
    {
        return [
            Rule::button('assignAdminRoleAction')
                ->when(fn($user) => $user->isAdmin() || !auth()->user()->hasRole(\App\Enums\Auth\RoleType::ADMIN->value))
                ->hide(),
    
             Rule::button('removeAdminRoleAction')
                ->when(fn($user) => !$user->isAdmin() || $user->id === auth()->id())
                ->hide(),
    
            Rule::button('assignWorkerRoleAction')
                ->when(fn($user) => $user->isWorker() || !auth()->user()->hasRole(\App\Enums\Auth\RoleType::ADMIN->value))
                ->hide(),
    
            Rule::button('removeWorkerRoleAction')
                ->when(fn($user) => !$user->isWorker() || $user->id === auth()->id())
                ->hide(),
    
            Rule::button('deleteUserAction')
                ->when(fn($user) => $user->id === auth()->id() || !auth()->user()->isAdmin())
                ->hide(),
        ];
    }
    
}
