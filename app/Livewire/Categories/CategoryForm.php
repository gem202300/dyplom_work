<?php

namespace App\Livewire\Categories;

use Livewire\Component;
use App\Models\Category;

class CategoryForm extends Component
{
    public ?Category $category = null;
    public $name = '';

    public function mount(Category $category = null)
    {
        $this->category = $category ?? new Category();

        if ($this->category->exists) {
            $this->name = $this->category->name;
        }
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $this->category->id],
        ];
    }

    public function submit()
    {
        $this->validate();

        $this->category->name = $this->name;
        $this->category->save();

        flash($this->category->wasRecentlyCreated ? 'Dodano kategorię.' : 'Zaktualizowano kategorię.', '', 'success');

        return redirect()->route('categories.index');
    }

    public function render()
    {
        return view('livewire.categories.category-form');
    }
}
