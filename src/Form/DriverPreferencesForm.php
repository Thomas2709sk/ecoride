<?php

namespace App\Form;

use App\Entity\Drivers;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DriverPreferencesForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('animals', CheckboxType::class, [
                'label'    => 'oui', 
                'required' => false, 
                'mapped' => true,   
                'value' => 'oui',         
            ])
            ->add('smoking', CheckboxType::class, [
                'label'    => 'oui',
                'required' => false, 
                'mapped' => true,   
                'value' => 'oui',         
            ])
            ->add('preferences')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Drivers::class,
        ]);
    }
}
