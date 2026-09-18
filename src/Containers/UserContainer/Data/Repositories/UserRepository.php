<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Data\Repositories;

use App\Containers\UserContainer\Data\Entities\User;
use App\Containers\UserContainer\Data\Repositories\Interfaces\UserRepositoryInterface;
use App\Ship\Parents\Repositories\Repository;
use App\Ship\Traits\RepositorySupportTrait;
use App\Ship\ValueObjects\Email;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends Repository<User>
 */
final class UserRepository extends Repository implements PasswordUpgraderInterface, UserRepositoryInterface
{
    use RepositorySupportTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->changePassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function existByEmail(Email $email): bool
    {
        return $this->count(['email.value' => $email->value]) > 0;
    }

    public function findByEmail(Email $email): ?User
    {
        return $this->findOneBy(['email.value' => $email->value]);
    }
}
