<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Users\UserCollection;
use App\Models\User;
use Spatie\QueryBuilder\QueryBuilder;

class UserController
{
    public function index()
    {
        return new UserCollection(
            QueryBuilder::for(User::class)
                ->allowedSorts(['name', 'email', 'created_at'])
                ->allowedFilters(['name', 'email'])
                ->paginate(
                    request('rows', 10)
                )
                ->appends(request()->query())
        );
    }
}
