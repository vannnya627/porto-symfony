<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Tasks\RefreshToken;

use App\Ship\Parents\Tasks\Task;
use App\Ship\ValueObjects\Email;

final readonly class EnforceRefreshTokensLimitTask extends Task
{
    public function __construct() {}

    public function run(Email $email): void {}
}
