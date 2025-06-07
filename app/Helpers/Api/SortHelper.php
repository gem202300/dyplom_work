<?php

namespace App\Helpers\Api;

class SortHelper
{
    public static function extractSorts(): ?array
    {
        if (request()->sort === null) {
            return null;
        }
        $result = [];
        $sorts = explode(',', request()->sort);
        foreach ($sorts as $sort) {
            $result[] = self::extractSort($sort);
        }

        return $result;
    }

    private static function extractSort(string $sort): array
    {
        if (str_starts_with($sort, '-')) {
            return [
                'field' => substr($sort, 1),
                'order' => -1,
            ];
        }

        return [
            'field' => $sort,
            'order' => 1,
        ];
    }
}
