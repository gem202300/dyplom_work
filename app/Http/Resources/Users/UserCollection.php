<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use App\Helpers\Api\SortHelper;
use App\Helpers\Api\FilterHelper;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    public $collects = UserResource::class;

    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }

    public function with(Request $request): array
    {
        return [
            'meta' => [
                'multi_sort_meta' => SortHelper::extractSorts(),
                'filter' => FilterHelper::extractFilters(),
            ],
        ];
    }
}
