<?php

namespace App\Livewire\Attractions;

use App\Models\Attraction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Lang;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\{Button, Column, Footer, Header, PowerGrid, PowerGridComponent, PowerGridFields};

final class AttractionTable extends PowerGridComponent
{
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
        ->with('photos')
        ->withAvg('ratings', 'rating'); 
    }

    public function fields(): PowerGridFields
{
    return PowerGrid::fields()
        ->add('id')
        ->add('name')
        ->add('location')
        ->add('description')
        ->add('opening_hours')
        ->add('average_rating', function ($model) {
    return $model->ratings_avg_rating
        ? number_format($model->ratings_avg_rating, 2)
        : '—';
})

        ->add('created_at_formatted', fn ($model) => \Carbon\Carbon::parse($model->created_at)->format('Y-m-d H:i'))
        ->add('photo_column', function ($model) {
    $photos = $model->photos;

    if ($photos->isEmpty()) {
        return '<span class="text-gray-400">немає фото</span>';
    }

    $id = 'slider-' . $model->id;
    $output = '<div id="' . $id . '" class="relative w-[96px] h-[96px] mx-auto overflow-hidden rounded-md bg-gray-200">';
    $output .= '<div class="flex transition-transform duration-300 ease-in-out w-full h-full" style="transform: translateX(0);">';

    foreach ($photos as $photo) {
        $output .= '<img src="' . asset($photo->path) . '" 
            class="w-[96px] h-[96px] object-cover flex-shrink-0" alt="Фото атракціону"/>';
    }

    $output .= '</div>';

    if ($photos->count() > 1) {
        $output .= '
            <button type="button" onclick="prevSlide(\'' . $id . '\')" 
                class="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-6 h-6 text-sm bg-white bg-opacity-90 rounded-full shadow flex items-center justify-center hover:bg-opacity-100 transition-all border border-gray-300">
                ←
            </button>
            <button type="button" onclick="nextSlide(\'' . $id . '\')" 
                class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-6 h-6 text-sm bg-white bg-opacity-90 rounded-full shadow flex items-center justify-center hover:bg-opacity-100 transition-all border border-gray-300">
                →
            </button>
        ';
    }

    $output .= '</div>';
    return $output;
})

;

}



    public function columns(): array
{
    return [
        Column::make(Lang::get('attractions.photo'), 'photo_column'),
        Column::make(__('attractions.name'), 'name'),
        Column::make(__('attractions.location'), 'location'),
        Column::make(__('attractions.description'), 'description'),
        Column::make(__('attractions.hours'), 'opening_hours'),
        Column::make(__('attractions.rating'), 'average_rating'),
        Column::action(__('attractions.actions')),

            
    ];
}

public function actions(Attraction $attraction): array
{
    return [
       
    ];
}


}
