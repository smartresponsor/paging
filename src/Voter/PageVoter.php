<?php

declare(strict_types=1);

namespace App\Paging\Voter;

use App\Paging\DTO\Security\PageGrantCheck;
use App\Paging\Entity\Page;
use App\Paging\Enum\PageGrantType;
use App\Paging\ServiceInterface\Security\PageGrantServiceInterface;
use App\Paging\ServiceInterface\Security\PageSecuritySubjectResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class PageVoter extends Voter
{
    public const VIEW = 'PAGE_VIEW';
    public const EDIT = 'PAGE_EDIT';
    public const PUBLISH = 'PAGE_PUBLISH';
    public const MANAGE = 'PAGE_MANAGE';

    /** @var array<string, PageGrantType> */
    private const GRANT_BY_ATTRIBUTE = [
        self::VIEW => PageGrantType::View,
        self::EDIT => PageGrantType::Edit,
        self::PUBLISH => PageGrantType::Publish,
        self::MANAGE => PageGrantType::Manage,
    ];

    public function __construct(
        private readonly PageGrantServiceInterface $grantService,
        private readonly PageSecuritySubjectResolverInterface $subjectResolver,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Page && array_key_exists($attribute, self::GRANT_BY_ATTRIBUTE);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        if (!$subject instanceof Page) {
            return false;
        }

        return $this->grantService->isGranted(new PageGrantCheck(
            page: $subject,
            grant: self::GRANT_BY_ATTRIBUTE[$attribute],
            userId: $this->subjectResolver->userId($token),
            roles: $this->subjectResolver->roles($token),
        ));
    }
}
