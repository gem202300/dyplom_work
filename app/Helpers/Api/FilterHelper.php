<?php

namespace App\Helpers\Api;

class FilterHelper
{
    public static function extractFilters(): ?array
    {
        if (request()->filter === null || request()->filter === '') {
            return null;
        }
        $result = [];
        foreach (request()->filter as $filteredColumnName => $filterValue) {
            $result[$filteredColumnName] = [
                'value' => $filterValue,
                'matchMode' => 'contains',
            ];
        }

        return $result;
    }
}
