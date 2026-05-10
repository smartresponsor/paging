<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

interface PageSecuritySubjectResolverInterface
{
    public function userId(TokenInterface $token): ?string;

    /** @return list<string> */
    public function roles(TokenInterface $token): array;
}
