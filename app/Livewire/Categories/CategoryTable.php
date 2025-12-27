<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use WireUi\Traits\WireUiActions;
use PowerComponents\LivewirePowerGrid\{
    Button,
    Column,
    PowerGrid,
    PowerGridComponent,
    PowerGridFields
};

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
                ->class('cursor-pointer')
                ->dispatch('attemptDeleteCategory', ['categoryId' => $category->id]),
        ];
    }

    #[\Livewire\Attributes\On('attemptDeleteCategory')]
    public function attemptDeleteCategory(int $categoryId): void
    {
        $category = Category::findOrFail($categoryId);

        if (Category::count() === 1) {
            $this->dialog()->warning(
                title: 'Nie można usunąć',
                description: 'To jedyna kategoria w systemie. Nie można jej usunąć.'
            );
            return;
        }

        if ($category->attractions()->has('categories', '=', 1)->exists()) {
            $this->redirect(route('categories.delete-form', $category->id));
            return;
        }

        $this->dialog()->confirm([
            'title'       => 'Czy na pewno chcesz usunąć kategorię?',
            'description' => "Kategoria \"{$category->name}\" zostanie usunięta.",
            'acceptLabel' => 'Tak, usuń',
            'rejectLabel' => 'Anuluj',
            'method'      => 'deleteCategoryConfirmed',
            'params'      => $categoryId,
        ]);
    }

    public function deleteCategoryConfirmed(int $categoryId): void
    {
        $category = Category::findOrFail($categoryId);

        $category->attractions()->detach();
        $category->delete();

        $this->notification()->success(
            title: 'Sukces',
            description: 'Kategoria została usunięta.'
        );

        $this->dispatch('pg:refresh');
    }
}