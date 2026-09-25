<?php

declare(strict_types=1);

namespace App\Paging\Controller\Admin;

use App\Paging\Entity\PagePublicationEntity as PagePublication;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/** @extends AbstractCrudController<PagePublication> */
#[IsGranted('ROLE_ADMIN')]
final class PagePublicationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PagePublication::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Page publication')
            ->setEntityLabelInPlural('Page publications')
            ->setPageTitle(Crud::PAGE_INDEX, 'Page publications')
            ->setDefaultSort(['publishedAt' => 'DESC'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT, Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('page');
        yield AssociationField::new('revision');
        yield TextField::new('status');
        yield DateTimeField::new('publishedAt');
        yield DateTimeField::new('effectiveFrom');
        yield DateTimeField::new('expiresAt');
        yield TextField::new('publishedByUserId')->hideOnIndex();
    }
}
