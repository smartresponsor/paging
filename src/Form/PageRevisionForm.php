<?php

declare(strict_types=1);

namespace App\Paging\Form;

use App\Paging\DTO\Revision\PageRevisionCreateInput;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PageRevisionForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('bodyHtml', TextareaType::class)
            ->add('bodyMarkdown', TextareaType::class, ['required' => false])
            ->add('changeNote', TextareaType::class, ['required' => false])
            ->add('createdByUserId', TextType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PageRevisionCreateInput::class,
            'empty_data' => static fn ($form): PageRevisionCreateInput => new PageRevisionCreateInput(
                (string) $form->get('title')->getData(),
                (string) $form->get('bodyHtml')->getData(),
                null,
                null !== $form->get('bodyMarkdown')->getData() ? (string) $form->get('bodyMarkdown')->getData() : null,
                null,
                null !== $form->get('changeNote')->getData() ? (string) $form->get('changeNote')->getData() : null,
                null !== $form->get('createdByUserId')->getData() ? (string) $form->get('createdByUserId')->getData() : null,
            ),
        ]);
    }
}
