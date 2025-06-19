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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateCarpoolForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $driverCar = $options['driver_car'];

        $builder
            ->add('day', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Jour :'
            ])
            ->add('begin', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Début :'
            ])
            ->add('end', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Fin :'
            ])
            ->add('address_start', TextType::class, [
                'label' => 'adresse de départ :',
            ])
            ->add('address_end', TextType::class, [
                'label' => 'adresse d\'arrivée :',
            ])
            ->add('price', IntegerType::class, [
                'label' => 'Prix',
                'attr' => ['min' => 0],
            ])
            ->add('car', EntityType::class, [
                'class' => Cars::class,
                'label' => 'Véhicule :',
                'choices' => $driverCar,
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
                'label' => 'trajet écologique ?',
            ])
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
