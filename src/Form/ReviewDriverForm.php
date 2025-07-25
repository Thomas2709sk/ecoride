<?php

namespace App\Form;

use App\Entity\Carpools;
use App\Entity\Drivers;
use App\Entity\Reviews;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReviewDriverForm extends AbstractType
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
            ]);
           
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reviews::class,
        ]);
    }
}
