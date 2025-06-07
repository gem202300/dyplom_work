<?php

namespace App\Services\FlashNotifications;

use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;

class FlashNotificationService
{
    const FLASH_KEY = 'notification_flash_message';

    protected Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function hasMessages(): bool
    {
        return $this->session->has(self::FLASH_KEY);
    }

    public function getMessages(): ?Collection
    {
        $flashedMessages = $this->session->get(self::FLASH_KEY);
        if (! $flashedMessages) {
            return null;
        }

        $messages = collect();
        foreach ($flashedMessages as $message) {
            $messages->push(
                new FlashMessage(
                    isset($message['title']) ? $message['title'] : '',
                    isset($message['description']) ? $message['description'] : '',
                    isset($message['icon']) ? $message['icon'] : ''
                )
            );
        }

        return $messages;
    }

    public function flash(FlashMessage $message): void
    {
        $flashedMessages = $this->session->get(self::FLASH_KEY);
        $flashedMessages[] = $message->toArray();
        $this->session->flash(
            self::FLASH_KEY,
            $flashedMessages
        );
    }
}
