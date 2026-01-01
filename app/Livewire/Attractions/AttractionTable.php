<?php

namespace App\Livewire\Attractions;

use App\Models\Attraction;

use Illuminate\Support\Carbon;
use WireUi\Traits\WireUiActions;
use Illuminate\Support\Facades\Lang;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\{Button, Column, Footer, Header, PowerGrid, PowerGridComponent, PowerGridFields};

final class AttractionTable extends PowerGridComponent
{
    use WireUiActions;

    public function setUp(): array
    {
        return [
            Header::make()->showSearchInput(),
            Footer::make()->showRecordCount(),
        ];
    }

    public function dataSource(): \Illuminate\Database\Eloquent\Builder
    {
        return Attraction::query()
        ->with('photos', 'categories') 
        ->withAvg('ratings', 'rating'); 
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('location')
            ->add('description')
            ->add('opening_hours_combined', function ($model) {
                return $model->opening_time && $model->closing_time
                    ? \Carbon\Carbon::parse($model->opening_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($model->closing_time)->format('H:i')
                    : '—';
            })


            ->add('categories_names', function ($model) {
                return $model->categories->pluck('name')->join(', ');
            })
            ->add('formatted_average_rating', function ($model) {
                return is_numeric($model->ratings_avg_rating)
                    ? number_format((float)$model->ratings_avg_rating, 2)
                    : '—';
            })



            ->add('created_at_formatted', fn ($model) => \Carbon\Carbon::parse($model->created_at)->format('Y-m-d H:i'))
            ->add('photo_column', function ($model) {
                $photos = $model->photos;

                if ($photos->isEmpty()) {
                    return '<span class="text-gray-400">немає фото</span>';
                }

                $photo = $photos->first();

                return '<div style="width: 200px; height: 120px;" class="mx-auto rounded-md overflow-hidden bg-gray-200">
                            <img src="' . asset($photo->path) . '" 
                                style="width: 200px; height: 120px;" 
                                class="object-cover" 
                                alt=""/>
                        </div>';
            })
 ;

    }



    public function columns(): array
    {
        return [
            Column::make(Lang::get('attractions.photo'), 'photo_column')
            ->headerAttribute('style="width:200px"')
            ->bodyAttribute('style="width:130px"'),

            Column::make(__('attractions.name'), 'name'),
            Column::make(__('attractions.location'), 'location'),
            Column::make(__('attractions.description'), 'description'),
            Column::make(__('attractions.hours'), 'opening_hours_combined'),
            Column::make(__('attractions.rating'), 'formatted_average_rating'),

            Column::make('Kategorie', 'categories_names'),
            Column::action(__('attractions.actions')),

                
        ];
    }

    public function actions(Attraction $attraction): array
    {
        return [
            Button::add('showAttraction')
                ->route('attractions.show', ['attraction' => $attraction->id])
                ->slot('<x-wireui-icon name="eye" class="w-5 h-5 text-blue-500" />')
                ->tooltip('Zobacz szczegóły'),
            Button::add('editAttraction')
                ->route('attractions.edit', ['attraction' => $attraction->id])
                ->slot('<x-wireui-icon name="pencil" class="w-5 h-5" />')
                ->tooltip('Edytuj')
                ->class('text-gray-500'),

            Button::add('deleteAttraction')
                ->slot('<x-wireui-icon name="trash" class="w-5 h-5 text-red-500" />')
                ->tooltip('Usuń')
                ->dispatch('deleteAttractionAction', ['attraction' => $attraction->id]),
        ];
    }
    #[\Livewire\Attributes\On('deleteAttractionAction')]
    public function deleteAttractionAction(Attraction $attraction): void
    {
        $this->dialog()->confirm([
            'title' => 'Potwierdź usunięcie',
            'description' => "Czy na pewno chcesz usunąć \"{$attraction->name}\"?",
            'icon' => 'warning',
            'accept' => [
                'label' => 'Tak',
                'method' => 'destroy',
                'params' => $attraction->id,
            ],
            'reject' => [
                'label' => 'Nie',
            ],
        ]);
    }

    public function destroy(int $id): void
    {
        $attraction = Attraction::findOrFail($id);
        $attraction->delete();

        $this->notification()->success(
            'Sukces',
            "Atrakcja \"{$attraction->name}\" została usunięta."
        );

        $this->dispatch('pg:refresh');
    }



}
