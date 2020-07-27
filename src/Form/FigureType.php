<?php

namespace App\Form;

use App\Entity\Figure;
use App\Entity\Picture;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class FigureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Nom de la figure',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 5],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
            ])
            ->add('pictures', CollectionType::class, [
                'entry_type' => FileType::class,           
                'label' => true,
                'allow_add' => true,
                'prototype' => true,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => "Ajouter des images..."
                ]
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => VideoType::class,
                'label' => false,
                'entry_options' => [
                    'attr' => [
                        'class' => 'videoList-box'
                    ]
                ],
                'allow_add' => true,
                'required' => false,
                'prototype' => true,
                'by_reference' => false
            ])

            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}
