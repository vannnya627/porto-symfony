<?php

declare(strict_types=1);

namespace App\Ship\Parents\Repositories;

use App\Ship\Core\Abstracts\Repositories\Repository as CoreRepository;

/**
 * @template T of object
 *
 * @extends CoreRepository<T>
 */
abstract class Repository extends CoreRepository {}
