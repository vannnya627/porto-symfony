<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Data\Repositories;

use App\Containers\CartContainer\Data\Entities\Cart;
use App\Containers\CartContainer\Data\Repositories\Interfaces\CartRepositoryInterface;
use App\Ship\Parents\Repositories\Repository;
use App\Ship\Traits\RepositorySupportTrait;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends Repository<Cart>
 */
final class CartRepository extends Repository implements CartRepositoryInterface
{
    use RepositorySupportTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    public function findByUserId(int $userId): ?Cart
    {
        return $this->findOneBy(['userId' => $userId]);
    }

    public function findCartWithItems(int $userId): ?Cart
    {
        /** @var Cart|null $result */
        $result = $this->createQueryBuilder('c')
            ->leftJoin('c.cartItems', 'ci')
            ->addSelect('ci')
            ->where('c.userId = :user_id')
            ->setParameter('user_id', $userId)
            ->getQuery()
            ->getOneOrNullResult();

        return $result;
    }
}
