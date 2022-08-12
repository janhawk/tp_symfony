<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'attr' => [
                'maxLength' => 100
            ]
        ])
        ->add('slug', TextType::class, [
            'attr' => [
                'maxLength' => 100
            ]
        ])
        ->add('img1', FileType::class, [
            'required' => false,
            'mapped' => false,
            'help' => 'png, jpg, jpeg, jp2 ou webp - 1 Mo maximum',
            'constraints' => [
                new Image([
                    'maxSize' => '1M',
                    'maxSizeMessage' => 'Le fichier est trop volumineux ({{ size }} {{ suffix }}). Maximum autorisé : {{ limit }} {{ suffix }}.',
                    'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                            'image/jp2',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Merci de sélectionner une image au format {{ types }}.'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'maxLength' => 65535
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
