<?php

declare(strict_types=1);

namespace App\Paging\Controller\Admin;

use App\Paging\Entity\PageGrant;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class PageGrantCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PageGrant::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Page grant')
            ->setEntityLabelInPlural('Page grants')
            ->setPageTitle(Crud::PAGE_INDEX, 'Page grants')
            ->setDefaultSort(['createdAt' => 'DESC'])
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
        yield TextField::new('grant');
        yield TextField::new('subjectUserId');
        yield TextField::new('subjectRole');
        yield TextField::new('createdByUserId')->hideOnIndex();
        yield DateTimeField::new('createdAt');
    }
}
