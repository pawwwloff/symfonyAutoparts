<?php


namespace App\Form;


use App\Document\Order;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use libphonenumber\PhoneNumberFormat;

class OrderMakeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('fio');
        $builder->add('email', EmailType::class);
        $builder->add('phone', PhoneNumberType::class, array('default_region' => 'RU', 'format' => PhoneNumberFormat::NATIONAL));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class'=>Order::class]);
    }
}