<?php

namespace App\Form;

use App\Document\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReviewWebsiteForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('rate',  HiddenType::class, [
            'label' => false,
            ])
        ->add('commentary', TextareaType::class, [
            'label' => 'Votre avis',
            'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre message.']),
                ],
        ])
         ->add('pseudo', TextType::class, [
            'label' => 'Pseudo',
         ])
        
    ;
}

    

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
             'data_class' => Review::class,
            // Configure your form options here
        ]);
    }
}
