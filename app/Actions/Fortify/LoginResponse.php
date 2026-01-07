<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Handle the response after the user logs in.
     */
    public function toResponse($request)
    {
        // Редирект на мапу після логіну
        return redirect()->route('map.index');
    }
}
