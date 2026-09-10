<?php

declare(strict_types=1);

namespace App\Ship\Interfaces;

use Symfony\Component\Security\Core\User\UserInterface;

interface AuthUserInterface extends UserInterface
{
    public function getId(): int;
}
