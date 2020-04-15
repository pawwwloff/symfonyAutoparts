<?php

namespace App\Autoparts\StoreBundle\Controller;


use App\Autoparts\StoreBundle\Repository\StoreItemRepository;
use App\Document\OrderItem;
use App\Repository\OrderItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class StoreAdminController extends AbstractController
{
    /**
     * @var OrderItemRepository
     */
    private $orderItemRepository;
    /**
     * @var StoreItemRepository
     */
    private $storeItemRepository;

    /**
     * SearchController constructor.
     * @param OrderItemRepository $orderItemRepository
     * @param StoreItemRepository $storeItemRepository
     */
    public function __construct(OrderItemRepository $orderItemRepository, StoreItemRepository $storeItemRepository)
    {
        $this->orderItemRepository = $orderItemRepository;
        $this->storeItemRepository = $storeItemRepository;
    }

    public function ordersAction(Request $request){
        $supplierId = $request->get('supplier');
        $orders = [];
        if($supplierId){
            $orders = $this->orderItemRepository->list(['supplier.id'=>$supplierId, 'status'=>OrderItem::STATUSES_AT_RECEIPT]);
        }
        $return['html'] = $this->render('form/receipt.html.twig', [
            'data'  => ["orders"=>$orders],
        ])->getContent();
        return $this->json($return);

    }

    public function storeItemsAction(Request $request){
        $userId = $request->get('user');
        $storeItems = [];
        if($userId){
            $storeItems = $this->storeItemRepository->list(['receiptOrderItem.orderItem.id'=>2]);
        }
        dd($storeItems);
        $return['html'] = $this->render('form/shipment.html.twig', [
            'data'  => ["storeItems"=>$storeItems],
        ])->getContent();
        return $this->json($return);

    }


}
