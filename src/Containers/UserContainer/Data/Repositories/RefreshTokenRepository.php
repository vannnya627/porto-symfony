<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Data\Repositories;

use App\Containers\UserContainer\Data\Entities\RefreshToken;
use App\Containers\UserContainer\Data\Repositories\Interfaces\RefreshTokenRepositoryInterface;
use App\Ship\Parents\Repositories\Repository;
use App\Ship\Traits\RepositorySupportTrait;
use App\Ship\ValueObjects\Email;
use Doctrine\Persistence\ManagerRegistry;
use DateTimeImmutable;

/**
 * @extends Repository<RefreshToken>
 */
final class RefreshTokenRepository extends Repository implements RefreshTokenRepositoryInterface
{
    use RepositorySupportTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }

    public function findActiveTokensByEmail(Email $email): array
    {
        /** @var list<RefreshToken> $result */
        $result = $this->createQueryBuilder('rt')
            ->andWhere('rt.username = :email')
            ->andWhere('rt.valid >= :now')
            ->setParameter('email', $email->value)
            ->setParameter('now', new DateTimeImmutable())
            ->orderBy('rt.valid', 'DESC')
            ->getQuery()
            ->getResult();

        return $result;
    }
}
