<?php

namespace App\Form;

use App\Entity\Cars;
use App\Entity\Drivers;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DriverCarForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('brand', TextType::class, [
                'label' => 'Marque :',
            ])
            ->add('model', TextType::class, [
                'label' => 'Modèle :',
            ])
            ->add('color', TextType::class, [
                'label' => 'Couleur :',
            ])
            ->add('seats', IntegerType::class, [
                'label' => 'Nombre de places :',
            ])
            ->add('plate_number', TextType::class, [
                'label' => 'Plaque d\'immatriculation :',
            ])
            ->add('first_registration', DateType::class, [
                'widget' => 'single_text', 
                'label' => 'Date de première immatriculation :'
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
