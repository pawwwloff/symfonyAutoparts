<?php


namespace App\Autoparts\StoreBundle\Admin;

use App\Autoparts\StoreBundle\Document\StoreItem;
use App\Autoparts\StoreBundle\Form\Type\ShipmentType;
use App\Document\OrderItem;
use App\Autoparts\StoreBundle\Document\ReceiptOrderItem;
use App\Document\Supplier;
use App\Autoparts\StoreBundle\Form\Type\ReceiptType;
use App\Document\User;
use App\Repository\OrderItemRepository;
use App\Autoparts\StoreBundle\Repository\ReceiptOrderItemRepository;
use App\Service\OrderItemService;
use App\Autoparts\StoreBundle\Service\ReceiptOrderItemService;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use Sonata\AdminBundle\Form\Type\ModelType;

use Sonata\Form\Type\DatePickerType;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShipmentAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'shipment';
    protected $baseRoutePattern = 'shipment';

   /* public function setStoreItemRepository(OrderItemService $orderItemService)
    {
        $this->orderItemService = $orderItemService;
    }*/

    public function postPersist($object)
    {
        //$this->postOperation($object);
        parent::postPersist($object);
    }

    public function postUpdate($object)
    {
        //$this->postOperation($object);
        parent::postUpdate($object);
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $user = $this->getSubject()->getUser();
        $storeItems = $shipmentItems = [];
        /*if($user){
            $dm = $this->getModelManager()->getDocumentManager(OrderItem::class);
            $repository = new OrderItemRepository($dm);
            $orders = $repository->list(['user'=>$user]);

            $dm = $this->getModelManager()->getDocumentManager(ReceiptOrderItem::class);
            $repository = new ReceiptOrderItemRepository($dm);
            $receipts = $repository->list(['receipt'=>$this->getSubject()]);
        }*/

        $formMapper
            ->with('General', ['class' => 'col-md-12', 'label' => 'Поступления'])->end()
            ->with('Table', ['class' => 'col-md-12 receipt-tables','label' => 'Заказы'])->end()
        ;
        $formMapper
            ->with('General')
                ->add('user', ModelType::class, [
                    'class'   => User::class,
                    'property' => 'username',
                    'required'=>false,
                    'disabled'=>$user?true:false,
                    'attr' => ['class' => 'shipment_select_user'],
                ])
                ->add('create', DatePickerType::class, [
                    'dp_use_current'        => true,
                    'dp_collapse'           => true,
                    'dp_calendar_weeks'     => false,
                    'dp_view_mode'          => 'days',
                    'dp_min_view_mode'      => 'days',])
            ->end()
            ->with('Table')
                ->add('table', ShipmentType::class, [
                    "mapped" => false,
                    "data"=>[
                        "storeItems"=>$storeItems,
                        "shipmentItems"=>$shipmentItems,
                    ]
                ])
            ->end();
        ;

            //->add('product', AdminType::class);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        /*$datagridMapper->add('name')
            ->add('email');*/
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('id')
            ->add('user.username')
            ->add('create');
    }
}