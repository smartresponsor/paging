<?php

declare(strict_types=1);

namespace App\Paging\Controller\Admin;

use App\Paging\DTO\Authoring\PageCreateInput;
use App\Paging\DTO\Authoring\PageUpdateInput;
use App\Paging\Entity\Page;
use App\Paging\Enum\PageKind;
use App\Paging\ServiceInterface\Authoring\PageDraftServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class PageCrudController extends AbstractCrudController
{
    public function __construct(private readonly PageDraftServiceInterface $pageDraftService)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Page::class;
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

    public function createEntity(string $entityFqcn): Page
    {
        $suffix = (new \DateTimeImmutable())->format('YmdHis');

        return new Page('draft-'.$suffix, 'draft-'.$suffix, 'Draft page '.$suffix, PageKind::Page);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Page) {
            return;
        }

        $this->pageDraftService->createPage(new PageCreateInput(
            $entityInstance->getCode(),
            $entityInstance->getSlug(),
            $entityInstance->getTitle(),
            $entityInstance->getKind(),
            $entityInstance->getOwnerUserId(),
        ));
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Page) {
            return;
        }

        $this->pageDraftService->updatePage($entityInstance, new PageUpdateInput(
            $entityInstance->getTitle(),
            $entityInstance->getSlug(),
            $entityInstance->getOwnerUserId(),
        ));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('code')->setFormTypeOption('disabled', Crud::PAGE_EDIT === $pageName);
        yield TextField::new('slug');
        yield TextField::new('title');
        yield ChoiceField::new('kind')->setChoices(array_combine(
            array_map(static fn (PageKind $kind): string => $kind->value, PageKind::cases()),
            PageKind::cases(),
        ));
        yield TextField::new('status')->hideOnForm();
        yield TextField::new('ownerUserId')->setRequired(false);
        yield DateTimeField::new('createdAt')->hideOnForm();
        yield DateTimeField::new('updatedAt')->hideOnForm();
    }
}
