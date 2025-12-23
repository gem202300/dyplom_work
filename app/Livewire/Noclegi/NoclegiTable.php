<?php

namespace App\Livewire\Noclegi;

use App\Models\Nocleg;
use WireUi\Traits\WireUiActions;
use PowerComponents\LivewirePowerGrid\{
    PowerGridComponent,
    PowerGrid,
    PowerGridFields,
    Column,
    Button,
    Header,
    Footer
};

final class NoclegiTable extends PowerGridComponent
{
    use WireUiActions;

    protected $listeners = ['deleteNoclegAction'];

    public function setUp(): array
    {
        return [
            Header::make()->showSearchInput(),
            Footer::make()->showRecordCount()->showPerPage(),
        ];
    }

    public function datasource()
    {
        return Nocleg::query()
            ->with(['photos', 'objectType']);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('title')
            ->add('city')
            ->add('street')
            ->add('object_type', fn ($m) => $m->objectType?->name ?? '—')
            ->add('capacity')
            ->add('contact_phone')
            ->add('amenities', function ($m) {
                return collect([
                    $m->has_kitchen ? '🍳' : null,
                    $m->has_parking ? '🅿️' : null,
                    $m->has_bathroom ? '🚿' : null,
                    $m->has_wifi ? '📶' : null,
                    $m->has_tv ? '📺' : null,
                    $m->has_balcony ? '🌅' : null,
                ])->filter()->implode(' ') ?: '—';
            })
            ->add('photo_column', function ($m) {
                if ($m->photos->isEmpty()) {
                    return '<span class="text-gray-400">brak zdjęcia</span>';
                }

                return '<img src="' . asset($m->photos->first()->path) . '" class="w-36 h-24 object-cover rounded">';
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Zdjęcie', 'photo_column'),
            Column::make('Tytuł', 'title')->searchable()->sortable(),
            Column::make('Miasto', 'city')->sortable(),
            Column::make('Ulica', 'street'),
            Column::make('Typ', 'object_type'),
            Column::make('Miejsca', 'capacity'),
            Column::make('Telefon', 'contact_phone'),
            Column::make('Udogodnienia', 'amenities'),
            Column::action('Opcje'),
        ];
    }

    public function actions(Nocleg $nocleg): array
    {
        return [
            Button::add('show')->route('noclegi.show', $nocleg),
            Button::add('edit')->route('noclegi.edit', $nocleg),
            Button::add('delete')
                ->dispatch('deleteNoclegAction', ['id' => $nocleg->id]),
        ];
    }

    #[\Livewire\Attributes\On('deleteNoclegAction')]
    public function deleteNoclegAction(array $payload): void
    {
        $nocleg = Nocleg::find($payload['id'] ?? null);
        if (!$nocleg) return;

        $this->dialog()->confirm([
            'title' => 'Usuń nocleg',
            'description' => "Czy na pewno usunąć \"{$nocleg->title}\"?",
            'accept' => ['method' => 'destroy', 'params' => $nocleg->id],
        ]);
    }

    public function destroy(int $id): void
    {
        Nocleg::findOrFail($id)->delete();
        $this->notification()->success('Usunięto', 'Nocleg został usunięty');
        $this->dispatch('pg:refresh');
    }
}
