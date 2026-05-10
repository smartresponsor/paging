<?php

declare(strict_types=1);

namespace App\Paging\Form;

use App\Paging\DTO\Publication\PagePublishInput;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PagePublicationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('effectiveFrom', DateTimeType::class, ['required' => false, 'widget' => 'single_text'])
            ->add('expiresAt', DateTimeType::class, ['required' => false, 'widget' => 'single_text'])
            ->add('publishedByUserId', TextType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PagePublishInput::class,
            'empty_data' => static fn ($form): PagePublishInput => new PagePublishInput(
                $form->get('effectiveFrom')->getData() instanceof \DateTimeImmutable ? $form->get('effectiveFrom')->getData() : null,
                $form->get('expiresAt')->getData() instanceof \DateTimeImmutable ? $form->get('expiresAt')->getData() : null,
                null !== $form->get('publishedByUserId')->getData() ? (string) $form->get('publishedByUserId')->getData() : null,
            ),
        ]);
    }
}
