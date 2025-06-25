<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchCarpoolForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
             ->add('date', DateType::class, [
            'widget' => 'single_text',
            'required' => true,
            'label' => 'Date',

             ])
             ->add('address_start', TextType::class, [
                'label' => 'adresse de départ :',
            ])
            ->add('address_end', TextType::class, [
                'label' => 'adresse d\'arrivée :',
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
