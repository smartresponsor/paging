<?php

declare(strict_types=1);

namespace App\Paging\Form;

use App\Paging\DTO\Authoring\PageCreateInputDTO;
use App\Paging\Enum\PageKind;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PageForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class)
            ->add('slug', TextType::class)
            ->add('title', TextType::class)
            ->add('kind', ChoiceType::class, [
                'choices' => array_combine(
                    array_map(static fn (PageKind $kind): string => $kind->value, PageKind::cases()),
                    PageKind::cases(),
                ),
            ])
            ->add('ownerUserId', TextType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PageCreateInputDTO::class,
            'empty_data' => static fn ($form): PageCreateInputDTO => new PageCreateInputDTO(
                (string) $form->get('code')->getData(),
                (string) $form->get('slug')->getData(),
                (string) $form->get('title')->getData(),
                $form->get('kind')->getData() instanceof PageKind ? $form->get('kind')->getData() : PageKind::Page,
                null !== $form->get('ownerUserId')->getData() ? (string) $form->get('ownerUserId')->getData() : null,
            ),
        ]);
    }
}
