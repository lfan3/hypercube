<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType as TypePasswordType; 

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("firstname")
            ->add("lastname")
            ->add('username')
            ->add('email')
            ->add('password',null, array('help' => 'At least one digit  one lowercase  one uppercase  one special character from !*.@#$%^&(){}[]:;<>,.?/\~_+-=| 6-15 in lengh'))
            ->add('verifPassword', TypePasswordType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
