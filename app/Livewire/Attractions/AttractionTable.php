<?php

namespace App\Livewire\Attractions;

use App\Models\Attraction;
use Illuminate\Support\Carbon;
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
        return Attraction::query()->with('photos');
    }

    public function fields(): PowerGridFields
{
    return PowerGrid::fields()
        ->add('id')
        ->add('name')
        ->add('location')
        ->add('description')
        ->add('opening_hours')
        ->add('rating')
        ->add('created_at_formatted', fn ($model) => \Carbon\Carbon::parse($model->created_at)->format('Y-m-d H:i'))
        ->add('photo_column', function ($model) {
    $photos = $model->photos;

    if ($photos->isEmpty()) {
        return '<span class="text-gray-400">немає фото</span>';
    }

    $id = 'slider-' . $model->id;
    $output = '<div id="' . $id . '" class="relative w-24 h-24 mx-auto overflow-hidden rounded-md">';
    $output .= '<div class="flex transition-transform duration-300 ease-in-out" style="width: 100%">';
// Стрілки
if (count($photos) > 1) {
    $output .= '
        <button type="button" onclick="prevSlide(\'' . $id . '\')" class="absolute left-0 top-1/2 -translate-y-1/2 px-1 text-xs bg-white bg-opacity-80 rounded-l">&lt;</button>
        <button type="button" onclick="nextSlide(\'' . $id . '\')" class="absolute right-0 top-1/2 -translate-y-1/2 px-1 text-xs bg-white bg-opacity-80 rounded-r">&gt;</button>
    ';
}

    foreach ($photos as $photo) {
        $output .= '<img src="' . asset($photo->path) . '" class="w-24 h-24 object-cover flex-shrink-0" />';
    }

    $output .= '</div>';

    // Стрілки (абсолютні, працюють з flex)
    $output .= '
        <button type="button" onclick="prevSlide(\'' . $id . '\')" class="absolute left-0 top-1/2 -translate-y-1/2 px-1 text-xs bg-white bg-opacity-80 rounded-l">&lt;</button>
        <button type="button" onclick="nextSlide(\'' . $id . '\')" class="absolute right-0 top-1/2 -translate-y-1/2 px-1 text-xs bg-white bg-opacity-80 rounded-r">&gt;</button>
    ';
    $output .= '</div>';

    return $output;
});
}



    public function columns(): array
{
    return [
        Column::make('Фото', 'photo_column')
            ->sortable(false)
            ->searchable(false)
            ->bodyAttribute('text-center'),

        Column::make('Назва', 'name')->sortable()->searchable(),
        Column::make('Локація', 'location')->sortable()->searchable(),

        Column::make('Короткий опис', 'description')
            ->sortable()
            ->searchable()
            ->bodyAttribute('max-w-xs truncate'), 

        Column::make('Години роботи', 'opening_hours')->sortable(),

        Column::make('Оцінка', 'rating')->sortable(),

        Column::action('Дії'),

            
    ];
}

public function actions(Attraction $attraction): array
{
    return [
       
    ];
}


}
