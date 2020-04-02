<?php


namespace App\Form;


use App\Document\Order;
use App\Document\Files;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use libphonenumber\PhoneNumberFormat;

class FilesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('active', CheckboxType::class, ['required'=>false]);
        $builder->add('email', EmailType::class);
        $builder->add('emailTheme', TextType::class);
        $builder->add('searchFromXls', CheckboxType::class, ['required'=>false]);
        $builder->add('markup', NumberType::class);
        $builder->add('deliveryTime', NumberType::class);
        $builder->add('file', HiddenType::class);
        $builder->add('jsonTable', HiddenType::class);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'=>Files::class,
            'csrf_protection' => false,]);
    }

}