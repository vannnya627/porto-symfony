<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\Actions;

use App\Containers\OrderContainer\Data\Entities\Order;
use App\Containers\OrderContainer\Managers\UserClientManager;
use App\Containers\OrderContainer\Tasks\FindOrdersByUserIdWithProductTask;
use App\Ship\Parents\Actions\Action;
use App\Ship\ValueObjects\Email;

final readonly class GetOrdersAction extends Action
{
    public function __construct(
        private UserClientManager $userClientManager,
        private FindOrdersByUserIdWithProductTask $findOrdersByUserIdWithProductTask,
    ) {}

    /**
     * @return list<Order>
     */
    public function run(Email $email): array
    {
        $user = $this->userClientManager->getUserByEmail($email);

        return $this->findOrdersByUserIdWithProductTask->run($user->id);
    }
}
