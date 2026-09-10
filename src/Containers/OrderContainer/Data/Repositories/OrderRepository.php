<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\Data\Repositories;

use App\Containers\OrderContainer\Data\Entities\Order;
use App\Containers\OrderContainer\Data\Repositories\Interfaces\OrderRepositoryInterface;
use App\Ship\Parents\Repositories\Repository;
use App\Ship\Traits\RepositorySupportTrait;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends Repository<Order>
 */
final class OrderRepository extends Repository implements OrderRepositoryInterface
{
    use RepositorySupportTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @return list<Order>
     */
    public function findOrdersByUserId(int $userId): array
    {
        /** @var list<Order> $result */
        $result = $this->createQueryBuilder('o')
            ->leftJoin('o.orderItems', 'oi')
            ->addSelect('oi')
            ->where('o.userId = :user_id')
            ->setParameter('user_id', $userId)
            ->orderBy('o.id', 'DESC')
            ->getQuery()
            ->getResult();

        return $result;
    }
}
