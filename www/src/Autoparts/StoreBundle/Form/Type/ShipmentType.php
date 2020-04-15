<?php


namespace App\Autoparts\StoreBundle\Form\Type;



use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ShipmentType extends AbstractType
{
    // ...

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('shipment', null, $options);

    }
}