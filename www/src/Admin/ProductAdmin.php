<?php


namespace App\Admin;

use App\Document\Supplier;
use App\Filter\ModelFilterFix;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\DoctrineMongoDBAdminBundle\Datagrid\ProxyQuery;
use Sonata\DoctrineMongoDBAdminBundle\Filter\ModelFilter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class ProductAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'product';
    protected $baseRoutePattern = 'product';

    public function prePersist($object)
    {
        $object->setId();
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', TextType::class)
            ->add('article', TextType::class)
            ->add('vendor', TextType::class)
            //->add('supplier', Refere::class)
            ->add('supplier', ModelType::class, [
                'class'   => Supplier::class,
                'property' => 'name',
                //'class' => Supplier::class,
                //'choice_label' => 'name',
            ])
            ->add('price', NumberType::class)
            ->add('count', IntegerType::class);
    }

    /*public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $queryBuilder = $query
            ->field('supplier.$id')->equals(new \MongoId(3));
        $query = new ProxyQuery($queryBuilder);

        return $query;
    }*/

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        //$datagridMapper->add('name');
        //$datagridMapper->add('article');
        $datagridMapper->add('supplier', ModelFilterFix::class,
            array(
                'field_mapping'=>['simple'=>false],
                'field_name'=>'supplier',
                'show_filter' => true
            ),
            null,
            array(
                'class' => Supplier::class,
            )
        );
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name')
            ->add('article')
            ->add('vendor')
            ->add('price', NumberType::class)
            ->add('count', IntegerType::class)
            ->add('supplier.name');
    }
}