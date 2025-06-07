<?php

use App\Services\FlashNotifications\FlashMessage;
use App\Services\FlashNotifications\FlashNotificationService;

if (function_exists('flash')) {
    return;
}

function flash(?string $title = null, ?string $description = null, ?string $icon = null)
{
    $flashService = app(FlashNotificationService::class);

    if (is_null($title)) {
        return $flashService;
    }

    $flashService->flash(
        new FlashMessage($title, $description, $icon)
    );

    return $flashService;
}
