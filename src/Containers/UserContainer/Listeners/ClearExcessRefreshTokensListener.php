<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Listeners;

use App\Containers\UserContainer\Events\UserLoggedInEvent;
use App\Containers\UserContainer\Tasks\RefreshToken\DeleteAndCommitRefreshTokenTask;
use App\Containers\UserContainer\Tasks\RefreshToken\FindActiveRefreshTokensByEmailTask;
use App\Ship\Parents\Listeners\Listener;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ClearExcessRefreshTokensListener extends Listener
{
    public function __construct(
        private FindActiveRefreshTokensByEmailTask $activeRefreshTokensByEmailTask,
        private DeleteAndCommitRefreshTokenTask $deleteAndCommitRefreshTokenTask,
    ) {}

    public function __invoke(UserLoggedInEvent $event): void
    {
        $activeRefreshTokens = $this->activeRefreshTokensByEmailTask->run($event->email);

        if (count($activeRefreshTokens) > 5) {
            $tokensToDelete = array_slice($activeRefreshTokens, 5);

            foreach ($tokensToDelete as $oldToken) {
                $this->deleteAndCommitRefreshTokenTask->run($oldToken);
            }
        }
    }
}
