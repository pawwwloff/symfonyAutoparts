<?php


namespace App\Admin;

use App\Service\SupplierService;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\Filter\ChoiceType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineMongoDBAdminBundle\Datagrid\ProxyQuery;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class OrderItemAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'order';
    protected $baseRoutePattern = 'order';
    /**
     * @var SupplierService
     */
    private $supplierService;

    public function __construct($code, $class, $baseControllerName, SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
        parent::__construct($code, $class, $baseControllerName);

    }

    protected function configureFormFields(FormMapper $formMapper)
    {

    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
        $collection->add('change_price', $this->getRouterIdParameter().'/change_price');
        $collection->add('change_status', $this->getRouterIdParameter().'/change_status');
        $collection->add('change_quantity', $this->getRouterIdParameter().'/change_quantity');
        $collection->add('change_supplier', $this->getRouterIdParameter().'/change_supplier');
        $collection->add('split', $this->getRouterIdParameter().'/split');
    }

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $queryBuilder = $queryz
            ->field('order.$id')->notEqual(null);
        $query = new ProxyQuery($queryBuilder);

        return $query;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
    }


    protected function configureListFields(ListMapper $listMapper)
    {
        $supplpiers = $this->supplierService->list();
        $listMapper
            ->addIdentifier('id')
            ->add('order.id', NumberType::class)
            ->add('number', NumberType::class)
            ->add('status', null, [
                'template' => 'CRUD/orders/list__action_change_status.html.twig'
            ])
            ->add('name')
            ->add('article')
            ->add('vendor')
            ->add('price', null, [
                'template' => 'CRUD/orders/list__action_change_price.html.twig',
            ])
            ->add('quantity', null, [
                'template' => 'CRUD/orders/list__action_change_quantity.html.twig',
            ])
            ->add('supplier.name', null, [
                'suppliers'=>$supplpiers,
                'template' => 'CRUD/orders/list__action_change_supplier.html.twig',
            ])
            ->add('_action', 'actions', [
                'lable'=>'Действия',
                'actions' => [
                    'split' => [
                        'template' => 'CRUD/orders/list__action_split.html.twig',
                    ]
                ]
            ]);
        /** TODO добавить кастомные действия
         * (https://sonata-project.org/bundles/admin/master/doc/cookbook/recipe_custom_action.html)
         * Нам нужны будут - (добавление коментария, лог изменения заказов, при наведении наномер заказа выводить логи фильтры)
         */
    }
}