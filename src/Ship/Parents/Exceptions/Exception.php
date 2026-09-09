<?php

declare(strict_types=1);

namespace App\Ship\Parents\Exceptions;

use App\Ship\Core\Abstracts\Exceptions\Exception as CoreException;
use App\Ship\Exceptions\Interfaces\ApiExceptionInterface;

abstract class Exception extends CoreException implements ApiExceptionInterface {}
