<?php

declare(strict_types=1);

namespace App\Ship\Core\Abstracts\Repositories;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @template T of object
 *
 * @extends ServiceEntityRepository<T>
 */
abstract class Repository extends ServiceEntityRepository {}
