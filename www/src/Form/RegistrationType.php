<?php


namespace App\Form;


use App\Document\User;
use FOS\UserBundle\Form\Type\RegistrationFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TelType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('company', TextType::class, [
            'attr' => ['data-type' => User::USER_TYPE_ENTITY],
        ]);
        $builder->add('inn', TextType::class, [
            'attr' => ['data-type' => User::USER_TYPE_ENTITY]
        ]);
        $builder->add('kpp', TextType::class, [
            'attr' => ['data-type' => User::USER_TYPE_ENTITY]
        ]);
        $builder->add('city', TextType::class, [
            'attr' => ['data-type' => User::USER_TYPE_ENTITY]
        ]);
        $builder->add('first_name', TextType::class, [
            'attr' => ['data-type' => User::USER_TYPE_INDIVIDUAL],
            'required'=>false
        ]);
        $builder->add('second_name', TextType::class, [
            'attr' => ['data-type' => User::USER_TYPE_INDIVIDUAL],
            'required'=>false
        ]);
        $builder->add('last_name', TextType::class, [
            'attr' => ['data-type' => User::USER_TYPE_INDIVIDUAL],
            'required'=>false
        ]);
        $builder->add('email');
        $builder->add('phone', TelType::class);
        $builder->remove('username');
        /** TODO добавить валидацию телефона*/
    }

    public function getParent()
    {
        return RegistrationFormType::class;
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }
}