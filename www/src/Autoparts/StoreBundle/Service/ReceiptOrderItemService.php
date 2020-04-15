<?php


namespace App\Autoparts\StoreBundle\Service;


use App\Document\Product;
use App\Autoparts\StoreBundle\Document\ReceiptOrderItem;
use App\Repository\ProductRepository;
use App\Autoparts\StoreBundle\Repository\ReceiptOrderItemRepository;
use App\Repository\SupplierRepository;
use App\Service\OrderItemService;

class ReceiptOrderItemService
{


    /**
     * @var ReceiptOrderItemRepository
     */
    private $receiptOrderItemRepository;
    /**
     * @var OrderItemService
     */
    private $orderItemService;

    public function __construct(ReceiptOrderItemRepository $receiptOrderItemRepository, OrderItemService $orderItemService)
    {

        $this->receiptOrderItemRepository = $receiptOrderItemRepository;
        $this->orderItemService = $orderItemService;
    }



    public function addArray($arFields){
        $receiptOrderItems = [];
        foreach ($arFields as $arField){
            $receiptOrderItem = new ReceiptOrderItem();
            $receiptOrderItem->setOrderItem($arField['orderItem']);
            $receiptOrderItem->setReceipt($arField['receipt']);
            $receiptOrderItem->setQuantity($arField['quantity']);
            $receiptOrderItem->setCreate();

            $receiptOrderItems[] = $receiptOrderItem;
        }
        if(count($receiptOrderItems)>0) {
            return $this->receiptOrderItemRepository->saveArray($receiptOrderItems);
        }
    }
}