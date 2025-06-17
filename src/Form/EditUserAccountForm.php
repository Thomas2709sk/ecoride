<?php

namespace App\Form;

use App\Entity\Carpools;
use App\Entity\Drivers;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserAccountForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('roles')
            // ->add('password')
            ->add('pseudo', TextType::class, [
                'required' => false,
            ])
            ->add('photo', FileType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Photo de profil :',
                'attr' => [  
                        'accept' => 'image/png, image/jpeg, image/webp'       
                ]
            ])
            // ->add('drivers', EntityType::class, [
            //     'class' => Drivers::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('carpools', EntityType::class, [
            //     'class' => Carpools::class,
            //     'choice_label' => 'id',
            //     'multiple' => true,
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
