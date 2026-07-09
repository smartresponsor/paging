<?php

declare(strict_types=1);

namespace App\Paging\DataFixtures;

use App\Paging\Entity\Page;
use App\Paging\Entity\PagePublication;
use App\Paging\Entity\PageRevision;
use App\Paging\Enum\PageKind;
use App\Paging\Enum\PagePublicationStatus;
use App\Paging\Enum\PageStatus;
use App\Paging\ValueObject\PageSlug;
use Doctrine\Persistence\ObjectManager;

final class PagingDemoFixtures
{
    public function load(ObjectManager $manager): void
    {
        foreach ([
            ['home', PageSlug::fromSource('home')->value(), 'Marketplace home', PageKind::Page, '1001'],
            ['collections', PageSlug::fromSource('collections')->value(), 'Featured collections', PageKind::Blog, '1002'],
            ['shipping-returns', PageSlug::fromSource('shipping-returns')->value(), 'Shipping & returns', PageKind::Policy, null],
            ['order-tracking', PageSlug::fromSource('order-tracking')->value(), 'Order tracking', PageKind::Help, '1003'],
        ] as [$code, $slug, $title, $kind, $ownerUserId]) {
            $page = new Page($code, $slug, $title, $kind, $ownerUserId);
            $manager->persist($page);

            $revision1 = new PageRevision(
                $page,
                1,
                $title,
                sprintf('<article><h1>%s</h1><p>%s</p></article>', $title, $this->introParagraph($title)),
                sprintf('%s %s', $title, $this->introSentence()),
                sprintf("# %s\n\n%s", $title, $this->introParagraph($title)),
                ['blocks' => [['type' => 'hero', 'title' => $title], ['type' => 'cta', 'label' => 'Shop now']]],
                'Initial storefront revision',
                $ownerUserId,
            );
            $manager->persist($revision1);
            $page->useCurrentRevision($revision1);

            $revision2 = new PageRevision(
                $page,
                2,
                sprintf('%s Updated', $title),
                sprintf('<article><h1>%s</h1><p>%s</p></article>', $title, $this->updateParagraph($title)),
                sprintf('%s %s', $title, $this->updateSentence()),
                sprintf("# %s Updated\n\n%s", $title, $this->updateParagraph($title)),
                ['blocks' => [['type' => 'content', 'title' => $title], ['type' => 'feature-list', 'items' => ['Free shipping above $75', '30-day returns', 'Buy now, pay later']]]],
                'Second storefront revision',
                $ownerUserId,
            );
            $manager->persist($revision2);
            $page->markPublished($revision2);

            if (PageStatus::Published === $page->getStatus()) {
                $manager->persist(new PagePublication(
                    $page,
                    $revision2,
                    new \DateTimeImmutable('-1 day'),
                    null,
                    $ownerUserId,
                    PagePublicationStatus::Published,
                ));
            }
        }

        $manager->flush();
    }

    private function introParagraph(string $title): string
    {
        return sprintf(
            '%s keeps the storefront focused on product discovery, delivery confidence, and checkout clarity.',
            $title,
        );
    }

    private function introSentence(): string
    {
        return 'It reads like a real commerce landing page with concrete commerce copy.';
    }

    private function updateParagraph(string $title): string
    {
        return sprintf(
            '%s now highlights campaigns, shipping promises, and support coverage with clearer commerce copy.',
            $title,
        );
    }

    private function updateSentence(): string
    {
        return 'Buyers can understand the offer without needing any internal context.';
    }
}
