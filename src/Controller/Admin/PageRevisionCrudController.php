<?php

declare(strict_types=1);

namespace App\Paging\Controller\Admin;

use App\Paging\DTO\Publication\PagePublishInput;
use App\Paging\Entity\PageRevision;
use App\Paging\ServiceInterface\Publication\PagePublicationServiceInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class PageRevisionCrudController extends AbstractCrudController
{
    public function __construct(private readonly PagePublicationServiceInterface $pagePublicationService)
    {
    }

    public static function getEntityFqcn(): string
    {
        return PageRevision::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Page revision')
            ->setEntityLabelInPlural('Page revisions')
            ->setPageTitle(Crud::PAGE_INDEX, 'Page revisions')
            ->setDefaultSort(['createdAt' => 'DESC'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $publish = Action::new('publishRevision', 'Publish')->linkToCrudAction('publishRevision')->displayAsButton();

        return $actions
            ->disable(Action::NEW, Action::EDIT, Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $publish)
            ->add(Crud::PAGE_DETAIL, $publish)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('page');
        yield IntegerField::new('revisionNumber');
        yield TextField::new('title');
        yield TextField::new('checksum')->hideOnIndex();
        yield TextField::new('createdByUserId')->hideOnIndex();
        yield DateTimeField::new('createdAt');
        yield DateTimeField::new('lockedAt');
        yield TextField::new('lockedBy')->hideOnIndex();
    }

    public function publishRevision(AdminContext $context): RedirectResponse
    {
        $revision = $this->resolveRevision($context);
        $this->pagePublicationService->publishRevision($revision, new PagePublishInput());

        return $this->redirectToRoute('page_admin_page_revision_index');
    }

    private function resolveRevision(AdminContext $context): PageRevision
    {
        $entity = $context->getEntity();
        $instance = null === $entity ? null : $entity->getInstance();

        if (!$instance instanceof PageRevision) {
            throw $this->createNotFoundException('Page revision was not resolved for the EasyAdmin action.');
        }

        return $instance;
    }
}
