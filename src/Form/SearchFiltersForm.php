<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFiltersForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => 'Date',
                'label_attr' => [
                    'class' => 'fw-bold',
                ],

            ])
            ->add('address_start', TextType::class, [
                'label' => 'adresse de départ :',
                'label_attr' => [
                    'class' => 'fw-bold',
                ],
            ])
            ->add('address_end', TextType::class, [
                'label' => 'adresse d\'arrivée :',
                'label_attr' => [
                    'class' => 'fw-bold',
                ],
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Entrez un prix',
                ],
                'label_attr' => [
                    'class' => 'fw-bold',
                ],
            ])
            ->add('isEcological', ChoiceType::class, [
                'label' => 'Trajet écologique ?',
                'label_attr' => [
                    'class' => 'fw-bold',
                ],
                'required' => false,
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                    'Non spécifié' => null,
                ],
                'expanded' => true,
                'multiple' => false,
                'placeholder' => false,
            ])
            ->add('rate', ChoiceType::class, [
                'label' => 'Note',
                'label_attr' => [
                    'class' => 'fw-bold',
                ],
                'required' => false,
                'choices' => [
                    '1 étoile' => 1,
                    '2 étoiles' => 2,
                    '3 étoiles' => 3,
                    '4 étoiles' => 4,
                    '5 étoiles' => 5,
                ],
                'placeholder' => 'Choisissez une note',
                'required' => false,
            ])
            ->add('begin', TimeType::class, [
                'label' => 'Heure de début',
                'label_attr' => [
                    'class' => 'fw-bold',
                ],
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('duration', ChoiceType::class, [
                'label' => 'Durée',
                'label_attr' => [
                    'class' => 'fw-bold',
                ],
                'required' => false,
                'choices' => [
                    'Plus court' => 'plus_court',
                    'Plus long' => 'plus_long',

                ],
                'placeholder' => 'Trier par : (plus court / plus long)',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'method' => 'GET',
        ]);
    }
}
