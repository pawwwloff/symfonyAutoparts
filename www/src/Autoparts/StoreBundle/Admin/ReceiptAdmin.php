<?php


namespace App\Autoparts\StoreBundle\Admin;

use App\Autoparts\StoreBundle\Service\StoreItemService;
use App\Document\OrderItem;
use App\Autoparts\StoreBundle\Document\ReceiptOrderItem;
use App\Document\Supplier;
use App\Autoparts\StoreBundle\Form\Type\ReceiptType;
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

final class ReceiptAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'receipt';
    protected $baseRoutePattern = 'receipt';
    /**
     * @var OrderItemService
     */
    private $orderItemService;
    /**
     * @var ReceiptOrderItemService
     */
    private $receiptOrderItemService;
    /**
     * @var OrderItemRepository
     */
    private $orderItemRepository;
    /**
     * @var ReceiptOrderItemRepository
     */
    private $receiptOrderItemRepository;
    /**
     * @var StoreItemService
     */
    private $storeItemService;

    public function setOrderItem(OrderItemService $orderItemService, OrderItemRepository $orderItemRepository)
    {
        $this->orderItemService = $orderItemService;
        $this->orderItemRepository = $orderItemRepository;
    }

    public function setReceiptOrderItem(ReceiptOrderItemService $receiptOrderItemService, ReceiptOrderItemRepository $receiptOrderItemRepository)
    {
        $this->receiptOrderItemService = $receiptOrderItemService;
        $this->receiptOrderItemRepository = $receiptOrderItemRepository;
    }

    public function setStoreItem(StoreItemService $storeItemService)
    {
        $this->storeItemService = $storeItemService;
    }

    public function postPersist($object)
    {
        $this->postOperation($object);
        parent::postPersist($object);
    }

    public function postUpdate($object)
    {
        $this->postOperation($object);
        parent::postUpdate($object);
    }

    protected function postOperation($object){
        $ordersQuantity = $this->getRequest()->get('receipt');
        $arFields = [];
        $receipts = $this->receiptOrderItemRepository->list(['receipt'=>$object]);
        if($receipts) {
            foreach ($receipts as $receipt) {
                $orderItemId = $receipt->getOrderItem()->getId();
                unset($ordersQuantity[$orderItemId]);
            }
        }
        if(count($ordersQuantity)>0){
            $orders = $this->orderItemRepository->createQueryBuilder()
                ->field('id')->in(array_keys($ordersQuantity))
                ->getQuery()
                ->execute();

        }
        foreach ($orders as $order){
            $newQuantity = $ordersQuantity[$order->getId()];
            $oldQuantity = $order->getQuantity();
            if($newQuantity<=0 || $newQuantity>$oldQuantity){
                throw new NotFoundHttpException(sprintf('Неверное значение количества'));
            }
            if($newQuantity==$oldQuantity){
                $result = $order;
            }else {
                $result = $this->orderItemService->splitOrder($order, $newQuantity);
            }
            if(is_array($result) && isset($result['newOrder'])) {
                $arField['orderItem'] = $result['newOrder'];
            }else{
                $arField['orderItem'] = $result;
            }
            $arField['receipt'] = $object;
            $arField['quantity'] = $newQuantity;
            $arFields[] = $arField;
        }
        if(count($arFields)>0){
            $receiptOrderItems = $this->receiptOrderItemService->addArray($arFields);
            $this->storeItemService->addFromReceipt($receiptOrderItems);
            $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
            foreach ($arFields as $field){
                $this->orderItemService->changeStatus($field['orderItem'],$user, OrderItem::STATUS_TO_RECEIPT);
            }
        }
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $supplier = $this->getSubject()->getSupplier();
        $orders = $receipts = [];
        if($supplier){
            $orders = $this->orderItemRepository->createQueryBuilder()
                ->field('supplier')->equals($supplier)
                ->field('status')->in(OrderItem::STATUSES_AT_RECEIPT)
                ->getQuery()
                ->execute()->toArray();
            //$orders = $this->orderItemRepository->list(['supplier'=>$supplier, 'status'=>OrderItem::STATUSES_AT_RECEIPT]);
            $receipts = $this->receiptOrderItemRepository->list(['receipt'=>$this->getSubject()]);
        }

        $formMapper
            ->with('General', ['class' => 'col-md-12', 'label' => 'Поступления'])->end()
            ->with('Table', ['class' => 'col-md-12 receipt-tables','label' => 'Заказы'])->end()
        ;
        $formMapper
            ->with('General')
                ->add('supplier', ModelType::class, [
                    'class'   => Supplier::class,
                    'property' => 'name',
                    'required'=>false,
                    'disabled'=>$supplier?true:false,
                    'attr' => ['class' => 'receipt_select_supplier'],
                ])
                ->add('create', DatePickerType::class, [
                    'dp_use_current'        => true,
                    'dp_collapse'           => true,
                    'dp_calendar_weeks'     => false,
                    'dp_view_mode'          => 'days',
                    'dp_min_view_mode'      => 'days',])
            ->end()
            ->with('Table')
                ->add('table', ReceiptType::class, [
                    "mapped" => false,
                    "data"=>[
                        "orders"=>$orders,
                        "receipts"=>$receipts,
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
            ->add('supplier.name')
            ->add('create');
    }
}