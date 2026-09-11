<?php

declare(strict_types=1);

namespace App\Paging\Controller\Admin;

use App\Paging\DTO\Authoring\PageCreateInput;
use App\Paging\DTO\Authoring\PageUpdateInput;
use App\Paging\DTO\Revision\PageRevisionCreateInput;
use App\Paging\Entity\Page;
use App\Paging\Enum\PageKind;
use App\Paging\Enum\PageStatus;
use App\Paging\ServiceInterface\Authoring\PageDraftServiceInterface;
use App\Paging\ServiceInterface\Revision\PageRevisionServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/** @extends AbstractCrudController<Page> */
#[AdminRoute(path: '', name: 'page')]
#[IsGranted('ROLE_ADMIN')]
final class PageCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly PageDraftServiceInterface $pageDraftService,
        private readonly PageRevisionServiceInterface $pageRevisionService,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Page::class;
    }

    #[AdminRoute(path: '/index', name: 'index')]
    public function index(AdminContext $context): KeyValueStore|Response
    {
        return parent::index($context);
    }

    #[AdminRoute(path: '/new', name: 'new')]
    public function new(AdminContext $context): KeyValueStore|Response
    {
        return parent::new($context);
    }

    #[AdminRoute(path: '/edit/{entityId}', name: 'edit')]
    public function edit(AdminContext $context): KeyValueStore|Response
    {
        return parent::edit($context);
    }

    #[AdminRoute(path: '/detail/{entityId}', name: 'detail')]
    public function detail(AdminContext $context): KeyValueStore|Response
    {
        return parent::detail($context);
    }

    #[AdminRoute(path: '/delete/{entityId}', name: 'delete')]
    public function delete(AdminContext $context): KeyValueStore|Response
    {
        return parent::delete($context);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Page')
            ->setEntityLabelInPlural('Pages')
            ->setPageTitle(Crud::PAGE_INDEX, 'Pages')
            ->setDefaultSort(['updatedAt' => 'DESC', 'id' => 'DESC'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $revisions = Action::new('revisions', 'Revisions', 'fa fa-code-branch')
            ->linkToUrl(static fn (Page $page): string => '/admin/page/page-revision?filters[page][comparison]=%3D&filters[page][value]='.$page->getId())
            ->asInfoAction();

        return $actions
            ->add(Crud::PAGE_INDEX, $revisions)
            ->add(Crud::PAGE_EDIT, $revisions)
            ->add(Crud::PAGE_DETAIL, $revisions)
        ;
    }

    public function createEntity(string $entityFqcn): Page
    {
        $suffix = (new \DateTimeImmutable())->format('YmdHis');

        return new Page('draft-'.$suffix, 'draft-'.$suffix, 'Draft page '.$suffix, PageKind::Page);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $page = $this->pageDraftService->createPage(new PageCreateInput(
            $entityInstance->getCode(),
            $entityInstance->getSlug(),
            $entityInstance->getTitle(),
            $entityInstance->getKind(),
            $entityInstance->getOwnerUserId(),
        ));
        $this->createRevisionFromForm($page, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->pageDraftService->updatePage($entityInstance, new PageUpdateInput(
            $entityInstance->getTitle(),
            $entityInstance->getSlug(),
            $entityInstance->getOwnerUserId(),
        ));
        $this->createRevisionFromForm($entityInstance, $entityInstance);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('code')->setFormTypeOption('disabled', Crud::PAGE_EDIT === $pageName);
        yield TextField::new('slug');
        yield TextField::new('title');
        yield TextEditorField::new('draftBodyHtml', 'Content')
            ->setNumOfRows(24)
            ->setHelp('Saving creates a new immutable revision.');
        yield TextField::new('draftChangeNote', 'Revision note')
            ->setRequired(false)
            ->setHelp('Describe what changed in this revision.');
        yield ChoiceField::new('kind')->setChoices(array_combine(
            array_map(static fn (PageKind $kind): string => $kind->value, PageKind::cases()),
            PageKind::cases(),
        ));
        yield ChoiceField::new('status')->setChoices(array_combine(
            array_map(static fn (PageStatus $status): string => $status->value, PageStatus::cases()),
            PageStatus::cases(),
        ))->hideOnForm();
        yield TextField::new('ownerUserId')->setRequired(false);
        yield DateTimeField::new('createdAt')->hideOnForm();
        yield DateTimeField::new('updatedAt')->hideOnForm();
    }

    private function createRevisionFromForm(Page $page, Page $formPage): void
    {
        $bodyHtml = trim($formPage->getDraftBodyHtml());
        if ('' === $bodyHtml) {
            return;
        }

        $current = $page->getCurrentRevision();
        if (null !== $current && $current->getBodyHtml() === $bodyHtml && $current->getTitle() === $page->getTitle()) {
            return;
        }

        $this->pageRevisionService->createRevision($page, new PageRevisionCreateInput(
            title: $page->getTitle(),
            bodyHtml: $bodyHtml,
            changeNote: $formPage->getDraftChangeNote(),
        ));
    }
}
