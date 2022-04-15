<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('receiverName', TextType::class, array(
            'label'=>'Name*'
        ))
        ->add('receiverSurname', TextType::class, array(
            'label'=>'Surname*'
        ))
        ->add('address',  TextType::class, array(
            'label'=>'Address'
        ))
        ->add('city', TextType::class, array(
            'label'=>'City'
        ))
        ->add('country', TextType::class, array(
            'label'=>'Country'
        ))
        ->add('submit', SubmitType::class, array(
            'label' => 'Confirm Order',
            'attr' => ['class' => 'btn btn-success float-right mt-2']
        ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }

}