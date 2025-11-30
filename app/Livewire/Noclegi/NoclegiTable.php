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
        return Nocleg::query()->with('photos');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('title')
            ->add('city')
            ->add('street')
            ->add('object_type')
            ->add('capacity')
            ->add('contact_phone')
            ->add('amenities', function ($model) {
                $icons = [];
                if ($model->has_kitchen) $icons[] = '🍳';
                if ($model->has_parking) $icons[] = '🅿️';
                if ($model->has_bathroom) $icons[] = '🚿';
                if ($model->has_wifi) $icons[] = '📶';
                if ($model->has_tv) $icons[] = '📺';
                if ($model->has_balcony) $icons[] = '🌅';
                return implode(' ', $icons) ?: '—';
            })
            ->add('photo_column', function ($model) {
                if ($model->photos->isEmpty()) {
                    return '<span class="text-gray-400">brak zdjęcia</span>';
                }
                $photo = $model->photos->first();
                return '<div style="width:150px;height:100px;" class="rounded overflow-hidden bg-gray-100">
                            <img src="'.asset($photo->path).'" class="w-full h-full object-cover"/>
                        </div>';
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Zdjęcie', 'photo_column')->headerAttribute('style="width:160px"'),
            Column::make('Tytuł', 'title')->sortable()->searchable(),
            Column::make('Miasto', 'city')->sortable()->searchable(),
            Column::make('Ulica', 'street')->sortable()->searchable(),
            Column::make('Typ obiektu', 'object_type'),
            Column::make('Miejsca', 'capacity'),
            Column::make('Telefon', 'contact_phone'),
            Column::make('Udogodnienia', 'amenities'),
            Column::action('Opcje'),
        ];
    }

    public function actions(Nocleg $nocleg): array
    {
        return [
            Button::add('show')
                ->route('noclegi.show', ['nocleg' => $nocleg->id])
                ->slot('<x-wireui-icon name="eye" class="w-5 h-5 text-blue-600"/>')
                ->tooltip('Zobacz szczegóły'),

            Button::add('edit')
                ->route('noclegi.edit', ['nocleg' => $nocleg->id])
                ->slot('<x-wireui-icon name="pencil" class="w-5 h-5 text-gray-600"/>')
                ->tooltip('Edytuj'),

            Button::add('delete')
                ->slot('<x-wireui-icon name="trash" class="w-5 h-5 text-red-500"/>')
                ->tooltip('Usuń')
                ->dispatch('deleteNoclegAction', ['id' => $nocleg->id]),
            ];
    }

    #[\Livewire\Attributes\On('deleteNoclegAction')]
public function deleteNoclegAction(array $payload): void
{
    $id = $payload['id'] ?? null;
    if (!$id) return;

    $nocleg = Nocleg::find($id);
    if (!$nocleg) return;

    $this->dialog()->confirm([
        'title' => 'Potwierdź usunięcie',
        'description' => "Czy na pewno chcesz usunąć \"{$nocleg->title}\"?",
        'icon' => 'warning',
        'accept' => [
            'label' => 'Tak',
            'method' => 'destroy',
            'params' => $id,
        ],
        'reject' => [
            'label' => 'Nie',
        ],
    ]);
}


    public function destroy(int $id): void
{
    $nocleg = Nocleg::findOrFail($id);
    $nocleg->delete();

    $this->notification()->success(
        'Usunięto',
        "Nocleg \"{$nocleg->title}\" został usunięty."
    );

    $this->dispatch('pg:refresh');
}

}
