<?php


namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class SupplierAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'supplier';
    protected $baseRoutePattern = 'supplier';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', TextType::class)
            ->add('email', EmailType::class)
            ->add('emailForPrice', EmailType::class)
            ->add('emailTheme', TextType::class)
            ->add('searchFromXls', CheckboxType::class)
            ->add('markup', IntegerType::class)
            ->add('deliveryTime', IntegerType::class)
            ->add('file', TextType::class, ['required'=>false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
    }
}