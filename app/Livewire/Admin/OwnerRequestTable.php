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
        'rejectRequest' => 'reject',
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
            ->add('user_username', fn($model) => $model->user->username)
            ->add('phone')
            ->add('status')
            ->add('created_at_formatted', fn($model) => $model->created_at->format('Y-m-d H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id'),
            Column::make('Użytkownik', 'user_name')->sortable()->searchable(),
            Column::make('Username', 'user_username')->sortable()->searchable(),
            Column::make('Telefon', 'phone'),
            Column::make('Status', 'status')->sortable(),
            Column::make('Dodano', 'created_at_formatted')->sortable(),
            Column::action('Akcje'),
        ];
    }

    public function actions(OwnerRequest $request): array
{
    $buttons = [];

    $buttons[] = Button::add('view')
        ->route('admin.owner-requests.show', ['owner_request' => $request->id])
        ->slot('<x-wireui-icon name="eye" class="w-5 h-5 text-blue-500" />')
        ->tooltip('Zobacz szczegóły');

    if ($request->status === 'pending') {
        $buttons[] = Button::add('approve')
            ->slot('<button wire:click="approve('.$request->id.')" class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700">✓</button>');

        $buttons[] = Button::add('reject')
            ->slot('<button wire:click="reject('.$request->id.')" class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700">✗</button>');
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

    $this->notification()->success('Sukces', 'Wniosek został zatwierdzony.');
    $this->dispatch('pg:eventRefresh-default'); 
}

public function reject($id)
{
    $request = OwnerRequest::findOrFail($id);
    $request->update(['status' => 'rejected']);

    $request->user->notify(new \App\Notifications\TestNotification(
        'Twoja prośba została odrzucona',
        'Twoja prośba o rolę właściciela została odrzucona przez administratora.'
    ));

    $this->notification()->success('Info', 'Wniosek został odrzucony.');
    $this->dispatch('pg:eventRefresh-default'); 
}

}
