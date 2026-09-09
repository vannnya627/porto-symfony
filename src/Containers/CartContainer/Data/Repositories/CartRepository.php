<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Data\Repositories;

use App\Containers\CartContainer\Data\Entities\Cart;
use App\Containers\CartContainer\Data\Repositories\Interfaces\CartRepositoryInterface;
use App\Containers\UserContainer\Data\Entities\User;
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
        return $this->findOneBy(['user' => $userId]);
    }

    public function findCartWithItemsAndProducts(User $user): ?Cart
    {
        /** @var Cart|null $result */
        $result = $this->createQueryBuilder('c')
            ->leftJoin('c.cartItems', 'ci')
            ->addSelect('ci')
            ->leftJoin('ci.product', 'p')
            ->addSelect('p')
            ->where('c.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();

        return $result;
    }
}
