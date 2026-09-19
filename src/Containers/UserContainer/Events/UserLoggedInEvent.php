<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Events;

use App\Ship\Parents\Events\Event;
use App\Ship\ValueObjects\Email;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('outbox')]
final readonly class UserLoggedInEvent extends Event
{
    public function __construct(
        public Email $email,
    ) {}
}
