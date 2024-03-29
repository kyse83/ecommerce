<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('new_password',RepeatedType::class,[
            'type'=> PasswordType::class,
            'invalid_message'=> 'Le mot de passe et la confirmation doivent être identiques.',
            'label'=> 'Mon nouveau mot de passe',
            'required'=>true,
            'first_options'=> [
                'label'=>'Mon nouveau mot de passe',
                'attr'=>[
                    'placeholder'=>'Merci de saisir votre nouveau mot de passe'
                ] ],
            'second_options'=> [
                'label'=>'confirmez votre nouveau mot de passe',
            'attr'=>[
                'placeholder'=>'Merci de confirmer nouveau votre mot de passe'
            ]]
        ])
            ->add('submit',SubmitType::class,[
                'label'=>"Modifier le mot se passe",
                'attr'=> [
                    'class'=> 'btn-block btn-info'
                ]
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
