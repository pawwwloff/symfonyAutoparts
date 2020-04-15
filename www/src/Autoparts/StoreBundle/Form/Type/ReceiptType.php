<?php


namespace App\Autoparts\StoreBundle\Form\Type;



use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ReceiptType extends AbstractType
{
    // ...

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('receipt', null, $options);

    }
}