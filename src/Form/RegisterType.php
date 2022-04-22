<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, array(
                'label'=>'Name*'
            ))
            ->add('surname', TextType::class, array(
                'label'=>'Surname*'
            ))
            ->add('address',  TextType::class, array(
                'label'=>'Address',
                'required' => false
            ))
            ->add('city', TextType::class, array(
                'label'=>'City',
                'required' => false
            ))
            ->add('country', TextType::class, array(
                'label'=>'Country',
                'required' => false
            ))
            ->add('email', EmailType::class, array(
                'label'=>'Email*'
            ))
            ->add('phone', TextType::class, array(
                'label'=>'Phone',
                'required' => false
            ))
            ->add('password', PasswordType::class, array(
                'label'=>'Password*'
            ))
    ;}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
