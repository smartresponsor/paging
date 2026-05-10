<?php

declare(strict_types=1);

namespace App\Paging\Controller\Public;

use App\Paging\Repository\PageRepository;
use App\Paging\ServiceInterface\Rendering\PageRenderServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageViewController extends AbstractController
{
    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly PageRenderServiceInterface $pageRenderService,
    ) {
    }

    #[Route('/page/', name: 'page_public_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('page/index.html.twig');
    }

    #[Route('/page/{slug}', name: 'page_public_view', methods: ['GET'])]
    public function __invoke(string $slug): Response
    {
        $page = $this->pageRepository->findOneBy(['slug' => $slug]);
        if (null === $page || null === $page->getPublishedRevision()) {
            throw $this->createNotFoundException(sprintf('Published page "%s" was not found.', $slug));
        }

        return $this->render('page/view.html.twig', [
            'page' => $page,
            'view' => $this->pageRenderService->renderPublished($page),
        ]);
    }
}
