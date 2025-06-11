<?php
namespace App\Livewire\Categories;

use App\Models\Category;
use App\Models\Attraction;
use WireUi\Traits\WireUiActions;
use PowerComponents\LivewirePowerGrid\{Button, Column, PowerGrid, PowerGridComponent, PowerGridFields};

final class CategoryTable extends PowerGridComponent
{
    use WireUiActions;

    public function datasource(): \Illuminate\Database\Eloquent\Builder
    {
        return Category::query();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('created_at_formatted', fn ($model) => $model->created_at->format('Y-m-d'));
    }

    public function columns(): array
    {
        return [
            Column::make('Nazwa', 'name'),
            Column::make('Dodano', 'created_at_formatted'),
            Column::action('Akcje'),
        ];
    }

    public function actions(Category $category): array
    {
        return [
            Button::add('edit')
                ->route('categories.edit', ['category' => $category->id])
                ->slot('<x-wireui-icon name="pencil" class="w-5 h-5" />'),

            Button::add('delete')
                ->slot('<x-wireui-icon name="trash" class="w-5 h-5 text-red-500" />')
                ->tooltip('Usuń')
                ->dispatch('deleteCategory', ['category' => $category->id]),
        ];
    }

    #[\Livewire\Attributes\On('deleteCategory')]
    public function deleteCategory($params)
    {
        $category = Category::findOrFail($params['category']);

        $attractionsWithOnlyThisCategory = $category->attractions()
            ->has('categories', '=', 1)
            ->get();

        if ($attractionsWithOnlyThisCategory->count()) {
            $this->dialog()->warning(
                'Nie można usunąć',
                'Niektóre atrakcje mają tylko tę kategorię. Zmień kategorię przed usunięciem.'
            );
            return;
        }

        $category->attractions()->detach();
        $category->delete();

        $this->notification()->success('Sukces', 'Kategoria została usunięta.');
        $this->dispatch('pg:refresh');
    }
}
