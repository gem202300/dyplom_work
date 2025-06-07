<?php

namespace App\Services\FlashNotifications;

class FlashMessage
{
    public string $title;

    public string $description;

    public string $icon;

    public function __construct(
        string $title,
        string $description,
        string $icon
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->icon = $icon;
    }

    public function toArray()
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'icon' => $this->icon,
        ];
    }
}
