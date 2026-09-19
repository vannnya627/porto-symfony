<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Data\Entities;

use Doctrine\ORM\Mapping as ORM;
use App\Ship\Parents\Entities\RefreshToken as CoreRefreshToken;

#[ORM\Entity]
#[ORM\Table(name: 'refresh_tokens')]
final class RefreshToken extends CoreRefreshToken {}
