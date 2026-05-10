<?php

declare(strict_types=1);

namespace App\Paging\Service\Security;

use App\Paging\ServiceInterface\Security\PageSecuritySubjectResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class PageSecuritySubjectResolver implements PageSecuritySubjectResolverInterface
{
    public function userId(TokenInterface $token): ?string
    {
        $user = $token->getUser();
        if ($user instanceof UserInterface) {
            return $user->getUserIdentifier();
        }

        return is_string($user) && '' !== $user ? $user : null;
    }

    public function roles(TokenInterface $token): array
    {
        return array_values(array_unique($token->getRoleNames()));
    }
}
