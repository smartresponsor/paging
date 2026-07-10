<?php

declare(strict_types=1);

namespace App\Paging\Controller\Admin;

use App\Paging\Entity\PageAcceptance;
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
final class PageAcceptanceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PageAcceptance::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Page acceptance')
            ->setEntityLabelInPlural('Page acceptance history')
            ->setPageTitle(Crud::PAGE_INDEX, 'Page acceptance history')
            ->setDefaultSort(['acceptedAt' => 'DESC'])
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
        yield TextField::new('subjectUserId');
        yield TextField::new('revisionChecksum')->hideOnIndex();
        yield DateTimeField::new('acceptedAt');
        yield TextField::new('ipHash')->hideOnIndex();
        yield TextField::new('userAgentHash')->hideOnIndex();
    }
}
