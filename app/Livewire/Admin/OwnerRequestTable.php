<?php

namespace App\Livewire\Admin;

use App\Models\OwnerRequest;
use WireUi\Traits\WireUiActions;
use PowerComponents\LivewirePowerGrid\{Button, Column, PowerGrid, PowerGridComponent, PowerGridFields};

final class OwnerRequestTable extends PowerGridComponent
{
    use WireUiActions;

    protected $listeners = [
        'approveRequest' => 'approve',
    ];
    public $emitName = 'admin.owner-request-table';
    public function datasource(): \Illuminate\Database\Eloquent\Builder
    {
        return OwnerRequest::with('user')->latest();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('user_name', fn($model) => $model->user->name)
            ->add('user_email', fn($model) => $model->user->email)
            ->add('phone')
            ->add('status')
            ->add('created_at_formatted', fn($model) => $model->created_at->format('Y-m-d H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id'),
            Column::make('Użytkownik', 'user_name')->sortable()->searchable(),
            Column::make('Email', 'user_email')->sortable()->searchable(),
            Column::make('Telefon', 'phone'),
            Column::make('Status', 'status')->sortable(),
            Column::make('Dodano', 'created_at_formatted')->sortable(),
            Column::action('Akcje'),
        ];
    }

public function actions(OwnerRequest $request): array
{
    $buttons = [];

    // Кнопка перегляду (нейтральна, світла)
    $buttons[] = Button::add('view')
        ->route('admin.owner-requests.show', ['owner_request' => $request->id])
        ->slot('<button class="px-2 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 inline-flex items-center justify-center transition">
                    <x-wireui-icon name="eye" class="w-5 h-5"/>
                </button>')
        ->tooltip('Zobacz szczegóły');

    if ($request->status === 'pending') {
        // Кнопка затвердження (зелена, виділена)
        $buttons[] = Button::add('approve')
            ->slot('<button wire:click="approve(' . $request->id . ')" class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700 inline-flex items-center justify-center transition">
                        <x-wireui-icon name="check-circle" class="w-5 h-5"/>
                    </button>')
            ->tooltip('Zatwierdź wniosek');

        // Кнопка відкидання (червона, виділена)
        $buttons[] = Button::add('reject')
            ->slot('<a href="' . route('admin.owner-requests.show', $request->id) . '" class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 inline-flex items-center justify-center transition">
                        <x-wireui-icon name="x-circle" class="w-5 h-5"/>
                    </a>')
            ->tooltip('Odrzuć wniosek');
    }

    return $buttons;
}



    public function approve($id)
    {
        $request = OwnerRequest::findOrFail($id);
        $request->update(['status' => 'approved']);
        $request->user->assignRole('owner');
        $request->user->notify(new \App\Notifications\TestNotification(
            'Twoja prośba została zaakceptowana',
            'Twoja prośba o rolę właściciela została zatwierdzona przez administratora.'
        ));
        $this->dispatch('notification-added');
        $this->notification()->success('Sukces', 'Wniosek został zatwierdzony.');
        $this->dispatch('pg:eventRefresh-default');
    }
}
