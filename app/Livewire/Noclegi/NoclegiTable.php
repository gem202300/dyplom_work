<?php

namespace App\Livewire\Noclegi;

use App\Models\Nocleg;
use WireUi\Traits\WireUiActions;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class NoclegiTable extends PowerGridComponent
{
    use WireUiActions; 

    public function setUp(): array
    {
        return [
            Header::make()->showSearchInput(),
            Footer::make()->showRecordCount()->showPerPage(),
        ];
    }

    public function datasource()
    {
        return Nocleg::query()->with(['photos', 'objectType', 'user']);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('title')
            ->add('city')
            ->add('street')
            ->add('user_name', fn ($m) => $m->user?->name ?? '—')
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
            Column::make('Właściciel', 'user_name'),
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
            Button::add('show')
                ->slot('<x-wireui-icon name="eye" class="w-5 h-5" />')
                ->route('noclegi.show', $nocleg),

            Button::add('edit')
                ->slot('<x-wireui-icon name="pencil" class="w-5 h-5" />')
                ->route('noclegi.edit', $nocleg),

            Button::add('delete')
                ->slot('<x-wireui-icon name="trash" class="w-5 h-5 text-red-600" />')
                ->dispatch('deleteNoclegAction', ['id' => $nocleg->id]),
        ];
    }

    #[\Livewire\Attributes\On('deleteNoclegAction')]
    public function deleteNoclegAction($id): void
    {
        $nocleg = Nocleg::findOrFail($id);

        $this->dialog()->confirm([
            'title'       => __('noclegi.delete.confirm_title'),
            'description' => __('noclegi.delete.confirm_description', ['title' => $nocleg->title]),
            'acceptLabel' => __('noclegi.delete.confirm_accept'),
            'rejectLabel' => __('noclegi.delete.confirm_reject'),
            'method'      => 'confirmDeleteNoclegAdmin',
            'params'      => $id,
        ]);
    }

    public function confirmDeleteNoclegAdmin($id): void
    {
        $nocleg = Nocleg::findOrFail($id);
        $nocleg->delete();

        $this->notification()->success(
            title: __('noclegi.messages.success'),
            description: __('noclegi.messages.deleted', ['title' => $nocleg->title])
        );

        $this->dispatch('pg:refresh');
    }
}