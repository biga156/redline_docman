<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$now=new DateTime();

        $builder
            ->add('firstName', TextType::class,[
                'label' => 'First Name (*)',
                'required' => true
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last Name (*)',
                'required' => true
            ])
            //->add('username', TextType::class)
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'help' => 'Enter your password',
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Confirm Password'],
            ])
            ->add('email', EmailType::class, [
                'label'=>'Valid email adress (*)',
                'required' => true,
                'help'=>'please enter a valid e-mail. you will receive the user\'s name in this email'
                
            ])
            ->add('birthDate', BirthdayType::class, [
                'label' => 'Birth date (*)',
                'required' => true,
                'placeholder' => [
                    'year' => 'Year',
                    'month' => 'Month',
                    'day' => 'Day',
                ],
            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
