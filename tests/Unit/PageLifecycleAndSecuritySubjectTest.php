<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\Lifecycle\PageLifecyclePolicy;
use App\Paging\Service\Security\PageSecuritySubjectResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Covers stable lifecycle and host-security boundary behavior owned by Paging.
 */
final class PageLifecycleAndSecuritySubjectTest extends TestCase
{
    public function testLifecyclePolicyExposesAndEnforcesCanonicalTransitions(): void
    {
        self::assertTrue(PageLifecyclePolicy::canTransition('draft', 'review'));
        self::assertTrue(PageLifecyclePolicy::canTransition('published', 'published'));
        self::assertFalse(PageLifecyclePolicy::canTransition('archived', 'draft'));
        self::assertSame(['updated', 'withdrawn', 'archived'], PageLifecyclePolicy::allowedTargets('published'));
        self::assertSame([], PageLifecyclePolicy::allowedTargets('unknown'));

        PageLifecyclePolicy::assertCanTransition('review', 'published');
        self::addToAssertionCount(1);
    }

    public function testLifecyclePolicyRejectsInvalidTransition(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Invalid Paging lifecycle transition from "archived" to "published".');

        PageLifecyclePolicy::assertCanTransition('archived', 'published');
    }

    public function testSecuritySubjectResolverReturnsCanonicalUserIdentifierAndUniqueRoles(): void
    {
        $user = $this->createStub(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('vendor-123');

        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);
        $token->method('getRoleNames')->willReturn(['ROLE_EDITOR', 'ROLE_EDITOR', 'ROLE_USER']);

        $resolver = new PageSecuritySubjectResolver();

        self::assertSame('vendor-123', $resolver->userId($token));
        self::assertSame(['ROLE_EDITOR', 'ROLE_USER'], $resolver->roles($token));
    }

    public function testSecuritySubjectResolverReturnsNullForNonUserTokenSubject(): void
    {
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn(null);
        $token->method('getRoleNames')->willReturn([]);

        $resolver = new PageSecuritySubjectResolver();

        self::assertNull($resolver->userId($token));
        self::assertSame([], $resolver->roles($token));
    }
}
