<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\Events;

use App\Ship\Parents\Events\Event;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
final readonly class OrderCreatedEvent extends Event
{
    public function __construct(
        public int $userId,
    ) {}
}
