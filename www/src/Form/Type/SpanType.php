<?php


namespace App\Form\Type;



use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SpanType extends AbstractType
{
    // ...

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

            $builder->add('span', null, $options);

    }
}