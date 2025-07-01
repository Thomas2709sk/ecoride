<?php

namespace App\Form;

use App\Entity\Cars;
use App\Entity\Drivers;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Range;

class DriverCarForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('brand', TextType::class, [
                'label' => 'Marque :',
                'constraints' => [
                    new NotBlank(['message' => 'La marque est obligatoire.']),
                    new Length([
                        'max' => 30,
                        'maxMessage' => 'La marque ne peut pas dépasser {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('model', TextType::class, [
                'label' => 'Modèle :',
                'constraints' => [
                    new NotBlank(['message' => 'Le modèle est obligatoire.']),
                    new Length([
                        'max' => 30,
                        'maxMessage' => 'Le modèle ne peut pas dépasser {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('color', TextType::class, [
                'label' => 'Couleur :',
                'constraints' => [
                    new NotBlank(['message' => 'La couleur est obligatoire.']),
                    new Length([
                        'max' => 30,
                        'maxMessage' => 'La couleur ne peut pas dépasser {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('seats', IntegerType::class, [
                'label' => 'Nombre de places :',
                'constraints' => [
                    new NotBlank(['message' => 'Le nombre de places est obligatoire.']),
                    new Positive(['message' => 'Le nombre de places doit être positif.']),
                    new Range([
                        'min' => 1,
                        'max' => 9,
                        'notInRangeMessage' => 'Le nombre de places doit être entre {{ min }} et {{ max }}.'
                    ])
                ],
            ])
            ->add('plate_number', TextType::class, [
                'label' => 'Plaque d\'immatriculation :',
                'constraints' => [
                    new NotBlank(['message' => 'La plaque d\'immatriculation est obligatoire.']),
                    new Length([
                        'max' => 15,
                        'maxMessage' => 'La plaque ne peut pas dépasser {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('first_registration', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de première immatriculation :',
                'constraints' => [
                    new NotBlank(['message' => 'La date est obligatoire.']),
                    new LessThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date ne peut pas être dans le futur.'
                    ]),
                ],
            ])
            ->add('energy', ChoiceType::class, [
                'label' => 'Énergie',
                'choices' => [
                    'Essence'    => 'Essence',
                    'Diesel'     => 'Diesel',
                    'Electrique' => 'Electrique',
                    'Hybride'    => 'Hybride',
                    'GPL'        => 'GPL',
                ],
                'placeholder' => 'Choisissez une énergie',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cars::class,
        ]);
    }
}
