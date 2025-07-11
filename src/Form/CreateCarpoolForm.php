<?php

namespace App\Form;

use App\Entity\Carpools;
use App\Entity\Cars;
use App\Entity\Drivers;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Time;

class CreateCarpoolForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $driverCar = $options['driver_car'];

        $builder
            ->add('day', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Jour :',
                'constraints' => [
                    new NotBlank(['message' => 'Le jour est obligatoire.']),
                ],
            ])
            ->add('begin', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Début :',
                'constraints' => [
                    new NotBlank(['message' => 'L\'heure de début est obligatoire.']),
                ],
            ])
            ->add('end', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Fin :',
                'constraints' => [
                    new NotBlank(['message' => 'L\'heure de fin est obligatoire.']),
                  
                ],
            ])
            ->add('address_start', TextType::class, [
                'label' => 'adresse de départ :',
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse de départ est obligatoire.']),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'L\'adresse de départ ne peut pas dépasser {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('address_end', TextType::class, [
                'label' => 'adresse d\'arrivée :',
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse d\'arrivée est obligatoire.']),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'L\'adresse d\'arrivée ne peut pas dépasser {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('duration', HiddenType::class)
            ->add('price', IntegerType::class, [
                'label' => 'Prix',
                'attr' => ['min' => 0],
            ])
            ->add('car', EntityType::class, [
                'class' => Cars::class,
                'label' => 'Véhicule :',
                'choices' => $options['driver_car'],
                'placeholder' => 'Choisissez un véhicule',
                'required' => true,
            ])
            ->add('isEcological', ChoiceType::class, [
                'choices'  => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'label' => 'Trajet écologique ?',
            ])
            ->add('startLat', HiddenType::class)
            ->add('startLon', HiddenType::class)
            ->add('endLat', HiddenType::class)
            ->add('endLon', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Carpools::class,
            'driver_car' => [],
        ]);
    }
}
